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
        'airline_departure', 'airline_departure_logo', 'airline_return', 'airline_return_logo',
        'hotel_makkah_name', 'hotel_makkah_star', 'hotel_makkah_description',
        'hotel_madinah_name', 'hotel_madinah_star', 'hotel_madinah_description',
    ];

    protected $casts = [
        'quota' => 'integer',
        'sort_order' => 'integer',
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

    // Relationships
    public function package() { return $this->belongsTo(Package::class); }
    public function prices() { return $this->hasMany(PackageVariantPrice::class); }
    public function hotelPhotos() { return $this->hasMany(PackageVariantHotelPhoto::class); }
    public function includes() { return $this->hasMany(PackageVariantInclude::class)->orderBy('sort_order'); }
    public function excludes() { return $this->hasMany(PackageVariantExclude::class)->orderBy('sort_order'); }
    public function registrations() { return $this->hasMany(Registration::class); }
    
    public function hotelFacilities()
    {
        return $this->belongsToMany(HotelFacility::class, 'package_variant_hotel_facilities')
            ->withPivot('hotel_type')
            ->withTimestamps();
    }
    
    public function makkahFacilities()
    {
        return $this->belongsToMany(HotelFacility::class, 'package_variant_hotel_facilities')
            ->wherePivot('hotel_type', 'makkah')
            ->withTimestamps();
    }
    
    public function madinahFacilities()
    {
        return $this->belongsToMany(HotelFacility::class, 'package_variant_hotel_facilities')
            ->wherePivot('hotel_type', 'madinah')
            ->withTimestamps();
    }

    public function makkahPhotos()
    {
        return $this->hotelPhotos()->where('hotel_type', 'makkah')->orderBy('sort_order');
    }

    public function madinahPhotos()
    {
        return $this->hotelPhotos()->where('hotel_type', 'madinah')->orderBy('sort_order');
    }

    // Accessors
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
