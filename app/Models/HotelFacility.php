<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelFacility extends Model
{
    protected $fillable = [
        'name', 'sort_order',
    ];

    public function variants()
    {
        return $this->belongsToMany(PackageVariant::class, 'package_variant_hotel_facilities')
            ->withPivot('hotel_type')
            ->withTimestamps();
    }
}
