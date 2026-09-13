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
use App\Services\PeriodFilterService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard admin - PRD Section 6.11 (Operasional Dasar: angka & tabel ringkasan)
     * & Section 14-15 (Grafik Tren Pendaftaran & Pembayaran Dinamis Berdasarkan Periode).
     */
    public function index(Request $request, PeriodFilterService $periodFilterService)
    {
        // 1. Metrik Utama
        $totalJamaah = User::where('role', 'jamaah')->count();
        
        // Total Pendaftaran Aktif (tidak termasuk yang dibatalkan / selesai / approved cancellation)
        $totalPendaftaran = Registration::whereNotIn('status', [Registration::STATUS_DIBATALKAN, Registration::STATUS_SELESAI])
            ->where(function ($cq) {
                $cq->whereNull('cancellation_status')
                   ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
            })->count();

        // Total Selesai Berangkat / Ibadah (Tahap 9)
        $totalSelesai = Registration::where('status', Registration::STATUS_SELESAI)->count();

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

        // 5. Filter Periode & Dataset Grafik Analitik (PRD Bagian 15)
        $periodData = $periodFilterService->parseRequest($request);
        $startDate = $periodData['start_date'];
        $endDate = $periodData['end_date'];
        $period = $periodData['period'];
        $buckets = $periodFilterService->getBuckets($startDate, $endDate, $period);

        // Ambil pendaftaran dalam rentang periode aktif (berdasarkan tanggal daftar / created_at)
        $registrationsInPeriod = Registration::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', Registration::STATUS_DIBATALKAN)
            ->where(function ($cq) {
                $cq->whereNull('cancellation_status')
                   ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
            })
            ->select('id', 'created_at')
            ->get();

        // Ambil pembayaran yang diverifikasi dalam rentang periode aktif (berdasarkan verified_at)
        $paymentsInPeriod = Payment::where('status', Payment::STATUS_DISETUJUI)
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->select('id', 'amount', 'verified_at')
            ->get();

        $chartLabels = [];
        $registrationChartData = [];
        $paymentChartData = [];

        foreach ($buckets as $bucket) {
            $chartLabels[] = $bucket['label'];
            $bStart = $bucket['start'];
            $bEnd = $bucket['end'];

            $regCount = $registrationsInPeriod->filter(function ($reg) use ($bStart, $bEnd) {
                return $reg->created_at >= $bStart && $reg->created_at <= $bEnd;
            })->count();

            $paySum = (float) $paymentsInPeriod->filter(function ($pay) use ($bStart, $bEnd) {
                return $pay->verified_at >= $bStart && $pay->verified_at <= $bEnd;
            })->sum('amount');

            $registrationChartData[] = $regCount;
            $paymentChartData[] = $paySum;
        }

        $totalPeriodRegistrations = array_sum($registrationChartData);
        $totalPeriodIncome = array_sum($paymentChartData);

        return view('admin.dashboard', [
            'totalJamaah' => $totalJamaah,
            'totalPendaftaran' => $totalPendaftaran,
            'totalSelesai' => $totalSelesai,
            'totalPaketAktif' => $totalPaketAktif,
            'totalIncome' => $totalIncome,
            'totalOutstanding' => $totalOutstanding,
            'pendingDocsCount' => $pendingDocsCount,
            'pendingPaymentsCount' => $pendingPaymentsCount,
            'pendingCancellationsCount' => $pendingCancellationsCount,
            'packagesSummary' => $packagesSummary,
            'upcomingDueInvoices' => $upcomingDueInvoices,
            'recentRegistrations' => $recentRegistrations,
            // Period Filter & Chart Data
            'periodData' => $periodData,
            'chartLabels' => $chartLabels,
            'registrationChartData' => $registrationChartData,
            'paymentChartData' => $paymentChartData,
            'totalPeriodRegistrations' => $totalPeriodRegistrations,
            'totalPeriodIncome' => $totalPeriodIncome,
        ]);
    }
}
