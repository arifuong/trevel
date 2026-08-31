<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationCancellation extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'registration_id',
        'user_id',
        'reason',
        'fee_amount',
        'fee_percentage',
        'refund_amount',
        'category',
        'status',
        'requested_at',
        'processed_by',
        'processed_at',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'fee_percentage' => 'integer',
        'refund_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'fee_amount_formatted',
        'refund_amount_formatted',
    ];

    /**
     * Label status pembatalan dalam Bahasa Indonesia.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                self::STATUS_PENDING => 'Menunggu Validasi',
                self::STATUS_APPROVED => 'Disetujui',
                self::STATUS_REJECTED => 'Ditolak',
                default => ucwords(str_replace('_', ' ', $this->status)),
            }
        );
    }

    /**
     * Format Rupiah untuk biaya pembatalan.
     */
    protected function feeAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) $this->fee_amount, 0, ',', '.')
        );
    }

    /**
     * Format Rupiah untuk dana refund.
     */
    protected function refundAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) $this->refund_amount, 0, ',', '.')
        );
    }

    /**
     * Relasi ke pendaftaran yang dibatalkan.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Relasi ke user / jamaah yang mengajukan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin yang memproses/menyetujui pembatalan.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Admin yang menolak pembatalan.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
