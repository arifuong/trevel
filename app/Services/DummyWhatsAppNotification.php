<?php

namespace App\Services;

use App\Contracts\WhatsAppNotificationInterface;
use Illuminate\Support\Facades\Log;

/**
 * Implementasi notifikasi WhatsApp sistem untuk development & testing.
 * Log pesan notifikasi ke storage/logs/laravel.log.
 */
class DummyWhatsAppNotification implements WhatsAppNotificationInterface
{
    public function sendMessage(string $phoneNumber, string $message): bool
    {
        Log::channel('single')->info("[WhatsApp Notification] Pesan ke {$phoneNumber}:\n{$message}");

        return true;
    }
}
