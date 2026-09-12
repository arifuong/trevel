<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class PackageVariant extends Model
{
    const STATUS_AKTIF = 'aktif';
    const STATUS_NONAKTIF = 'nonaktif';
    const STATUS_SOLD_OUT = 'sold_out';

    protected $fillable = [
        'package_id', 'name', 'slug', 'description', 'main_photo',
        'quota', 'status', 'sort_order',
        'hotel_makkah_id', 'hotel_madinah_id',
        'has_include_override', 'has_exclude_override',
    ];

    protected $casts = [
        'quota' => 'integer',
        'sort_order' => 'integer',
        'has_include_override' => 'boolean',
        'has_exclude_override' => 'boolean',
    ];

    protected $appends = ['lowest_price', 'lowest_price_formatted', 'is_sold_out', 'remaining_quota', 'status_label'];

    // Boot: auto-generate slug
    protected static function booted(): void
    {
        static::creating(function (PackageVariant $variant) {
            if (empty($variant->slug)) {
                $package = $variant->package ?? Package::find($variant->package_id);
                $baseSlug = Str::slug(($package ? $package->name . ' ' : '') . $variant->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $variant->slug = $slug;
            }
        });
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function package() { return $this->belongsTo(Package::class); }
    public function prices() { return $this->hasMany(PackageVariantPrice::class); }
    public function registrations() { return $this->hasMany(Registration::class); }

    /**
     * Hotel Makkah dari master data.
     */
    public function hotelMakkah()
    {
        return $this->belongsTo(Hotel::class, 'hotel_makkah_id');
    }

    /**
     * Hotel Madinah dari master data.
     */
    public function hotelMadinah()
    {
        return $this->belongsTo(Hotel::class, 'hotel_madinah_id');
    }

    /**
     * Maskapai (many-to-many via pivot airline_package_variant).
     */
    public function airlines()
    {
        return $this->belongsToMany(Airline::class, 'airline_package_variant')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * Override include items khusus tier ini (opsional, di atas default induk).
     */
    public function overrideIncludes()
    {
        return $this->hasMany(PackageVariantInclude::class)->orderBy('sort_order');
    }

    /**
     * Override exclude items khusus tier ini (opsional, di atas default induk).
     */
    public function overrideExcludes()
    {
        return $this->hasMany(PackageVariantExclude::class)->orderBy('sort_order');
    }

    // =============================================
    // COMPUTED: MERGED INCLUDE/EXCLUDE
    // =============================================

    /**
     * Gabungan daftar include: dari paket induk + override tier (jika ada).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMergedIncludesAttribute()
    {
        $parentIncludes = $this->package ? $this->package->includes : collect();

        if ($this->has_include_override) {
            $overrides = $this->relationLoaded('overrideIncludes')
                ? $this->overrideIncludes
                : $this->overrideIncludes()->get();
            return $parentIncludes->merge($overrides);
        }

        return $parentIncludes;
    }

    /**
     * Gabungan daftar exclude: dari paket induk + override tier (jika ada).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMergedExcludesAttribute()
    {
        $parentExcludes = $this->package ? $this->package->excludes : collect();

        if ($this->has_exclude_override) {
            $overrides = $this->relationLoaded('overrideExcludes')
                ? $this->overrideExcludes
                : $this->overrideExcludes()->get();
            return $parentExcludes->merge($overrides);
        }

        return $parentExcludes;
    }

    /**
     * Nama maskapai digabung dengan separator "/", contoh: "Saudia / Qatar Airways"
     */
    public function getAirlinesDisplayAttribute(): string
    {
        $airlines = $this->relationLoaded('airlines')
            ? $this->airlines
            : $this->airlines()->get();

        return $airlines->pluck('name')->implode(' / ');
    }

    // =============================================
    // PRICING ACCESSORS (unchanged)
    // =============================================

    protected function lowestPrice(): Attribute
    {
        return Attribute::get(function () {
            $prices = $this->relationLoaded('prices')
                ? $this->prices->filter(fn($p) => $p->is_active && (float) $p->normal_price > 0)
                : $this->prices()->where('is_active', true)->where('normal_price', '>', 0)->get();

            if ($prices->isEmpty()) return null;

            return $prices->min(function ($p) {
                return ($p->promo_price && (float) $p->promo_price > 0) ? (float) $p->promo_price : (float) $p->normal_price;
            });
        });
    }

    protected function lowestPriceFormatted(): Attribute
    {
        return Attribute::get(function () {
            $price = $this->lowest_price;
            return $price ? 'Rp ' . number_format((float) $price, 0, ',', '.') : '-';
        });
    }

    // =============================================
    // QUOTA ACCESSORS (unchanged)
    // =============================================

    /**
     * Hitung jumlah jamaah aktif pada sub-paket ini (tidak termasuk yang dibatalkan / approved cancellation).
     */
    public function getActivePaxCount(?int $ignoreRegistrationId = null): int
    {
        return (int) RegistrationMember::whereHas('registration', function ($q) use ($ignoreRegistrationId) {
            $q->where('package_variant_id', $this->id)
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
     * Hitung sisa kuota sub-paket secara dinamis: kuota asli - jamaah aktif.
     */
    public function getRemainingQuota(?int $ignoreRegistrationId = null): int
    {
        $activePax = $this->getActivePaxCount($ignoreRegistrationId);
        return max(0, (int) $this->quota - $activePax);
    }

    protected function remainingQuota(): Attribute
    {
        return Attribute::get(fn () => $this->getRemainingQuota());
    }

    protected function isSoldOut(): Attribute
    {
        return Attribute::get(fn () => $this->status === self::STATUS_SOLD_OUT || $this->getRemainingQuota() <= 0);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(function () {
            if ($this->status === self::STATUS_NONAKTIF) return 'Nonaktif';
            $rem = $this->getRemainingQuota();
            if ($this->status === self::STATUS_SOLD_OUT || $rem <= 0) return 'Penuh / Habis';
            if ($rem <= 5) return 'Hampir Penuh';
            return 'Tersedia';
        });
    }
}
