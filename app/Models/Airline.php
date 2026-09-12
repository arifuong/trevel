<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Airline extends Model
{
    const STATUS_AKTIF = 'aktif';
    const STATUS_NONAKTIF = 'nonaktif';

    protected $fillable = [
        'name', 'slug', 'logo', 'code', 'status', 'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Airline $airline) {
            if (empty($airline->slug)) {
                $baseSlug = Str::slug($airline->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $airline->slug = $slug;
            }
        });
    }

    // --- Relationships ---

    /**
     * Sub-paket (variants) yang menggunakan maskapai ini (many-to-many).
     */
    public function variants()
    {
        return $this->belongsToMany(PackageVariant::class, 'airline_package_variant')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    // --- Accessors ---

    /**
     * Nama lengkap dengan kode IATA, contoh: "Saudia Airlines (SV)"
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->code
            ? "{$this->name} ({$this->code})"
            : $this->name;
    }

    /**
     * Total sub-paket yang menggunakan maskapai ini.
     */
    public function getUsageCountAttribute(): int
    {
        return $this->variants()->count();
    }

    /**
     * Apakah maskapai ini sedang digunakan oleh sub-paket aktif.
     */
    public function getIsInUseAttribute(): bool
    {
        return $this->variants()
            ->where('package_variants.status', '!=', PackageVariant::STATUS_NONAKTIF)
            ->exists();
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }
}
