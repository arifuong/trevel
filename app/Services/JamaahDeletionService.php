<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JamaahDeletionService
{
    /**
     * Hapus akun jamaah dan seluruh data terkait secara aman (dengan Transaction & Storage Clean).
     *
     * @param User $user
     * @param int|null $adminId ID administrator yang melakukan aksi
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteJamaah(User $user, ?int $adminId = null): array
    {
        // 1. Guard check: Jangan pernah menghapus akun admin atau diri sendiri
        if ($user->role === 'admin' || ($adminId && $user->id === $adminId)) {
            return [
                'success' => false,
                'message' => 'Akun Administrator tidak dapat dihapus.',
            ];
        }

        try {
            $userName = $user->name;
            $userId = $user->id;
            $filesToDelete = [];

            // 2. Kumpulkan file profil user
            if ($user->avatar) {
                $filesToDelete[] = $user->avatar;
            }

            // 3. Kumpulkan file dokumen jamaah & bukti pembayaran pada setiap pendaftaran
            $user->load([
                'registrations.members.verificationHistories',
                'registrations.payments',
                'registrations.invoice',
                'registrations.cancellations',
            ]);

            foreach ($user->registrations as $registration) {
                // File dokumen anggota jamaah
                foreach ($registration->members as $member) {
                    $docFields = ['ktp_file', 'kk_file', 'passport_file', 'marriage_book_file', 'birth_certificate_file'];
                    foreach ($docFields as $docField) {
                        if ($member->{$docField}) {
                            $filesToDelete[] = $member->{$docField};
                        }
                    }
                }

                // File bukti transfer pembayaran
                foreach ($registration->payments as $payment) {
                    if ($payment->proof_file) {
                        $filesToDelete[] = $payment->proof_file;
                    }
                }
            }

            // 4. Eksekusi penghapusan database dalam transaksi atomik
            DB::transaction(function () use ($user) {
                foreach ($user->registrations as $registration) {
                    // Hapus riwayat verifikasi dokumen & data anggota jamaah
                    foreach ($registration->members as $member) {
                        $member->verificationHistories()->delete();
                        $member->delete();
                    }

                    // Hapus riwayat pembayaran
                    $registration->payments()->delete();

                    // Hapus invoice/tagihan
                    if ($registration->invoice) {
                        $registration->invoice->delete();
                    }

                    // Hapus data pembatalan jika ada
                    $registration->cancellations()->delete();

                    // Hapus pendaftaran
                    $registration->delete();
                }

                // Hapus record user
                $user->delete();
            });

            // 5. Bersihkan file fisik storage HANYA milik user yang dihapus
            foreach ($filesToDelete as $filePath) {
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            // 6. Audit Logging
            $executor = $adminId ? "Admin ID {$adminId}" : "System/Admin";
            Log::info("{$executor} deleted Jamaah ID {$userId} ({$userName}) at " . now()->toIso8601String());

            return [
                'success' => true,
                'message' => "Akun jamaah '{$userName}' dan seluruh data terkait berhasil dihapus secara aman.",
            ];

        } catch (\Throwable $e) {
            Log::error("Gagal menghapus jamaah ID {$user->id}: " . $e->getMessage(), ['exception' => $e]);
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data jamaah. Silakan coba lagi.',
            ];
        }
    }
}
