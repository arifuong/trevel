<?php

namespace App\Http\Controllers;

use App\Helpers\TerbilangHelper;
use App\Models\Payment;
use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Tampilkan dan cetak Invoice resmi tagihan pendaftaran (Tampilan Web / Print).
     * TIDAK MENYIMPAN file fisik ke disk pada saat halaman dibuka.
     */
    public function invoice(Request $request, Registration $registration)
    {
        $user = $request->user();

        // Validasi hak akses: Admin atau Jamaah pemilik pendaftaran
        if ($user->role !== 'admin' && $user->id !== $registration->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat invoice pendaftaran ini.');
        }

        $registration->load([
            'user',
            'package',
            'packageVariant',
            'members',
            'invoice',
            'payments' => function ($q) {
                $q->where('status', Payment::STATUS_DISETUJUI)->orderBy('verified_at', 'asc');
            },
        ]);

        $invoice = $registration->invoice;

        if (!$invoice) {
            $memberCount = max(1, $registration->members->count());
            $packagePrice = (float) ($registration->package?->price ?? 0);
            $totalPrice = $packagePrice * $memberCount;

            $invoice = $registration->invoice()->create([
                'total_price' => $totalPrice,
                'total_paid' => 0,
                'remaining_balance' => $totalPrice,
                'due_date' => now()->addDays(7),
            ]);
            $registration->setRelation('invoice', $invoice);
        }

        $terbilangTotal = TerbilangHelper::terbilang($invoice->total_price);
        $terbilangRemaining = TerbilangHelper::terbilang($invoice->remaining_balance);
        $terbilangPaid = TerbilangHelper::terbilang($invoice->total_paid);

        return view('documents.invoice', [
            'registration' => $registration,
            'invoice' => $invoice,
            'user' => $registration->user,
            'package' => $registration->package,
            'members' => $registration->members,
            'verifiedPayments' => $registration->payments,
            'terbilangTotal' => $terbilangTotal,
            'terbilangRemaining' => $terbilangRemaining,
            'terbilangPaid' => $terbilangPaid,
            'autoPrint' => $request->boolean('print'),
        ]);
    }

    /**
     * Unduh atau stream PDF Invoice resmi secara ON-THE-FLY LANGSUNG DI MEMORI.
     * Tidak menyimpan file fisik ke storage server (zero disk storage footprint).
     */
    public function downloadInvoicePdf(Request $request, Registration $registration)
    {
        $user = $request->user();

        // Validasi hak akses RBAC: Admin atau Jamaah pemilik pendaftaran
        if ($user->role !== 'admin' && $user->id !== $registration->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh invoice pendaftaran ini.');
        }

        $registration->load([
            'user',
            'package',
            'packageVariant',
            'members',
            'invoice',
            'payments' => function ($q) {
                $q->where('status', Payment::STATUS_DISETUJUI)->orderBy('verified_at', 'asc');
            },
        ]);

        $invoice = $registration->invoice;

        if (!$invoice) {
            $memberCount = max(1, $registration->members->count());
            $packagePrice = (float) ($registration->package?->price ?? 0);
            $totalPrice = $packagePrice * $memberCount;

            $invoice = $registration->invoice()->create([
                'total_price' => $totalPrice,
                'total_paid' => 0,
                'remaining_balance' => $totalPrice,
                'due_date' => now()->addDays(7),
            ]);
            $registration->setRelation('invoice', $invoice);
        }

        $terbilangTotal = TerbilangHelper::terbilang($invoice->total_price);
        $terbilangRemaining = TerbilangHelper::terbilang($invoice->remaining_balance);
        $terbilangPaid = TerbilangHelper::terbilang($invoice->total_paid);

        // Generate PDF langsung di memori tanpa pemanggilan ->save() ke disk
        $pdf = Pdf::loadView('documents.pdf.invoice', [
            'registration' => $registration,
            'invoice' => $invoice,
            'user' => $registration->user,
            'package' => $registration->package,
            'members' => $registration->members,
            'verifiedPayments' => $registration->payments,
            'terbilangTotal' => $terbilangTotal,
            'terbilangRemaining' => $terbilangRemaining,
            'terbilangPaid' => $terbilangPaid,
        ])->setPaper('A4', 'portrait');

        $cleanNumber = str_replace(['/', '\\', ' '], '-', $invoice->invoice_number);
        $filename = "Invoice-{$cleanNumber}.pdf";

        // Dukungan stream di browser (jika query ?stream=1) atau download otomatis
        if ($request->boolean('stream')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Tampilkan Kwitansi resmi pembayaran (Tampilan Web / Print).
     * Hanya berlaku untuk pembayaran yang sudah disetujui (verified).
     */
    public function receipt(Request $request, Payment $payment)
    {
        $user = $request->user();
        $registration = $payment->registration;

        // Validasi hak akses: Admin atau Jamaah pemilik pembayaran
        if ($user->role !== 'admin' && $user->id !== $registration->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat kwitansi pembayaran ini.');
        }

        // Kwitansi hanya sah untuk pembayaran yang sudah disetujui
        if ($payment->status !== Payment::STATUS_DISETUJUI) {
            return back()->with('error', 'Kwitansi pembayaran resmi hanya dapat dicetak untuk pembayaran yang telah diverifikasi dan disetujui oleh Admin.');
        }

        $payment->load([
            'registration.user',
            'registration.package',
            'registration.packageVariant',
            'registration.invoice',
            'verifiedBy',
        ]);

        $terbilang = TerbilangHelper::terbilang($payment->amount);

        return view('documents.receipt', [
            'payment' => $payment,
            'registration' => $registration,
            'user' => $registration->user,
            'package' => $registration->package,
            'invoice' => $registration->invoice,
            'terbilang' => $terbilang,
        ]);
    }

    /**
     * Unduh atau stream PDF Kwitansi resmi secara ON-THE-FLY LANGSUNG DI MEMORI.
     * Tidak menyimpan file fisik ke storage server (zero disk storage footprint).
     */
    public function downloadReceiptPdf(Request $request, Payment $payment)
    {
        $user = $request->user();
        $registration = $payment->registration;

        // Validasi hak akses RBAC: Admin atau Jamaah pemilik pembayaran
        if ($user->role !== 'admin' && $user->id !== $registration->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh kwitansi pembayaran ini.');
        }

        if ($payment->status !== Payment::STATUS_DISETUJUI) {
            return back()->with('error', 'Kwitansi resmi hanya dapat diunduh untuk pembayaran yang telah disetujui Admin.');
        }

        $payment->load([
            'registration.user',
            'registration.package',
            'registration.packageVariant',
            'registration.invoice',
            'verifiedBy',
        ]);

        $terbilang = TerbilangHelper::terbilang($payment->amount);

        // Generate PDF langsung di memori tanpa pemanggilan ->save() ke disk
        $pdf = Pdf::loadView('documents.pdf.receipt', [
            'payment' => $payment,
            'registration' => $registration,
            'user' => $registration->user,
            'package' => $registration->package,
            'invoice' => $registration->invoice,
            'terbilang' => $terbilang,
        ])->setPaper('A4', 'portrait');

        $cleanNumber = str_replace(['/', '\\', ' '], '-', $payment->receipt_number);
        $filename = "Kwitansi-{$cleanNumber}.pdf";

        if ($request->boolean('stream')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Unduh file Excel Invoice resmi (fallback / opsi spreadsheet).
     * Jika format=pdf, otomatis dialihkan ke downloadInvoicePdf.
     * Menggunakan deleteFileAfterSend(true) agar file sementara tidak tertinggal di server.
     */
    public function downloadInvoice(Request $request, Registration $registration, \App\Services\ExcelDocumentService $excelService)
    {
        if ($request->query('format') === 'pdf' || $request->query('type') === 'pdf') {
            return $this->downloadInvoicePdf($request, $registration);
        }

        $user = $request->user();

        if ($user->role !== 'admin' && $user->id !== $registration->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh invoice pendaftaran ini.');
        }

        $filePath = $excelService->getOrGenerateInvoicePath($registration);

        return response()->download($filePath, basename($filePath), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Unduh file Excel Kwitansi resmi (fallback / opsi spreadsheet).
     * Jika format=pdf, otomatis dialihkan ke downloadReceiptPdf.
     * Menggunakan deleteFileAfterSend(true) agar file sementara tidak tertinggal di server.
     */
    public function downloadReceipt(Request $request, Payment $payment, \App\Services\ExcelDocumentService $excelService)
    {
        if ($request->query('format') === 'pdf' || $request->query('type') === 'pdf') {
            return $this->downloadReceiptPdf($request, $payment);
        }

        $user = $request->user();
        $registration = $payment->registration;

        if ($user->role !== 'admin' && $user->id !== $registration->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh kwitansi pembayaran ini.');
        }

        if ($payment->status !== Payment::STATUS_DISETUJUI) {
            return back()->with('error', 'Kwitansi resmi hanya dapat diunduh untuk pembayaran yang telah disetujui Admin.');
        }

        $filePath = $excelService->getOrGenerateReceiptPath($payment);

        return response()->download($filePath, basename($filePath), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
