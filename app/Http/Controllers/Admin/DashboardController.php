<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationCancellation;
use App\Models\RegistrationMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard admin - PRD Section 6.11 (Operasional Dasar: angka & tabel ringkasan).
     */
    public function index()
    {
        // 1. Metrik Utama
        $totalJamaah = User::where('role', 'jamaah')->count();
        
        // Total Pendaftaran Aktif (tidak termasuk yang dibatalkan / approved cancellation)
        $totalPendaftaran = Registration::where('status', '!=', Registration::STATUS_DIBATALKAN)
            ->where(function ($cq) {
                $cq->whereNull('cancellation_status')
                   ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
            })->count();

        $totalPaketAktif = Package::where('status', Package::STATUS_AKTIF)->count();
        
        // Dana Masuk dari pendaftaran aktif
        $totalIncome = (float) Invoice::whereHas('registration', function ($q) {
            $q->where('status', '!=', Registration::STATUS_DIBATALKAN)
              ->where(function ($cq) {
                  $cq->whereNull('cancellation_status')
                     ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
              });
        })->sum('total_paid');
        
        // Sisa piutang aktif: hanya hitung pendaftaran yang tidak dibatalkan
        $totalOutstanding = (float) Invoice::whereHas('registration', function ($q) {
            $q->where('status', '!=', Registration::STATUS_DIBATALKAN)
              ->where(function ($cq) {
                  $cq->whereNull('cancellation_status')
                     ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
              });
        })->sum('remaining_balance');

        $pendingDocsCount = RegistrationMember::where('document_status', RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI)
            ->whereHas('registration', function ($q) {
                $q->where('status', '!=', Registration::STATUS_DIBATALKAN)
                  ->where(function ($cq) {
                      $cq->whereNull('cancellation_status')
                         ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
                  });
            })->count();

        $pendingPaymentsCount = Payment::where('status', Payment::STATUS_MENUNGGU_VERIFIKASI)
            ->whereHas('registration', function ($q) {
                $q->where('status', '!=', Registration::STATUS_DIBATALKAN)
                  ->where(function ($cq) {
                      $cq->whereNull('cancellation_status')
                         ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
                  });
            })->count();

        $pendingCancellationsCount = RegistrationCancellation::where('status', RegistrationCancellation::STATUS_PENDING)->count();

        // 2. Ringkasan Pendaftar per Paket (Tabel 1)
        $packagesSummary = Package::withCount(['registrations as active_registrations_count' => function ($q) {
            $q->where('status', '!=', Registration::STATUS_DIBATALKAN)
              ->where(function ($cq) {
                  $cq->whereNull('cancellation_status')
                     ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
              });
        }])->get()->map(function ($pkg) {
            $registrations = $pkg->registrations()
                ->where('status', '!=', Registration::STATUS_DIBATALKAN)
                ->where(function ($cq) {
                    $cq->whereNull('cancellation_status')
                       ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
                })
                ->with('members', 'invoice')
                ->get();
            $totalPax = $registrations->sum(fn ($r) => $r->members->count());
            $totalPaid = $registrations->sum(fn ($r) => (float) ($r->invoice->total_paid ?? 0));
            $totalPrice = $registrations->sum(fn ($r) => (float) ($r->invoice->total_price ?? 0));

            return (object) [
                'id' => $pkg->id,
                'name' => $pkg->name,
                'quota' => $pkg->quota,
                'status' => $pkg->status,
                'departure_date' => $pkg->departure_date,
                'pax_count' => $totalPax,
                'registrations_count' => $registrations->count(),
                'total_paid' => $totalPaid,
                'total_price' => $totalPrice,
            ];
        });

        // 3. Jamaah Mendekati Jatuh Tempo (Tabel 2: Due Date Alert)
        // STRICT: Jangan tampilkan jamaah yang pembatalannya sudah disetujui
        $upcomingDueInvoices = Invoice::with(['registration.user', 'registration.package'])
            ->where('remaining_balance', '>', 0)
            ->whereHas('registration', function ($q) {
                $q->where('status', '!=', Registration::STATUS_DIBATALKAN)
                  ->where(function ($cq) {
                      $cq->whereNull('cancellation_status')
                         ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
                  });
            })
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->take(8)
            ->get();

        // 4. Pendaftaran Terkini (Tabel 3)
        $recentRegistrations = Registration::with(['user', 'package', 'members', 'invoice'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', [
            'totalJamaah' => $totalJamaah,
            'totalPendaftaran' => $totalPendaftaran,
            'totalPaketAktif' => $totalPaketAktif,
            'totalIncome' => $totalIncome,
            'totalOutstanding' => $totalOutstanding,
            'pendingDocsCount' => $pendingDocsCount,
            'pendingPaymentsCount' => $pendingPaymentsCount,
            'pendingCancellationsCount' => $pendingCancellationsCount,
            'packagesSummary' => $packagesSummary,
            'upcomingDueInvoices' => $upcomingDueInvoices,
            'recentRegistrations' => $recentRegistrations,
        ]);
    }
}
