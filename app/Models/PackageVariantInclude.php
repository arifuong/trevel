<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageVariantInclude extends Model
{
    protected $fillable = [
        'package_variant_id', 'item', 'sort_order',
    ];

    public function variant()
    {
        return $this->belongsTo(PackageVariant::class, 'package_variant_id');
    }
}
