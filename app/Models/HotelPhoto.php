<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelPhoto extends Model
{
    const CATEGORY_MAIN = 'main';
    const CATEGORY_EXTERIOR = 'exterior';
    const CATEGORY_ROOM = 'room';
    const CATEGORY_RESTAURANT = 'restaurant';
    const CATEGORY_FACILITY = 'facility';
    const CATEGORY_GALLERY = 'gallery';

    const CATEGORY_LABELS = [
        'main' => 'Foto Utama',
        'exterior' => 'Eksterior',
        'room' => 'Kamar / Interior',
        'restaurant' => 'Restoran / Dining',
        'facility' => 'Fasilitas',
        'gallery' => 'Galeri',
    ];

    protected $fillable = [
        'hotel_id', 'photo_path', 'category', 'caption', 'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_LABELS[$this->category] ?? ucfirst($this->category);
    }
}
