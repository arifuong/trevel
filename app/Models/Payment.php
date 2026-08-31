<?php

namespace App\Models;

use App\Services\ImageUploadService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    public const TYPE_DP = 'dp';
    public const TYPE_PELUNASAN = 'pelunasan';

    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';

    /**
     * PRD Section 9 - Data Pembayaran:
     * Jenis (DP/pelunasan), nominal, bukti transfer,
     * status verifikasi, tanggal
     */
    protected $fillable = [
        'registration_id',
        'type',
        'amount',
        'proof_file',
        'status',
        'receipt_number',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'verified_by',
        'verified_at',
    ];

    protected $appends = [
        'amount_formatted',
        'proof_url',
        'type_label',
        'status_label',
        'receipt_number',
    ];

    /**
     * Model boot: Otomatis hapus file fisik bukti pembayaran saat record payment dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(function (Payment $payment) {
            $imageService = app(ImageUploadService::class);
            $imageService->deleteFile($payment->proof_file);
        });

        static::saving(function (Payment $payment) {
            if ($payment->status === self::STATUS_DISETUJUI && empty($payment->receipt_number)) {
                $year = $payment->verified_at ? $payment->verified_at->format('Y') : date('Y');
                $latestId = (int) (Payment::whereNotNull('receipt_number')->max('id') ?? 0) + 1;
                $payment->receipt_number = sprintf('KWT-%s-%05d', $year, $latestId);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'rejected_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    protected function amountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) $this->amount, 0, ',', '.')
        );
    }

    protected function proofUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->proof_file ? Storage::url($this->proof_file) : null
        );
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->type) {
                self::TYPE_DP => 'Uang Muka (DP)',
                self::TYPE_PELUNASAN => 'Pelunasan / Cicilan',
                default => strtoupper($this->type),
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                self::STATUS_MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
                self::STATUS_DISETUJUI => 'Sudah Diverifikasi',
                self::STATUS_DITOLAK => 'Ditolak',
                default => ucwords(str_replace('_', ' ', $this->status)),
            }
        );
    }

    public function getReceiptNumberAttribute(?string $value): ?string
    {
        if ($this->status !== self::STATUS_DISETUJUI) {
            return null;
        }

        if (!empty($value)) {
            return $value;
        }

        $year = $this->verified_at ? $this->verified_at->format('Y') : ($this->created_at ? $this->created_at->format('Y') : date('Y'));
        $generated = sprintf('KWT-%s-%05d', $year, $this->id ?? 1);

        if ($this->exists && empty($value)) {
            $this->forceFill(['receipt_number' => $generated])->saveQuietly();
        }

        return $generated;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_VERIFIKASI;
    }

    /**
     * Pembayaran milik satu pendaftaran.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Admin yang menolak bukti pembayaran.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Admin yang menyetujui bukti pembayaran.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
