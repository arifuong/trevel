<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Registration;
use App\Models\RegistrationCancellation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancellationController extends Controller
{

    /**
     * Tampilkan halaman konfirmasi pembatalan pendaftaran dengan perhitungan fee.
     * PRD Section 6.10 & 8 Business Rules
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $registration = $user->activeRegistration();

        if (!$registration) {
            return redirect()->route('jamaah.dashboard')
                ->with('error', 'Anda tidak memiliki pendaftaran aktif.');
        }

        $registration->load(['package', 'members', 'invoice', 'payments', 'latestCancellation']);
        $feeCalculation = $registration->calculateCancellationFee();

        return view('jamaah.registration.cancel', [
            'registration' => $registration,
            'fee' => $feeCalculation,
            'latestCancellation' => $registration->latestCancellation,
        ]);
    }

    /**
     * Ajukan pembatalan pendaftaran oleh jamaah (Status: Menunggu Validasi Admin).
     * PRD Section 6.10 & 8:
     * - Menyimpan pengajuan pembatalan ke tabel registration_cancellations
     * - Status pembatalan menjadi 'pending'
     * - Menunggu konfirmasi / validasi Admin
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $registration = $user->activeRegistration();

        if (!$registration) {
            return redirect()->route('jamaah.dashboard')
                ->with('error', 'Pendaftaran aktif tidak ditemukan.');
        }

        // Cek jika sudah ada pengajuan pembatalan yang sedang pending
        if ($registration->hasPendingCancellation()) {
            return redirect()->route('jamaah.my-registration')
                ->with('warning', 'Pengajuan pembatalan Anda sebelumnya sedang dalam proses validasi Admin.');
        }

        $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:5', 'max:1000'],
            'agree_terms' => ['accepted'],
        ], [
            'cancellation_reason.required' => 'Alasan pembatalan pendaftaran wajib diisi.',
            'cancellation_reason.min' => 'Alasan pembatalan minimal 5 karakter.',
            'cancellation_reason.max' => 'Alasan pembatalan maksimal 1000 karakter.',
            'agree_terms.accepted' => 'Anda harus menyetujui ketentuan dan konsekuensi pembatalan.',
        ]);

        $registration->load(['package', 'members', 'invoice']);
        $fee = $registration->calculateCancellationFee();

        DB::beginTransaction();

        try {
            // 1. Buat record pengajuan pembatalan dengan status pending
            RegistrationCancellation::create([
                'registration_id' => $registration->id,
                'user_id' => $user->id,
                'reason' => trim($request->cancellation_reason),
                'fee_amount' => $fee['fee_amount'],
                'fee_percentage' => $fee['fee_percentage'],
                'refund_amount' => $fee['refund_amount'],
                'category' => $fee['category'],
                'status' => RegistrationCancellation::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            // 2. Set cancellation_status pada registrasi menjadi 'pending'
            $registration->update([
                'cancellation_status' => RegistrationCancellation::STATUS_PENDING,
            ]);

            DB::commit();

            return redirect()->route('jamaah.my-registration')
                ->with('success', 'Pengajuan pembatalan pendaftaran Anda telah berhasil dikirim dan sedang menunggu validasi dari Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengajukan pembatalan: ' . $e->getMessage());
        }
    }
}
