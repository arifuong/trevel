<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    public const ACTION_MARK_COMPLETED_INDIVIDUAL = 'mark_completed_individual';
    public const ACTION_MARK_COMPLETED_BULK = 'mark_completed_bulk';
    public const ACTION_REGISTRATION_COMPLETED = self::ACTION_MARK_COMPLETED_INDIVIDUAL;
    public const ACTION_DEPARTURE_COMPLETED = self::ACTION_MARK_COMPLETED_BULK;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Helper untuk mencatat entri audit log baru secara seragam.
     */
    public static function record(
        string $action,
        string $description,
        ?Model $auditable = null,
        ?User $user = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): self {
        return self::create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->getKey(),
            'description' => $description,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }
}
