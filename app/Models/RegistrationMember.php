<?php

namespace App\Models;

use App\Services\ImageUploadService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class RegistrationMember extends Model
{
    use HasFactory;

    // PRD Section 11 Document Statuses
    public const DOC_STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    public const DOC_STATUS_DISETUJUI = 'disetujui';
    public const DOC_STATUS_DITOLAK = 'ditolak';

    /**
     * PRD Section 9 - Data Anggota Keluarga (per orang):
     * NIK + file KTP, No. KK + file KK, No. Paspor + file Paspor,
     * Hubungan keluarga, file Buku Nikah, file Akta Kelahiran
     */
    protected $fillable = [
        'registration_id',
        'name',
        'birth_place',
        'birth_date',
        'gender',
        'nik',
        'address',
        'ktp_file',
        'no_kk',
        'kk_file',
        'no_passport',
        'passport_file',
        'relationship',
        'marriage_book_file',
        'birth_certificate_file',
        'document_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'rejected_at',
        'rejected_by',
    ];

    protected $appends = [
        'relationship_label',
        'document_status_label',
        'gender_label',
    ];

    /**
     * Model boot: Otomatis hapus seluruh file fisik berkas saat record anggota dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(function (RegistrationMember $member) {
            $imageService = app(ImageUploadService::class);
            $imageService->deleteFile($member->ktp_file);
            $imageService->deleteFile($member->kk_file);
            $imageService->deleteFile($member->passport_file);
            $imageService->deleteFile($member->marriage_book_file);
            $imageService->deleteFile($member->birth_certificate_file);
        });
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'verified_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    /**
     * Accessor label jenis kelamin (Laki-laki / Perempuan)
     */
    protected function genderLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->gender) {
                'laki-laki' => 'Laki-laki',
                'perempuan' => 'Perempuan',
                default => $this->gender ? ucfirst($this->gender) : '-',
            },
        );
    }

    /**
     * Mutator NIK: Pastikan hanya angka 0-9 (maks 16 digit) yang disimpan.
     */
    protected function nik(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value !== null ? substr(preg_replace('/[^0-9]/', '', $value), 0, 16) : null,
        );
    }

    /**
     * Mutator No. KK: Pastikan hanya angka 0-9 (maks 16 digit) yang disimpan.
     */
    protected function noKk(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value !== null ? substr(preg_replace('/[^0-9]/', '', $value), 0, 16) : null,
        );
    }

    // ──────────────────────────────────────────────────
    // Status helper methods
    // ──────────────────────────────────────────────────

    /**
     * Apakah dokumen sudah diverifikasi (final).
     */
    public function isVerified(): bool
    {
        return $this->document_status === self::DOC_STATUS_DISETUJUI;
    }

    /**
     * Apakah dokumen ditolak dan perlu diperbaiki.
     */
    public function isRejected(): bool
    {
        return $this->document_status === self::DOC_STATUS_DITOLAK;
    }

    /**
     * Apakah dokumen masih menunggu verifikasi.
     */
    public function isPending(): bool
    {
        return $this->document_status === self::DOC_STATUS_MENUNGGU_VERIFIKASI;
    }

    // ──────────────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────────────

    /**
     * Label hubungan keluarga yang ramah pengguna.
     */
    protected function relationshipLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->relationship) {
                'diri_sendiri' => 'Diri Sendiri (Pemesan)',
                'suami' => 'Suami',
                'istri' => 'Istri',
                'anak' => 'Anak',
                'orang_tua' => 'Orang Tua',
                'saudara' => 'Saudara Kandung',
                default => ucwords(str_replace('_', ' ', $this->relationship ?? '')),
            }
        );
    }

    /**
     * Label status dokumen.
     */
    protected function documentStatusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->document_status) {
                self::DOC_STATUS_MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
                self::DOC_STATUS_DISETUJUI => 'Sudah Diverifikasi',
                self::DOC_STATUS_DITOLAK => 'Perlu Diperbaiki',
                default => ucwords(str_replace('_', ' ', $this->document_status ?? '')),
            }
        );
    }

    public function getKtpUrlAttribute(): ?string
    {
        return $this->ktp_file ? Storage::url($this->ktp_file) : null;
    }

    public function getKkUrlAttribute(): ?string
    {
        return $this->kk_file ? Storage::url($this->kk_file) : null;
    }

    public function getPassportUrlAttribute(): ?string
    {
        return $this->passport_file ? Storage::url($this->passport_file) : null;
    }

    public function getMarriageBookUrlAttribute(): ?string
    {
        return $this->marriage_book_file ? Storage::url($this->marriage_book_file) : null;
    }

    public function getBirthCertificateUrlAttribute(): ?string
    {
        return $this->birth_certificate_file ? Storage::url($this->birth_certificate_file) : null;
    }

    // ──────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────

    /**
     * Anggota keluarga milik satu pendaftaran.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Admin yang memverifikasi dokumen.
     */
    public function verifiedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Admin yang menolak dokumen.
     */
    public function rejectedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Riwayat perubahan status dokumen (audit trail).
     */
    public function verificationHistories(): HasMany
    {
        return $this->hasMany(DocumentVerificationHistory::class);
    }

    /**
     * Seluruh dokumen yang diunggah anggota jamaah (baik tahap awal maupun keberangkatan).
     */
    public function documents(): HasMany
    {
        return $this->hasMany(JamaahDocument::class, 'registration_member_id');
    }

    /**
     * Dokumen tahap keberangkatan untuk anggota jamaah ini.
     */
    public function departureDocuments(): HasMany
    {
        return $this->hasMany(JamaahDocument::class, 'registration_member_id')
            ->whereHas('documentType', function ($query) {
                $query->where('phase', DocumentType::PHASE_KEBERANGKATAN);
            });
    }

    /**
     * Ambil dokumen keberangkatan berdasarkan kode (VISA, VAKSIN_MENINGITIS, FOTO_VISA).
     */
    public function getDocumentByCode(string $code): ?JamaahDocument
    {
        if ($this->relationLoaded('documents')) {
            return $this->documents->first(fn($d) => $d->documentType && $d->documentType->code === $code);
        }

        return $this->documents()->whereHas('documentType', fn($q) => $q->where('code', $code))->first();
    }
}
