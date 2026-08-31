<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageVariantHotelPhoto extends Model
{
    protected $fillable = [
        'package_variant_id', 'hotel_type', 'photo_path', 'category', 'caption', 'sort_order',
    ];

    public function variant()
    {
        return $this->belongsTo(PackageVariant::class, 'package_variant_id');
    }
}
