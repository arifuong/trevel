<?php

namespace App\Services;

use App\Helpers\TerbilangHelper;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ExcelDocumentService
{
    /**
     * Path direktori master template.
     */
    protected string $templateDir;

    /**
     * Path direktori penyimpanan invoice.
     */
    protected string $invoiceDir;

    /**
     * Path direktori penyimpanan kwitansi.
     */
    protected string $receiptDir;

    public function __construct()
    {
        $this->templateDir = storage_path('app/templates');
        $this->invoiceDir = storage_path('app/documents/invoices');
        $this->receiptDir = storage_path('app/documents/receipts');

        File::ensureDirectoryExists($this->invoiceDir);
        File::ensureDirectoryExists($this->receiptDir);
    }

    /**
     * Generate file Excel Invoice berdasarkan template master Invoice.xlsx.
     */
    public function generateInvoice(Registration $registration): string
    {
        $invoice = $registration->invoice;

        if (!$invoice) {
            $totalPrice = $registration->calculateOfficialTotalPrice();

            $invoice = $registration->invoice()->create([
                'total_price' => $totalPrice,
                'total_paid' => 0,
                'remaining_balance' => $totalPrice,
                'due_date' => now()->addDays(7),
            ]);
        }

        $invoiceNumber = $invoice->invoice_number;
        $cleanNumber = str_replace(['/', '\\', ' '], '_', $invoiceNumber);
        $targetFile = $this->invoiceDir . "/Invoice_{$cleanNumber}.xlsx";

        $templateFile = $this->templateDir . '/Invoice.xlsx';
        if (!file_exists($templateFile)) {
            $templateFile = $this->templateDir . '/invoice_template.xlsx';
        }

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Master template Invoice tidak ditemukan di {$templateFile}");
        }

        $user = $registration->user;
        $package = $registration->package;
        $memberCount = max(1, $registration->members->count());
        $totalPrice = (float) $invoice->total_price;
        $packagePrice = $memberCount > 0 ? ($totalPrice / $memberCount) : (float) ($package?->price ?? 0);
        $totalPaid = (float) $invoice->total_paid;
        $remainingBalance = (float) $invoice->remaining_balance;
        $terbilangRemaining = TerbilangHelper::terbilang($remainingBalance);

        $packageName = $package ? $package->name : 'Paket Umrah';
        if ($registration->packageVariant) {
            $packageName .= ' - ' . $registration->packageVariant->name;
            if ($registration->room_type) {
                $packageName .= ' (' . ucfirst($registration->room_type) . ')';
            }
        }

        // Ambil pembayaran yang disetujui (maksimal 4 transaksi sesuai template baris 28-31)
        $verifiedPayments = $registration->payments()
            ->where('status', Payment::STATUS_DISETUJUI)
            ->orderBy('verified_at', 'asc')
            ->take(4)
            ->get();

        // 1. Placeholder replacements for xl/sharedStrings.xml
        $p1 = $verifiedPayments->first();
        $p1Tgl = $p1 ? (($p1->verified_at ? $p1->verified_at->format('d/m/Y') : $p1->created_at->format('d/m/Y')) . ' - ' . $p1->type_label) : '-';
        $p1Nominal = $p1 ? $p1->amount_formatted : 'Rp 0';

        $placeholderReplacements = [
            '{{TANGGAL}}'        => $invoice->created_at ? $invoice->created_at->format('d/m/Y') : date('d/m/Y'),
            '{{NO_INV}}'         => $invoiceNumber,
            '{{ID_CUST}}'        => $invoice->customer_id,
            '{{NAMA}}'           => $user?->name ?? 'JAMAAH',
            '{{PAKET}}'          => $packageName,
            '{{HARGA}}'          => 'Rp ' . number_format($packagePrice, 0, ',', '.'),
            '{{DISKON}}'         => '-',
            '{{TOTAL}}'          => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
            '{{TERBILANG_SISA}}' => strtoupper(TerbilangHelper::terbilang($remainingBalance, false)) . ' RUPIAH',
            '{{TRANS_TGL}}'      => $p1Tgl,
            '{{PAYMENT}}'        => $p1Nominal,
        ];

        // 2. Cell-level overrides for summary and additional transactions 2..4
        $cellReplacements = [
            'I21' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
            'I22' => 'Rp ' . number_format($totalPaid, 0, ',', '.'),
            'I23' => 'Rp ' . number_format($remainingBalance, 0, ',', '.'),
        ];

        // Baris transaksi 2 (Row 29)
        if ($verifiedPayments->count() >= 2) {
            $p2 = $verifiedPayments->get(1);
            $cellReplacements['B29'] = ($p2->verified_at ? $p2->verified_at->format('d/m/Y') : $p2->created_at->format('d/m/Y')) . ' - ' . $p2->type_label;
            $cellReplacements['D29'] = $p2->amount_formatted;
            $cellReplacements['F29'] = $user?->name ?? '';
            $cellReplacements['H29'] = $p2->amount_formatted;
        }

        // Baris transaksi 3 (Row 30)
        if ($verifiedPayments->count() >= 3) {
            $p3 = $verifiedPayments->get(2);
            $cellReplacements['B30'] = ($p3->verified_at ? $p3->verified_at->format('d/m/Y') : $p3->created_at->format('d/m/Y')) . ' - ' . $p3->type_label;
            $cellReplacements['D30'] = $p3->amount_formatted;
            $cellReplacements['F30'] = $user?->name ?? '';
            $cellReplacements['H30'] = $p3->amount_formatted;
        }

        // Baris transaksi 4 (Row 31)
        if ($verifiedPayments->count() >= 4) {
            $p4 = $verifiedPayments->get(3);
            $cellReplacements['B31'] = ($p4->verified_at ? $p4->verified_at->format('d/m/Y') : $p4->created_at->format('d/m/Y')) . ' - ' . $p4->type_label;
            $cellReplacements['D31'] = $p4->amount_formatted;
            $cellReplacements['F31'] = $user?->name ?? '';
            $cellReplacements['H31'] = $p4->amount_formatted;
        }

        $this->buildExcelFromTemplate($templateFile, $targetFile, $placeholderReplacements, $cellReplacements);

        return $targetFile;
    }

    /**
     * Generate file Excel Kwitansi berdasarkan template master Kwitansi.xlsx.
     * Hanya boleh dipanggil untuk pembayaran berstatus 'disetujui'.
     */
    public function generateReceipt(Payment $payment): string
    {
        if ($payment->status !== Payment::STATUS_DISETUJUI) {
            throw new \InvalidArgumentException("Kwitansi hanya dapat dibuat untuk pembayaran yang telah disetujui (verified).");
        }

        $receiptNumber = $payment->receipt_number;
        $cleanNumber = str_replace(['/', '\\', ' '], '_', $receiptNumber);
        $targetFile = $this->receiptDir . "/Kwitansi_{$cleanNumber}.xlsx";

        $templateFile = $this->templateDir . '/Kwitansi.xlsx';
        if (!file_exists($templateFile)) {
            $templateFile = $this->templateDir . '/kuitansi_template.xlsx';
        }

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Master template Kwitansi tidak ditemukan di {$templateFile}");
        }

        $registration = $payment->registration;
        $user = $registration?->user;
        $package = $registration?->package;

        $tglVerifikasi = $payment->verified_at ? $payment->verified_at->translatedFormat('d F Y') : date('d F Y');
        $terbilang = TerbilangHelper::terbilang($payment->amount, false);
        $paketKet = $package?->name ?? 'Paket Umrah';
        $nominalFormatted = $payment->amount_formatted;

        $placeholderReplacements = [
            '{{NO_KWT}}'           => $receiptNumber,
            '{{TANGGAL}}'          => $tglVerifikasi,
            '{{NAMA}}'             => $user?->name ?? 'JAMAAH',
            '{{TERBILANG_BAYAR}}'  => strtoupper($terbilang) . ' RUPIAH',
            '{{PAKET}}'            => $paketKet,
            '{{PAYMENT}}'          => $nominalFormatted,
        ];

        $this->buildExcelFromTemplate($templateFile, $targetFile, $placeholderReplacements);

        return $targetFile;
    }

    /**
     * Memproses duplikasi template master, menginjeksikan data placeholder ke sharedStrings.xml dan sheet1.xml,
     * lalu mem-package kembali file xlsx tanpa mengubah format, logo, style, atau layout aslinya.
     */
    protected function buildExcelFromTemplate(string $masterTemplate, string $targetFile, array $placeholderReplacements, array $cellReplacements = []): void
    {
        $tempDir = storage_path('app/temp_xlsx_' . uniqid());
        File::ensureDirectoryExists($tempDir);

        $tempScript = $tempDir . '/zip_runner.ps1';
        $absMaster = str_replace('/', '\\', realpath($masterTemplate));
        $absTemp = str_replace('/', '\\', $tempDir);
        $absTarget = str_replace('/', '\\', $targetFile);

        // 1. Ekstrak master template
        $extractScript = "
Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::ExtractToDirectory('$absMaster', '$absTemp\\unpacked')
";
        file_put_contents($tempScript, $extractScript);
        exec("powershell -ExecutionPolicy Bypass -File \"{$tempScript}\"");

        $unpackedDir = $tempDir . '/unpacked';
        $sharedStringsPath = $unpackedDir . '/xl/sharedStrings.xml';
        $sheetXmlPath = $unpackedDir . '/xl/worksheets/sheet1.xml';

        // 2. Ganti seluruh placeholder string pada xl/sharedStrings.xml
        if (file_exists($sharedStringsPath)) {
            $sharedXml = file_get_contents($sharedStringsPath);

            foreach ($placeholderReplacements as $placeholder => $value) {
                $escapedValue = htmlspecialchars((string)$value, ENT_XML1, 'UTF-8');
                $sharedXml = str_replace($placeholder, $escapedValue, $sharedXml);
            }

            file_put_contents($sharedStringsPath, $sharedXml);
        }

        // 3. Jika ada cell-level overrides tambahan (misal transaksi ke-2, 3, 4)
        if (file_exists($sheetXmlPath) && !empty($cellReplacements)) {
            $sheetXml = file_get_contents($sheetXmlPath);

            foreach ($cellReplacements as $cellRef => $value) {
                $escapedValue = htmlspecialchars((string)$value, ENT_XML1, 'UTF-8');
                $pattern = '/<c\s+r="' . $cellRef . '"([^>]*)>(.*?)<\/c>|<c\s+r="' . $cellRef . '"([^>]*)\/>/s';

                if (preg_match($pattern, $sheetXml, $matches)) {
                    $attributes = !empty($matches[1]) ? $matches[1] : (!empty($matches[3]) ? $matches[3] : '');
                    $styleAttr = '';
                    if (preg_match('/s="\d+"/', $attributes, $sMatch)) {
                        $styleAttr = ' ' . $sMatch[0];
                    }

                    $replacement = '<c r="' . $cellRef . '"' . $styleAttr . ' t="inlineStr"><is><t>' . $escapedValue . '</t></is></c>';
                    $sheetXml = preg_replace($pattern, $replacement, $sheetXml, 1);
                }
            }

            file_put_contents($sheetXmlPath, $sheetXml);
        }

        // Hapus file target lama jika ada
        if (file_exists($targetFile)) {
            @unlink($targetFile);
        }

        // 4. Repackage kembali ke file target xlsx
        $packScript = "
Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory('$absTemp\\unpacked', '$absTarget')
";
        file_put_contents($tempScript, $packScript);
        exec("powershell -ExecutionPolicy Bypass -File \"{$tempScript}\"");

        // 5. Bersihkan temporary directory
        exec("powershell -Command \"Remove-Item -Recurse -Force '{$absTemp}' -ErrorAction SilentlyContinue\"");
    }

    /**
     * Dapatkan path file invoice yang tersimpan, atau buat jika belum ada.
     */
    public function getOrGenerateInvoicePath(Registration $registration): string
    {
        $invoice = $registration->invoice;
        if (!$invoice) {
            return $this->generateInvoice($registration);
        }

        $cleanNumber = str_replace(['/', '\\', ' '], '_', $invoice->invoice_number);
        $targetFile = $this->invoiceDir . "/Invoice_{$cleanNumber}.xlsx";

        if (!file_exists($targetFile)) {
            return $this->generateInvoice($registration);
        }

        return $targetFile;
    }

    /**
     * Dapatkan path file kwitansi yang tersimpan, atau buat jika belum ada.
     */
    public function getOrGenerateReceiptPath(Payment $payment): ?string
    {
        if ($payment->status !== Payment::STATUS_DISETUJUI) {
            return null;
        }

        $cleanNumber = str_replace(['/', '\\', ' '], '_', $payment->receipt_number);
        $targetFile = $this->receiptDir . "/Kwitansi_{$cleanNumber}.xlsx";

        if (!file_exists($targetFile)) {
            return $this->generateReceipt($payment);
        }

        return $targetFile;
    }
}
