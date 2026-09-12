<?php

namespace App\Models;

use App\Services\ImageUploadService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class JamaahDocument extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    public const STATUS_VALID = 'valid';
    public const STATUS_DISETUJUI = 'disetujui'; // Alias for valid
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_PERLU_PERBAIKAN = 'perlu_perbaikan';

    protected $fillable = [
        'registration_member_id',
        'document_type_id',
        'file_path',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected $appends = [
        'status_label',
        'file_url',
        'is_pdf',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (JamaahDocument $doc) {
            if (!empty($doc->file_path)) {
                app(ImageUploadService::class)->deleteFile($doc->file_path);
            }
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(RegistrationMember::class, 'registration_member_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function verifiedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                self::STATUS_VALID, self::STATUS_DISETUJUI => 'Valid',
                self::STATUS_DITOLAK => 'Ditolak',
                self::STATUS_PERLU_PERBAIKAN => 'Perlu Perbaikan',
                default => 'Menunggu Verifikasi',
            }
        );
    }

    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_path ? Storage::url($this->file_path) : null
        );
    }

    protected function isPdf(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_path ? str_ends_with(strtolower($this->file_path), '.pdf') : false
        );
    }

    public function isValid(): bool
    {
        return in_array($this->status, [self::STATUS_VALID, self::STATUS_DISETUJUI]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_VERIFIKASI;
    }

    public function isRejected(): bool
    {
        return in_array($this->status, [self::STATUS_DITOLAK, self::STATUS_PERLU_PERBAIKAN]);
    }

    public function markAsValid(?int $adminId = null): void
    {
        $this->update([
            'status' => self::STATUS_VALID,
            'rejection_reason' => null,
            'verified_by' => $adminId,
            'verified_at' => now(),
        ]);
    }

    public function markAsRejected(?int $adminId = null, ?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_DITOLAK,
            'rejection_reason' => $reason,
            'verified_by' => $adminId,
            'verified_at' => now(),
        ]);
    }
}
