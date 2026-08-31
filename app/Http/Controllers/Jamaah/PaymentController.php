<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Registration;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

    /**
     * Tampilkan halaman formulir upload bukti transfer DP.
     * PRD Section 6.4 & 10
     */
    public function createDp(Request $request)
    {
        $user = $request->user();
        $registration = $user->activeRegistration();

        if (!$registration) {
            return redirect()->route('jamaah.dashboard')
                ->with('error', 'Anda belum memiliki pendaftaran aktif.');
        }

        // Hanya bisa bayar jika status menunggu_pembayaran_dp
        if (!in_array($registration->status, [Registration::STATUS_MENUNGGU_PEMBAYARAN_DP, Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP])) {
            return redirect()->route('jamaah.my-registration')
                ->with('warning', 'Pendaftaran Anda saat ini belum berada di tahap pembayaran DP.');
        }

        $registration->load(['package', 'members', 'invoice', 'payments']);

        // Default nominal DP: Rp 5.000.000 x jumlah jamaah (PRD Section 10)
        $defaultDpPerPax = 5000000;
        $recommendedDp = $defaultDpPerPax * $registration->members->count();

        // Cari pembayaran DP terakhir yang ditolak (jika ada)
        $lastRejectedDp = $registration->payments
            ->where('type', Payment::TYPE_DP)
            ->where('status', Payment::STATUS_DITOLAK)
            ->last();

        return view('jamaah.payment.dp', [
            'registration' => $registration,
            'recommendedDp' => $recommendedDp,
            'lastRejectedDp' => $lastRejectedDp,
        ]);
    }

    /**
     * Simpan bukti transfer DP.
     * PRD Section 6.4 & 11:
     * Upload Bukti DP → Status Pendaftaran berubah menjadi 'menunggu_verifikasi_pembayaran_dp'
     */
    public function storeDp(StorePaymentRequest $request)
    {
        $user = $request->user();
        $registration = $user->activeRegistration();

        if (!$registration) {
            return redirect()->route('jamaah.dashboard')
                ->with('error', 'Pendaftaran aktif tidak ditemukan.');
        }

        // Upload & Kompres bukti transfer DP
        $proofPath = $this->imageUploadService->uploadDocument($request->file('proof_file'), 'payments/dp');

        Payment::create([
            'registration_id' => $registration->id,
            'type' => Payment::TYPE_DP,
            'amount' => $request->amount,
            'proof_file' => $proofPath,
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        // Perbarui status pendaftaran ke Tahap 3: Menunggu Verifikasi Pembayaran DP
        $registration->update([
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP,
        ]);

        return redirect()->route('jamaah.my-registration')
            ->with('success', 'Bukti transfer DP berhasil dikirim! Tim keuangan kami akan memverifikasi pembayaran Anda.');
    }

    /**
     * Tampilkan formulir pelunasan bertahap / cicilan tabungan umrah.
     * PRD Section 6.5:
     * - Nominal bebas
     * - Upload bukti berkali-kali
     * - Jamaah hanya melihat total_paid & remaining_balance
     */
    public function createPelunasan(Request $request)
    {
        $user = $request->user();
        $registration = $user->activeRegistration();

        if (!$registration) {
            return redirect()->route('jamaah.dashboard')
                ->with('error', 'Anda belum memiliki pendaftaran aktif.');
        }

        if (!in_array($registration->status, [Registration::STATUS_JAMAAH, Registration::STATUS_CICILAN_PELUNASAN])) {
            return redirect()->route('jamaah.my-registration')
                ->with('warning', 'Pelunasan bertahap hanya dapat dilakukan setelah pembayaran DP disetujui.');
        }

        $registration->load(['package', 'members', 'invoice', 'payments']);

        if ($registration->invoice && $registration->invoice->remaining_balance <= 0) {
            return redirect()->route('jamaah.my-registration')
                ->with('success', 'Biaya paket Anda sudah lunas sepenuhnya!');
        }

        // Cari pembayaran pelunasan terakhir yang ditolak (jika ada)
        $lastRejectedPelunasan = $registration->payments
            ->where('type', Payment::TYPE_PELUNASAN)
            ->where('status', Payment::STATUS_DITOLAK)
            ->last();

        return view('jamaah.payment.pelunasan', [
            'registration' => $registration,
            'invoice' => $registration->invoice,
            'lastRejectedPelunasan' => $lastRejectedPelunasan,
        ]);
    }

    /**
     * Simpan setoran pelunasan / cicilan.
     * PRD Section 6.5
     */
    public function storePelunasan(Request $request)
    {
        $user = $request->user();
        $registration = $user->activeRegistration();

        if (!$registration) {
            return redirect()->route('jamaah.dashboard')
                ->with('error', 'Pendaftaran aktif tidak ditemukan.');
        }

        $invoice = $registration->invoice;
        $maxAmount = $invoice ? (float) $invoice->remaining_balance : 100000000;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:100000', 'max:' . $maxAmount],
            'proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'amount.required' => 'Nominal transfer pelunasan wajib diisi.',
            'amount.min' => 'Nominal transfer minimal Rp 100.000.',
            'amount.max' => 'Nominal transfer tidak boleh melebihi sisa tagihan (Rp ' . number_format($maxAmount, 0, ',', '.') . ').',
            'proof_file.required' => 'Foto atau scan bukti transfer pelunasan wajib diunggah.',
            'proof_file.mimes' => 'Format file harus berupa JPG, JPEG, PNG, atau WEBP.',
            'proof_file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        // Upload & Kompres bukti setoran pelunasan
        $proofPath = $this->imageUploadService->uploadDocument($request->file('proof_file'), 'payments/pelunasan');

        Payment::create([
            'registration_id' => $registration->id,
            'type' => Payment::TYPE_PELUNASAN,
            'amount' => $request->amount,
            'proof_file' => $proofPath,
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        return redirect()->route('jamaah.my-registration')
            ->with('success', 'Bukti setoran pelunasan sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil dikirim! Saldo total Anda akan diperbarui setelah verifikasi admin.');
    }
}
