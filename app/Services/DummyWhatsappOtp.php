<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Implementasi dummy WhatsApp OTP & Notifikasi.
 * Log pesan ke storage/logs untuk testing.
 * Ganti dengan provider WhatsApp API asli (Fonnte, Wablas, dll) untuk produksi.
 */
class DummyWhatsappOtp implements WhatsappOtpInterface
{
    public function sendOtp(string $phone, string $otp): bool
    {
        Log::channel('single')->info("[WhatsApp OTP] Kode OTP untuk {$phone}: {$otp}");

        return true;
    }

    public function sendMessage(string $phone, string $message): bool
    {
        Log::channel('single')->info("[WhatsApp Notification] Pesan ke {$phone}:\n{$message}");

        return true;
    }
}
