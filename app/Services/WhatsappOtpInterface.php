<?php

namespace App\Services;

interface WhatsappOtpInterface
{
    /**
     * Kirim OTP ke nomor WhatsApp.
     *
     * @param string $phone Nomor HP (format: 628xxx)
     * @param string $otp Kode OTP
     * @return bool Berhasil atau tidak
     */
    public function sendOtp(string $phone, string $otp): bool;

    /**
     * Kirim pesan notifikasi umum ke nomor WhatsApp.
     * PRD Section 6.8: Notifikasi otomatis WhatsApp
     *
     * @param string $phone Nomor HP (format: 628xxx)
     * @param string $message Isi Pesan
     * @return bool Berhasil atau tidak
     */
    public function sendMessage(string $phone, string $message): bool;
}
