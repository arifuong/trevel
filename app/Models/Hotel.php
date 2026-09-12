<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Hotel extends Model
{
    const CITY_MAKKAH = 'makkah';
    const CITY_MADINAH = 'madinah';

    const STATUS_AKTIF = 'aktif';
    const STATUS_NONAKTIF = 'nonaktif';

    const CITY_LABELS = [
        'makkah' => 'Makkah',
        'madinah' => 'Madinah',
    ];

    protected $fillable = [
        'name', 'slug', 'city', 'star_rating', 'description',
        'main_photo', 'address', 'distance_to_haram', 'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Hotel $hotel) {
            if (empty($hotel->slug)) {
                $baseSlug = Str::slug($hotel->name . ' ' . $hotel->city);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $hotel->slug = $slug;
            }
        });
    }

    // --- Relationships ---

    public function photos()
    {
        return $this->hasMany(HotelPhoto::class)->orderBy('sort_order');
    }

    public function mainPhoto()
    {
        return $this->hasOne(HotelPhoto::class)->where('category', 'main');
    }

    public function galleryPhotos()
    {
        return $this->hasMany(HotelPhoto::class)
            ->where('category', '!=', 'main')
            ->orderBy('sort_order');
    }

    public function facilities()
    {
        return $this->belongsToMany(HotelFacility::class, 'hotel_hotel_facility')
            ->withTimestamps()
            ->orderBy('hotel_facilities.sort_order');
    }

    /**
     * Sub-paket yang menggunakan hotel ini sebagai hotel Makkah.
     */
    public function variantsAsMakkah()
    {
        return $this->hasMany(PackageVariant::class, 'hotel_makkah_id');
    }

    /**
     * Sub-paket yang menggunakan hotel ini sebagai hotel Madinah.
     */
    public function variantsAsMadinah()
    {
        return $this->hasMany(PackageVariant::class, 'hotel_madinah_id');
    }

    // --- Accessors ---

    public function getCityLabelAttribute(): string
    {
        return self::CITY_LABELS[$this->city] ?? ucfirst($this->city);
    }

    /**
     * Total sub-paket yang menggunakan hotel ini (Makkah + Madinah).
     */
    public function getUsageCountAttribute(): int
    {
        return $this->variantsAsMakkah()->count() + $this->variantsAsMadinah()->count();
    }

    /**
     * Apakah hotel ini sedang digunakan oleh sub-paket aktif.
     */
    public function getIsInUseAttribute(): bool
    {
        $inMakkah = $this->variantsAsMakkah()
            ->where('status', '!=', PackageVariant::STATUS_NONAKTIF)
            ->exists();
        $inMadinah = $this->variantsAsMadinah()
            ->where('status', '!=', PackageVariant::STATUS_NONAKTIF)
            ->exists();
        return $inMakkah || $inMadinah;
    }

    // --- Scopes ---

    public function scopeMakkah($query)
    {
        return $query->where('city', self::CITY_MAKKAH);
    }

    public function scopeMadinah($query)
    {
        return $query->where('city', self::CITY_MADINAH);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }
}
