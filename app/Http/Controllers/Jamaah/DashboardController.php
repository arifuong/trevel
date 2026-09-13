<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard jamaah - halaman utama setelah login dengan 3 kondisi:
     * - Kondisi A: Ada booking aktif (status bukan 'selesai')
     * - Kondisi B: Semua booking sudah 'selesai' (punya riwayat)
     * - Kondisi C: Belum pernah booking (akun baru)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Ambil seluruh pendaftaran aktif (status BUKAN 'selesai' dan BUKAN 'dibatalkan')
        $activeRegistrations = $user->registrations()
            ->whereNotIn('status', [Registration::STATUS_SELESAI, Registration::STATUS_DIBATALKAN])
            ->with(['package', 'packageVariant', 'members', 'payments', 'invoice', 'latestCancellation'])
            ->get();

        // Urutkan pendaftaran aktif berdasarkan prioritas perhatian:
        // Menunggu verifikasi dokumen / pembayaran di atas
        $priorityMap = [
            Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN => 1,
            Registration::STATUS_MENUNGGU_PEMBAYARAN_DP => 2,
            Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP => 3,
            Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN => 4,
            Registration::STATUS_CICILAN_PELUNASAN => 5,
            Registration::STATUS_JAMAAH => 6,
            Registration::STATUS_LUNAS => 7,
            Registration::STATUS_BERANGKAT => 8,
        ];

        $sortedActiveRegistrations = $activeRegistrations->sortBy(function ($reg) use ($priorityMap) {
            return $priorityMap[$reg->status] ?? 99;
        })->values();

        // 2. Ambil pendaftaran yang sudah selesai
        $completedRegistrations = $user->registrations()
            ->where('status', Registration::STATUS_SELESAI)
            ->with(['package', 'packageVariant', 'members', 'payments', 'invoice'])
            ->latest('updated_at')
            ->get();

        $latestCompleted = $completedRegistrations->first();

        // 3. Tentukan State / Kondisi Dashboard
        if ($sortedActiveRegistrations->isNotEmpty()) {
            $dashboardState = 'A'; // Kondisi A: Ada booking aktif
        } elseif ($latestCompleted !== null) {
            $dashboardState = 'B'; // Kondisi B: Semua selesai, punya riwayat
        } else {
            $dashboardState = 'C'; // Kondisi C: Belum pernah booking
        }

        // Pendaftaran utama untuk backward-compatibility view dan single-card display
        $primaryRegistration = $sortedActiveRegistrations->first() ?? $latestCompleted;

        return view('jamaah.dashboard', [
            'user' => $user,
            'dashboardState' => $dashboardState,
            'activeRegistrations' => $sortedActiveRegistrations,
            'registration' => $primaryRegistration,
            'latestCompleted' => $latestCompleted,
            'completedCount' => $completedRegistrations->count(),
        ]);
    }
}
