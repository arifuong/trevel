<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory;

    public const PHASE_AWAL = 'awal';
    public const PHASE_KEBERANGKATAN = 'keberangkatan';

    public const CODE_VISA = 'VISA';
    public const CODE_VAKSIN_MENINGITIS = 'VAKSIN_MENINGITIS';
    public const CODE_FOTO_VISA = 'FOTO_VISA';

    protected $fillable = [
        'name',
        'code',
        'phase',
        'is_required',
        'description',
        'allowed_mimes',
        'max_size_kb',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'max_size_kb' => 'integer',
        ];
    }

    public function jamaahDocuments(): HasMany
    {
        return $this->hasMany(JamaahDocument::class);
    }

    public function isDeparturePhase(): bool
    {
        return $this->phase === self::PHASE_KEBERANGKATAN;
    }

    public function isInitialPhase(): bool
    {
        return $this->phase === self::PHASE_AWAL;
    }

    public function scopeDeparturePhase($query)
    {
        return $query->where('phase', self::PHASE_KEBERANGKATAN);
    }

    public function scopeInitialPhase($query)
    {
        return $query->where('phase', self::PHASE_AWAL);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function getIsMandatoryAttribute(): bool
    {
        return (bool) $this->is_required;
    }
}
