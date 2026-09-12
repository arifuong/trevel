<?php

namespace App\Contracts;

interface WhatsAppNotificationInterface
{
    /**
     * Kirim pesan notifikasi WhatsApp ke nomor tujuan.
     *
     * @param string $phoneNumber Nomor HP tujuan
     * @param string $message Isi pesan notifikasi
     * @return bool True jika berhasil dikirim/di-log
     */
    public function sendMessage(string $phoneNumber, string $message): bool;
}
