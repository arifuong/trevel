<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelFacility extends Model
{
    protected $fillable = [
        'name', 'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Hotels yang memiliki fasilitas ini (many-to-many via hotel_hotel_facility).
     */
    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_hotel_facility')
            ->withTimestamps();
    }
}
