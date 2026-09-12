<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Registration;
use App\Contracts\WhatsAppNotificationInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(
        private WhatsAppNotificationInterface $whatsapp
    ) {}

    /**
     * Tampilkan daftar pembayaran yang perlu diverifikasi admin.
     * PRD Section 6.7
     */
    public function index(Request $request)
    {
        $query = Payment::with([
            'registration.user',
            'registration.package',
            'registration.invoice',
            'rejectedBy',
            'verifiedBy'
        ])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'totalPayments' => Payment::count(),
            'pendingPaymentsCount' => Payment::where('status', Payment::STATUS_MENUNGGU_VERIFIKASI)->count(),
            'approvedPaymentsCount' => Payment::where('status', Payment::STATUS_DISETUJUI)->count(),
        ]);
    }

    /**
     * Verifikasi bukti pembayaran oleh admin (Approve / Reject).
     * PRD Section 6.5, 6.7, 6.8, 9, 10, 11:
     * - Saat DP disetujui: ubah status jadi "Jamaah", update invoice, kirim WA, buat record perlengkapan
     * - Saat Pelunasan bertahap disetujui: ubah status jadi "Cicilan Pelunasan" atau "Lunas" jika lunas
     * - Saat LUNAS: kirim notifikasi WhatsApp ucapan selamat sesuai PRD
     */
    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => [
                'required_if:action,reject',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->action === 'reject' && (!is_string($value) || trim($value) === '')) {
                        $fail('Alasan penolakan bukti pembayaran wajib diisi.');
                    }
                },
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'action.required' => 'Aksi verifikasi harus ditentukan.',
            'action.in' => 'Aksi verifikasi tidak valid.',
            'rejection_reason.required_if' => 'Alasan penolakan bukti pembayaran wajib diisi.',
            'rejection_reason.required' => 'Alasan penolakan bukti pembayaran wajib diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $registration = $payment->registration;
        $user = $registration->user;
        $package = $registration->package;

        DB::beginTransaction();

        try {
            if ($request->action === 'approve') {
                // 1. Update status pembayaran & audit timestamps
                $payment->update([
                    'status' => Payment::STATUS_DISETUJUI,
                    'rejection_reason' => null,
                    'verified_by' => auth()->id(),
                    'verified_at' => Carbon::now(),
                    'rejected_by' => null,
                    'rejected_at' => null,
                ]);

                // 2. Hitung ulang total pembayaran yang sudah disetujui
                $totalPaid = (float) $registration->payments()
                    ->where('status', Payment::STATUS_DISETUJUI)
                    ->sum('amount');

                // 3. Update Invoice
                $invoice = $registration->invoice;
                if (!$invoice) {
                    $totalPrice = $registration->calculateOfficialTotalPrice();
                    $invoice = Invoice::create([
                        'registration_id' => $registration->id,
                        'total_price' => $totalPrice,
                        'total_paid' => $totalPaid,
                        'remaining_balance' => max(0, $totalPrice - $totalPaid),
                        'due_date' => Carbon::now()->addDays(7),
                    ]);
                } else {
                    $remaining = max(0, (float) $invoice->total_price - $totalPaid);
                    $invoice->update([
                        'total_paid' => $totalPaid,
                        'remaining_balance' => $remaining,
                    ]);
                }

                $isFullyPaid = $invoice->remaining_balance <= 0;

                // 4. Update status registrasi sesuai alur 8 Tahap Persiapan Ibadah
                if ($isFullyPaid) {
                    $registration->update([
                        'status' => Registration::STATUS_LUNAS,
                    ]);

                    // Otomatis transisi ke MENUNGGU_KELENGKAPAN_KEBERANGKATAN atau BERANGKAT
                    $bookingStatusService = app(\App\Services\BookingStatusService::class);
                    $bookingStatusService->evaluateStatus($registration);
                } elseif ($payment->type === Payment::TYPE_DP) {
                    $registration->update([
                        'status' => Registration::STATUS_JAMAAH,
                    ]);
                } else {
                    $registration->update([
                        'status' => Registration::STATUS_CICILAN_PELUNASAN,
                    ]);
                }

                // 5. Otomatis Generate/Update Dokumen Excel Resmi (Kwitansi & Invoice)
                try {
                    $excelDocService = app(\App\Services\ExcelDocumentService::class);
                    $excelDocService->generateReceipt($payment->fresh());
                    $excelDocService->generateInvoice($registration->fresh());
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Gagal generate dokumen Excel: ' . $e->getMessage());
                }

                // 6. Kirim Notifikasi WhatsApp Sesuai PRD
                $formattedAmount = 'Rp ' . number_format((float) $payment->amount, 0, ',', '.');
                $formattedTotalPaid = 'Rp ' . number_format((float) $invoice->total_paid, 0, ',', '.');
                $formattedRemaining = 'Rp ' . number_format((float) $invoice->remaining_balance, 0, ',', '.');
                $regId = $registration->registration_number;

                if ($isFullyPaid) {
                    // Pesan WhatsApp Selamat Lunas
                    $waMessage = "Assalamu'alaikum Wr. Wb. Bapak/Ibu {$user->name},\n\n" .
                        "Alhamdulillah wa syukurillah, pembayaran paket {$package->name} Anda ({$regId}) telah LUNAS SEPENUHNYA dengan total pembayaran {$formattedTotalPaid}.\n\n" .
                        "Selamat! Anda telah menyelesaikan seluruh administrasi pembayaran ibadah Umrah/Haji bersama PT. Zein Internasional.\n\n" .
                        "Tim kami akan segera menghubungi Anda untuk koordinasi bimbingan manasik dan jadwal persiapan ibadah Anda.\n\n" .
                        "Jazakumullah khairan katsiran atas kepercayaan Anda.\n" .
                        "PT. Zein Internasional";
                } elseif ($payment->type === Payment::TYPE_DP) {
                    // Pesan WhatsApp DP Disetujui
                    $waMessage = "Assalamu'alaikum Wr. Wb. Bapak/Ibu {$user->name},\n\n" .
                        "Alhamdulillah, pembayaran DP Anda sebesar {$formattedAmount} untuk pendaftaran {$package->name} telah BERHASIL DIVERIFIKASI dan DISETUJUI oleh tim PT. Zein Internasional.\n\n" .
                        "📋 Nomor Registrasi: {$regId}\n" .
                        "🕋 Status Anda: Resmi Terdaftar sebagai Jamaah\n" .
                        "💰 Sisa Tagihan Pelunasan: {$formattedRemaining}\n\n" .
                        "Invoice dan kuitansi resmi telah diperbarui di dashboard Anda. Silakan pantau persiapan keberangkatan ibadah Anda melalui sistem.\n\n" .
                        "Jazakumullah khairan katsiran.\n" .
                        "PT. Zein Internasional";
                } else {
                    // Pesan WhatsApp Cicilan Bertahap Disetujui
                    $waMessage = "Assalamu'alaikum Wr. Wb. Bapak/Ibu {$user->name},\n\n" .
                        "Alhamdulillah, setoran cicilan pelunasan Anda sebesar {$formattedAmount} untuk paket {$package->name} ({$regId}) telah BERHASIL DIVERIFIKASI.\n\n" .
                        "💵 Total Terbayar: {$formattedTotalPaid}\n" .
                        "💰 Sisa Tagihan: {$formattedRemaining}\n\n" .
                        "Terima kasih atas setoran Anda. Pantau saldo tabungan pelunasan Anda di sistem.\n\n" .
                        "PT. Zein Internasional";
                }

                if ($user && !empty($user->phone)) {
                    $this->whatsapp->sendMessage($user->phone, $waMessage);
                }

                DB::commit();

                $successMsg = $isFullyPaid
                    ? "Pembayaran sebesar {$formattedAmount} disetujui. Tagihan telah LUNAS! Status registrasi otomatis berubah menjadi 'Lunas' dan notifikasi WhatsApp ucapan selamat telah dikirim."
                    : "Pembayaran sebesar {$formattedAmount} berhasil disetujui. Sisa tagihan: {$formattedRemaining}. Notifikasi WhatsApp telah dikirim.";

                return back()->with('success', $successMsg);

            } else {
                // Reject Action
                $payment->update([
                    'status' => Payment::STATUS_DITOLAK,
                    'rejection_reason' => trim($request->rejection_reason),
                    'rejected_by' => auth()->id(),
                    'rejected_at' => Carbon::now(),
                    'verified_by' => null,
                    'verified_at' => null,
                ]);

                // Jika DP ditolak, kembalikan status pendaftaran ke menunggu_pembayaran_dp agar jamaah bisa upload ulang
                if ($payment->type === Payment::TYPE_DP) {
                    $registration->update([
                        'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
                    ]);
                }

                DB::commit();

                return back()->with('success', 'Bukti pembayaran ditolak dengan alasan penolakan yang telah dicatat.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses verifikasi pembayaran: ' . $e->getMessage());
        }
    }
}
