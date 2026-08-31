<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsappOtpInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    public function __construct(
        private WhatsappOtpInterface $whatsapp
    ) {}

    /**
     * Tampilkan form verifikasi OTP.
     */
    public function showForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('register');
        }

        $user = User::find(session('otp_user_id'));
        if (!$user) {
            return redirect()->route('register');
        }

        return view('auth.otp-verify', [
            'phone' => $user->phone,
        ]);
    }

    /**
     * Verifikasi kode OTP.
     * PRD Section 6.1: OTP benar → akun terverifikasi
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus 6 digit.',
        ]);

        $user = User::find(session('otp_user_id'));

        if (!$user) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran tidak ditemukan. Silakan daftar ulang.');
        }

        // Cek OTP kedaluwarsa
        if ($user->otp_expires_at && Carbon::now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang.']);
        }

        // Cek kecocokan OTP
        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan coba lagi.']);
        }

        // OTP benar → aktifkan akun
        $user->update([
            'phone_verified_at' => Carbon::now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        session()->forget('otp_user_id');

        Auth::login($user);

        return redirect()->route('jamaah.dashboard')
            ->with('success', 'Selamat! Akun Anda berhasil diverifikasi.');
    }

    /**
     * Kirim ulang OTP.
     * PRD Section 6.1: OTP salah/kedaluwarsa → kirim ulang OTP
     */
    public function resend()
    {
        $user = User::find(session('otp_user_id'));

        if (!$user) {
            return redirect()->route('register');
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $this->whatsapp->sendOtp($user->phone, $otp);

        return back()->with('success', 'Kode OTP baru telah dikirim ke WhatsApp Anda.');
    }
}
