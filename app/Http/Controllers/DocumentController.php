<?php

namespace App\Http\Controllers;

use App\Helpers\TerbilangHelper;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Tampilkan dan cetak Invoice resmi tagihan pendaftaran.
     */
    public function invoice(Request $request, Registration $registration, \App\Services\ExcelDocumentService $excelService)
    {
        $user = $request->user();

        // Validasi hak akses: Admin atau Jamaah pemilik pendaftaran
        if ($user->role !== 'admin' && $user->id !== $registration->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat invoice pendaftaran ini.');
        }

        $registration->load([
            'user',
            'package',
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

        // Pastikan dokumen fisik Invoice Excel hasil generate dari storage/app/templates/Invoice.xlsx
        // tersimpan di storage/app/documents/invoices/Invoice_{NO_INV}.xlsx
        $excelService->getOrGenerateInvoicePath($registration);

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
     * Tampilkan dan cetak Kwitansi resmi penerimaan pembayaran.
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
     * Unduh file Excel Invoice resmi yang digenerate dari template master.
     */
    public function downloadInvoice(Request $request, Registration $registration, \App\Services\ExcelDocumentService $excelService)
    {
        $user = $request->user();

        if ($user->role !== 'admin' && $user->id !== $registration->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh invoice pendaftaran ini.');
        }

        $filePath = $excelService->getOrGenerateInvoicePath($registration);

        return response()->download($filePath, basename($filePath), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Unduh file Excel Kwitansi resmi yang digenerate dari template master.
     */
    public function downloadReceipt(Request $request, Payment $payment, \App\Services\ExcelDocumentService $excelService)
    {
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
        ]);
    }
}
