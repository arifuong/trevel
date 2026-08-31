<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PackageVariantPrice extends Model
{
    const ROOM_QUAD = 'quad';
    const ROOM_TRIPLE = 'triple';
    const ROOM_DOUBLE = 'double';

    const ROOM_LABELS = [
        'quad' => 'Kuad / Quad',
        'triple' => 'Triple',
        'double' => 'Double',
    ];

    protected $fillable = [
        'package_variant_id', 'room_type', 'normal_price', 'promo_price', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'normal_price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function variant() { return $this->belongsTo(PackageVariant::class, 'package_variant_id'); }

    protected function roomLabel(): Attribute
    {
        return Attribute::get(fn () => self::ROOM_LABELS[$this->room_type] ?? ucfirst($this->room_type));
    }

    protected function normalPriceFormatted(): Attribute
    {
        return Attribute::get(fn () => 'Rp ' . number_format((float) $this->normal_price, 0, ',', '.'));
    }

    protected function promoPriceFormatted(): Attribute
    {
        return Attribute::get(function () {
            return $this->promo_price ? 'Rp ' . number_format((float) $this->promo_price, 0, ',', '.') : null;
        });
    }

    // Get effective price (promo if available, otherwise normal)
    protected function effectivePrice(): Attribute
    {
        return Attribute::get(fn () => $this->promo_price ?? $this->normal_price);
    }

    protected function effectivePriceFormatted(): Attribute
    {
        return Attribute::get(fn () => 'Rp ' . number_format((float) $this->effective_price, 0, ',', '.'));
    }
}
