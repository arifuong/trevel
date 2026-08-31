<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\WhatsappOtpInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function __construct(
        private WhatsappOtpInterface $whatsapp
    ) {}

    /**
     * Tampilkan form registrasi.
     */
    public function showForm()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi & kirim OTP.
     * PRD Section 6.1: Isi data → verifikasi OTP WhatsApp → akun aktif
     */
    public function register(RegisterRequest $request)
    {
        $phone = $request->phone;
        // Normalisasi: 08xxx → 628xxx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $phone,
            'password' => $request->password,
            'role' => 'jamaah',
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $this->whatsapp->sendOtp($phone, $otp);

        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.verify')->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda.');
    }
}
