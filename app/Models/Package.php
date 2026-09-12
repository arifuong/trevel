<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';
    public const STATUS_SOLD_OUT = 'sold_out';

    /**
     * PRD Section 9 - Data Paket:
     * Nama paket, harga, tanggal keberangkatan, durasi,
     * fasilitas, kuota kursi, status (aktif/sold out)
     */
    protected $fillable = [
        'name',
        'slug',
        'price',
        'departure_date',
        'duration',
        'facilities',
        'quota',
        'status',
        'package_type',
        'category_label',
        'description',
        'main_photo',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'departure_date' => 'date',
        'quota' => 'integer',
        'duration' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Package $package) {
            if (empty($package->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($package->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $package->slug = $slug;
            }
        });
    }

    protected $appends = [
        'price_formatted',
        'is_sold_out',
        'lowest_price_formatted',
        'remaining_quota',
        'total_quota',
        'status_label',
        'type_label',
    ];

    /**
     * Label kategori paket untuk badge di kartu & halaman detail.
     * Prioritas: category_label (custom dari admin) > auto-generate dari package_type.
     */
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!empty($this->category_label)) {
                    return $this->category_label;
                }

                $name = trim($this->name ?? '');
                // Bersihkan awalan "Paket" bila ada
                $cleanName = trim(preg_replace('/^paket\s+/i', '', $name));

                // Ekstrak kategori jika diawali Umrah/Umroh atau Haji
                if (preg_match('/^(umr[ao]h|haji)\s+([a-z0-9\s\-]+)/i', $cleanName, $matches)) {
                    $prefix = strtolower($matches[1]) === 'umroh' ? 'Umrah' : \Illuminate\Support\Str::title($matches[1]);
                    // Bersihkan embel-embel durasi atau tahun seperti "12 Hari", "1447H", "2026" di ujung nama
                    $suffix = preg_replace('/\s+(\d+\s*(hari|day[s]?)|\d{4}h?)$/i', '', trim($matches[2]));
                    if (!empty($suffix)) {
                        return $prefix . ' ' . \Illuminate\Support\Str::title($suffix);
                    }
                }

                return ($this->package_type ?? 'umrah') === 'haji' ? 'Haji Khusus' : 'Umrah Reguler';
            }
        );
    }

    /**
     * Helper accessor untuk harga terformat rupiah.
     */
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) $this->price, 0, ',', '.')
        );
    }

    /**
     * Hitung total kapasitas kuota (jika ada varian, jumlahkan kuota varian aktif; jika tidak ada, gunakan kuota paket).
     */
    public function getTotalQuota(): int
    {
        $activeVariants = $this->relationLoaded('variants')
            ? $this->variants->where('status', '!=', PackageVariant::STATUS_NONAKTIF)
            : $this->variants()->where('status', '!=', PackageVariant::STATUS_NONAKTIF)->get();

        if ($activeVariants->isNotEmpty()) {
            return (int) $activeVariants->sum('quota');
        }

        return (int) $this->quota;
    }

    protected function totalQuota(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getTotalQuota()
        );
    }

    /**
     * Hitung jumlah jamaah aktif (yang tidak dibatalkan / cancelled).
     */
    public function getActivePaxCount(?int $ignoreRegistrationId = null): int
    {
        return (int) RegistrationMember::whereHas('registration', function ($q) use ($ignoreRegistrationId) {
            $q->where('package_id', $this->id)
              ->where('status', '!=', Registration::STATUS_DIBATALKAN)
              ->where(function ($cq) {
                  $cq->whereNull('cancellation_status')
                     ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
              });
            if ($ignoreRegistrationId) {
                $q->where('id', '!=', $ignoreRegistrationId);
            }
        })->count();
    }

    /**
     * Hitung sisa kuota paket secara dinamis: total kuota - jamaah aktif.
     */
    public function getRemainingQuota(?int $ignoreRegistrationId = null): int
    {
        $activeVariants = $this->relationLoaded('variants')
            ? $this->variants->where('status', '!=', PackageVariant::STATUS_NONAKTIF)
            : $this->variants()->where('status', '!=', PackageVariant::STATUS_NONAKTIF)->get();

        if ($activeVariants->isNotEmpty()) {
            return (int) $activeVariants->sum(fn ($v) => $v->getRemainingQuota($ignoreRegistrationId));
        }

        $activePax = $this->getActivePaxCount($ignoreRegistrationId);
        return max(0, (int) $this->quota - $activePax);
    }

    protected function remainingQuota(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getRemainingQuota()
        );
    }

    /**
     * Cek apakah paket sudah habis kuota / berstatus sold out.
     */
    protected function isSoldOut(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === self::STATUS_SOLD_OUT || $this->getRemainingQuota() <= 0
        );
    }

    /**
     * Label status ketersediaan paket.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status === 'nonaktif') return 'Tidak Aktif';
                $rem = $this->getRemainingQuota();
                if ($this->status === self::STATUS_SOLD_OUT || $rem <= 0) return 'Penuh / Habis';
                if ($rem <= 5) return 'Hampir Penuh';
                return 'Tersedia';
            }
        );
    }

    /**
     * Fasilitas dalam bentuk array (dipisah per baris).
     */
    public function getFacilitiesArrayAttribute(): array
    {
        if (empty($this->facilities)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $this->facilities)))));
    }

    /**
     * Satu paket bisa punya banyak pendaftaran.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(PackageVariant::class);
    }

    /**
     * Daftar item "Biaya Termasuk" (Include) di level paket induk.
     * Berlaku default untuk semua sub-paket, kecuali ada override.
     */
    public function includes(): HasMany
    {
        return $this->hasMany(PackageInclude::class)->orderBy('sort_order');
    }

    /**
     * Daftar item "Biaya Tidak Termasuk" (Exclude) di level paket induk.
     * Berlaku default untuk semua sub-paket, kecuali ada override.
     */
    public function excludes(): HasMany
    {
        return $this->hasMany(PackageExclude::class)->orderBy('sort_order');
    }

    public function getLowestPriceAttribute()
    {
        $activeVariants = $this->relationLoaded('variants')
            ? $this->variants->where('status', 'aktif')
            : $this->variants()->where('status', 'aktif')->with(['prices' => function($q) {
                $q->where('is_active', true)->where('normal_price', '>', 0);
            }])->get();

        if ($activeVariants->isEmpty()) {
            return null;
        }

        $lowestPrice = null;

        foreach ($activeVariants as $variant) {
            $prices = $variant->relationLoaded('prices')
                ? $variant->prices->filter(fn($p) => $p->is_active && (float) $p->normal_price > 0)
                : $variant->prices()->where('is_active', true)->where('normal_price', '>', 0)->get();

            foreach ($prices as $price) {
                $effectivePrice = ($price->promo_price && (float) $price->promo_price > 0) ? (float) $price->promo_price : (float) $price->normal_price;
                if ($effectivePrice > 0 && ($lowestPrice === null || $effectivePrice < $lowestPrice)) {
                    $lowestPrice = $effectivePrice;
                }
            }
        }

        return $lowestPrice;
    }

    public function getLowestPriceFormattedAttribute()
    {
        $price = $this->lowest_price;
        return $price ? 'Rp ' . number_format((float) $price, 0, ',', '.') : '-';
    }
}
