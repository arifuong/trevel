<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageExclude extends Model
{
    protected $fillable = [
        'package_id', 'item', 'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
