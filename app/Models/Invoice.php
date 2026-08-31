<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    /**
     * PRD Section 9 - Data Invoice:
     * Nama jamaah, paket, total harga, jumlah dibayar,
     * sisa tagihan, tanggal jatuh tempo
     *
     * Nama jamaah & paket diakses via relasi:
     * invoice → registration → user (nama jamaah)
     * invoice → registration → package (paket)
     */
    protected $fillable = [
        'registration_id',
        'invoice_number',
        'total_price',
        'total_paid',
        'remaining_balance',
        'due_date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $year = date('Y');
                $latestId = (int) (Invoice::max('id') ?? 0) + 1;
                $invoice->invoice_number = sprintf('INV-%s-%05d', $year, $latestId);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    /**
     * Memastikan invoice memiliki nomor resmi persisten jika belum tersimpan.
     */
    public function getInvoiceNumberAttribute(?string $value): string
    {
        if (!empty($value)) {
            return $value;
        }

        $year = $this->created_at ? $this->created_at->format('Y') : date('Y');
        $generated = sprintf('INV-%s-%05d', $year, $this->id ?? 1);

        if ($this->exists && empty($value)) {
            $this->forceFill(['invoice_number' => $generated])->saveQuietly();
        }

        return $generated;
    }

    /**
     * Customer ID format untuk template invoice.
     */
    public function getCustomerIdAttribute(): string
    {
        $userId = $this->registration?->user_id ?? $this->registration_id ?? 1;
        return sprintf('CUST-%05d', $userId);
    }

    /**
     * Format Rupiah Total Tagihan.
     */
    public function getTotalPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_price, 0, ',', '.');
    }

    /**
     * Format Rupiah Total Terbayar.
     */
    public function getTotalPaidFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_paid, 0, ',', '.');
    }

    /**
     * Format Rupiah Sisa Tagihan.
     */
    public function getRemainingBalanceFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->remaining_balance, 0, ',', '.');
    }

    /**
     * Status Tagihan / Pembayaran.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        if ($this->registration && $this->registration->status === Registration::STATUS_DIBATALKAN) {
            return 'Dibatalkan';
        }

        if ((float) $this->total_paid >= (float) $this->total_price && (float) $this->total_price > 0) {
            return 'Lunas';
        }

        if ((float) $this->total_paid > 0) {
            return 'Belum Lunas';
        }

        return 'Menunggu Pembayaran';
    }

    /**
     * Invoice milik satu pendaftaran.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
