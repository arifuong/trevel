<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;

    // PRD Section 11 Status Constants
    public const STATUS_MENUNGGU_VERIFIKASI_DOKUMEN = 'menunggu_verifikasi_dokumen';
    public const STATUS_MENUNGGU_PEMBAYARAN_DP = 'menunggu_pembayaran_dp';
    public const STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP = 'menunggu_verifikasi_pembayaran_dp';
    public const STATUS_JAMAAH = 'jamaah';
    public const STATUS_CICILAN_PELUNASAN = 'cicilan_pelunasan';
    public const STATUS_LUNAS = 'lunas';
    public const STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN = 'menunggu_kelengkapan_keberangkatan';
    public const STATUS_BERANGKAT = 'berangkat';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    /**
     * Total tahapan alur persiapan ibadah (Single Source of Truth).
     */
    public const TOTAL_STEPS = 9;

    protected $fillable = [
        'user_id',
        'package_id',
        'package_variant_id',
        'room_type',
        'registration_number',
        'status',
        'cancellation_status',
    ];

    protected $appends = [
        'status_label',
        'step_number',
        'total_steps',
        'progress_percentage',
        'registration_number',
    ];

    /**
     * Generate Nomor Registrasi Acak, Unik, Kriptografis, & Permanen (Format: REG-XXXXXX)
     * Menggunakan huruf kapital dan angka, tanpa karakter ambigu (O, 0, I, 1, L).
     */
    public static function generateUniqueRegistrationNumber(): string
    {
        $charset = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $charsetLength = strlen($charset);

        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $charset[random_int(0, $charsetLength - 1)];
            }
            $registrationNumber = 'REG-' . $code;
        } while (self::where('registration_number', $registrationNumber)->exists());

        return $registrationNumber;
    }

    /**
     * Model boot: Otomatis generate nomor pendaftaran acak unik sekali saat dibuat,
     * dan cascade delete pada members dan payments agar file fisik terhapus.
     */
    protected static function booted(): void
    {
        static::creating(function (Registration $registration) {
            if (empty($registration->registration_number)) {
                $registration->registration_number = self::generateUniqueRegistrationNumber();
            }
        });

        static::deleting(function (Registration $registration) {
            foreach ($registration->members as $member) {
                $member->delete();
            }
            foreach ($registration->payments as $payment) {
                $payment->delete();
            }
        });
    }

    /**
     * Memastikan registration_number selalu persisten dan tidak null.
     */
    public function getRegistrationNumberAttribute(?string $value): string
    {
        if (!empty($value)) {
            return $value;
        }

        $generated = self::generateUniqueRegistrationNumber();
        if ($this->exists && empty($value)) {
            $this->forceFill(['registration_number' => $generated])->saveQuietly();
        }

        return $generated;
    }

    /**
     * Label manusiawi untuk status pendaftaran.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                self::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN => 'Menunggu Verifikasi Dokumen',
                self::STATUS_MENUNGGU_PEMBAYARAN_DP => 'Menunggu Pembayaran DP',
                self::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP => 'Menunggu Verifikasi DP',
                self::STATUS_JAMAAH => 'Sudah Terdaftar Resmi',
                self::STATUS_CICILAN_PELUNASAN => 'Proses Pelunasan',
                self::STATUS_LUNAS => 'Lunas',
                self::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN => 'Kelengkapan Dokumen Keberangkatan',
                self::STATUS_BERANGKAT => 'Siap Berangkat',
                self::STATUS_SELESAI => 'Selesai',
                self::STATUS_DIBATALKAN => 'Dibatalkan',
                default => ucwords(str_replace('_', ' ', $this->status)),
            }
        );
    }

    /**
     * Step index (1-9) untuk indikator alur PRD & Alur 9 Tahap Persiapan Ibadah.
     */
    protected function stepNumber(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                self::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN => 1,
                self::STATUS_MENUNGGU_PEMBAYARAN_DP => 2,
                self::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP => 3,
                self::STATUS_JAMAAH => 4,
                self::STATUS_CICILAN_PELUNASAN => 5,
                self::STATUS_LUNAS => 6,
                self::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN => 7,
                self::STATUS_BERANGKAT => 8,
                self::STATUS_SELESAI => 9,
                self::STATUS_DIBATALKAN => 0,
                default => 1,
            }
        );
    }

    /**
     * Dapatkan total tahapan alur persiapan ibadah secara statis (Single Source of Truth).
     */
    public static function totalSteps(): int
    {
        return self::TOTAL_STEPS;
    }

    /**
     * Accessor total_steps untuk model Registration.
     */
    public function getTotalStepsAttribute(): int
    {
        return self::TOTAL_STEPS;
    }

    /**
     * Persentase progress persiapan ibadah (0 - 100%) dengan batasan matematis aman.
     * Tidak akan pernah bernilai lebih dari 100% atau kurang dari 0%.
     */
    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status === self::STATUS_DIBATALKAN) {
                    return 0;
                }

                $step = (int) $this->step_number;
                if ($step <= 0) {
                    return 0;
                }

                $percentage = (int) round(($step / self::TOTAL_STEPS) * 100);
                return min(100, max(0, $percentage));
            }
        );
    }

    /**
     * Apakah pendaftaran masih aktif.
     */
    public function isActive(): bool
    {
        return !in_array($this->status, [self::STATUS_BERANGKAT, self::STATUS_SELESAI, self::STATUS_DIBATALKAN]);
    }

    /**
     * Apakah perjalanan ibadah telah selesai (Tahap 9).
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    /**
     * Menghitung harga resmi per orang (jamaah) berdasarkan Database Source of Truth.
     * Mengikuti prioritas:
     * 1. Harga varian untuk tipe kamar yang dipilih (promo_price jika ada, else normal_price)
     * 2. Harga terendah varian aktif
     * 3. Harga paket induk
     */
    public function getOfficialPricePerPerson(): float
    {
        $package = $this->package;
        $variant = $this->packageVariant;
        $roomType = $this->room_type ?? 'quad';

        if ($variant) {
            $priceRecord = $variant->prices()
                ->where('room_type', $roomType)
                ->where('is_active', true)
                ->first();

            if ($priceRecord && (float) $priceRecord->normal_price > 0) {
                return (float) ($priceRecord->promo_price ?: $priceRecord->normal_price);
            }

            if ($variant->lowest_price !== null && (float) $variant->lowest_price > 0) {
                return (float) $variant->lowest_price;
            }
        }

        return (float) ($package?->price ?? 0);
    }

    /**
     * Menghitung total tagihan resmi pendaftaran berdasarkan harga resmi x jumlah anggota jamaah.
     */
    public function calculateOfficialTotalPrice(): float
    {
        $memberCount = max(1, $this->members()->count());
        return $this->getOfficialPricePerPerson() * $memberCount;
    }

    /**
     * Perhitungan Cancellation Fee & Estimasi Refund
     * PERSIS sesuai tabel di PRD Bagian "8. Business Rules" & Section 6.10:
     * - Belum bayar DP sama sekali -> Bebas biaya (0%), refund Rp 0
     * - Setelah menjadi pendaftar (sudah DP) -> 2% dari harga paket
     * - 25 hari sebelum keberangkatan (H-25 s/d H-16) -> 25% dari harga paket
     * - 15 hari sebelum keberangkatan (H-15 s/d H-7) -> 65% dari harga paket
     * - 6 hari sebelum keberangkatan (H-6 s/d H) -> 85% dari harga paket
     */
    public function calculateCancellationFee(): array
    {
        $invoice = $this->invoice;
        $totalPaid = $invoice ? (float) $invoice->total_paid : 0;
        
        $totalPrice = $invoice && $invoice->total_price > 0
            ? (float) $invoice->total_price
            : $this->calculateOfficialTotalPrice();

        $departureDate = $this->package && $this->package->departure_date 
            ? Carbon::parse($this->package->departure_date) 
            : null;

        // 1. Jika belum bayar DP sama sekali -> bebas biaya pembatalan
        if ($totalPaid <= 0) {
            return [
                'has_paid' => false,
                'fee_percentage' => 0,
                'fee_amount' => 0.0,
                'fee_formatted' => 'Rp 0 (Bebas Biaya)',
                'total_paid' => 0.0,
                'total_paid_formatted' => 'Rp 0',
                'refund_amount' => 0.0,
                'refund_formatted' => 'Rp 0',
                'category' => 'Belum Bayar DP (Bebas Biaya)',
                'days_to_departure' => $departureDate ? (int) now()->diffInDays($departureDate, false) : null,
            ];
        }

        // 2. Jika sudah bayar DP -> hitung fee bertingkat dari total harga paket
        $daysToDeparture = $departureDate ? (int) now()->diffInDays($departureDate, false) : 999;

        if ($daysToDeparture <= 6) {
            $percentage = 85;
            $category = 'H-6 Sebelum Keberangkatan (85%)';
        } elseif ($daysToDeparture <= 15) {
            $percentage = 65;
            $category = 'H-15 Sebelum Keberangkatan (65%)';
        } elseif ($daysToDeparture <= 25) {
            $percentage = 25;
            $category = 'H-25 Sebelum Keberangkatan (25%)';
        } else {
            $percentage = 2;
            $category = 'Setelah Menjadi Pendaftar / > 25 Hari (2%)';
        }

        $feeAmount = ($percentage / 100) * $totalPrice;
        $refundAmount = max(0, $totalPaid - $feeAmount);

        return [
            'has_paid' => true,
            'fee_percentage' => $percentage,
            'fee_amount' => $feeAmount,
            'fee_formatted' => 'Rp ' . number_format($feeAmount, 0, ',', '.') . " ({$percentage}%)",
            'total_paid' => $totalPaid,
            'total_paid_formatted' => 'Rp ' . number_format($totalPaid, 0, ',', '.'),
            'refund_amount' => $refundAmount,
            'refund_formatted' => 'Rp ' . number_format($refundAmount, 0, ',', '.'),
            'category' => $category,
            'days_to_departure' => $daysToDeparture,
        ];
    }

    /**
     * Pendaftaran milik satu user (jamaah).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pendaftaran untuk satu paket.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Pendaftaran untuk varian paket.
     */
    public function packageVariant(): BelongsTo
    {
        return $this->belongsTo(PackageVariant::class);
    }

    /**
     * Satu pendaftaran bisa punya banyak anggota keluarga.
     */
    public function members(): HasMany
    {
        return $this->hasMany(RegistrationMember::class);
    }

    /**
     * Satu pendaftaran bisa punya banyak pembayaran (DP + pelunasan).
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Satu pendaftaran punya satu invoice.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Riwayat pengajuan pembatalan pendaftaran.
     */
    public function cancellations(): HasMany
    {
        return $this->hasMany(RegistrationCancellation::class);
    }

    /**
     * Pengajuan pembatalan pendaftaran terkini.
     */
    public function latestCancellation(): HasOne
    {
        return $this->hasOne(RegistrationCancellation::class)->latestOfMany();
    }

    /**
     * Cek apakah ada pengajuan pembatalan yang sedang menunggu verifikasi admin.
     */
    public function hasPendingCancellation(): bool
    {
        return $this->cancellation_status === RegistrationCancellation::STATUS_PENDING;
    }

    /**
     * Cek apakah pembatalan sudah disetujui admin.
     */
    public function hasApprovedCancellation(): bool
    {
        return $this->cancellation_status === RegistrationCancellation::STATUS_APPROVED || $this->status === self::STATUS_DIBATALKAN;
    }

    /**
     * Cek apakah pengajuan pembatalan terakhir ditolak admin.
     */
    public function hasRejectedCancellation(): bool
    {
        return $this->cancellation_status === RegistrationCancellation::STATUS_REJECTED;
    }
}
