<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationCancellation;
use App\Services\PeriodFilterService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected PeriodFilterService $periodFilterService
    ) {}

    /**
     * Halaman Utama Laporan Terpadu (Multi-Tab: Jamaah / Pembayaran / Piutang / Keberangkatan)
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'jamaah');
        if (!in_array($tab, ['jamaah', 'pembayaran', 'piutang', 'keberangkatan'])) {
            $tab = 'jamaah';
        }

        $periodData = $this->periodFilterService->parse($request);
        $startDate = $periodData['startDate'];
        $endDate = $periodData['endDate'];

        $viewData = [
            'tab' => $tab,
            'periodData' => $periodData,
        ];

        if ($tab === 'jamaah') {
            $query = Registration::with(['user', 'package', 'packageVariant', 'members', 'invoice'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->latest('created_at');

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                    })->orWhereHas('package', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    })->orWhere('registration_number', 'like', "%{$search}%");
                });
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

            $baseQuery = Registration::whereBetween('created_at', [$startDate, $endDate]);
            $totalRegistrations = (clone $baseQuery)->count();
            $totalPax = DB::table('registration_members')
                ->join('registrations', 'registration_members.registration_id', '=', 'registrations.id')
                ->whereBetween('registrations.created_at', [$startDate, $endDate])
                ->count();

            $totalInvoiceValue = DB::table('invoices')
                ->join('registrations', 'invoices.registration_id', '=', 'registrations.id')
                ->whereBetween('registrations.created_at', [$startDate, $endDate])
                ->where('registrations.status', '!=', Registration::STATUS_DIBATALKAN)
                ->sum('invoices.total_price');

            $activePaxCount = DB::table('registration_members')
                ->join('registrations', 'registration_members.registration_id', '=', 'registrations.id')
                ->whereBetween('registrations.created_at', [$startDate, $endDate])
                ->whereIn('registrations.status', [
                    Registration::STATUS_JAMAAH,
                    Registration::STATUS_CICILAN_PELUNASAN,
                    Registration::STATUS_LUNAS,
                    Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
                    Registration::STATUS_BERANGKAT,
                    Registration::STATUS_SELESAI,
                ])
                ->count();

            $registrations = $query->paginate(15)->withQueryString();

            $exportParams = array_merge($periodData['queryParams'], [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
            ]);

            $viewData['registrations'] = $registrations;
            $viewData['summary'] = [
                'totalRegistrations' => $totalRegistrations,
                'totalPax' => $totalPax,
                'totalInvoiceValue' => $totalInvoiceValue,
                'activePaxCount' => $activePaxCount,
            ];
            $viewData['exportCsvUrl'] = route('admin.reports.jamaah.export', array_merge($exportParams, ['format' => 'csv']));
            $viewData['exportPdfUrl'] = route('admin.reports.jamaah.export', array_merge($exportParams, ['format' => 'pdf']));

        } elseif ($tab === 'pembayaran') {
            $status = $request->input('status', Payment::STATUS_DISETUJUI);

            $query = Payment::with(['registration.user', 'registration.package'])
                ->latest('verified_at');

            if ($status === Payment::STATUS_DISETUJUI) {
                $query->where('status', Payment::STATUS_DISETUJUI)
                      ->whereBetween('verified_at', [$startDate, $endDate]);
            } elseif ($status === 'all') {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } else {
                $query->where('status', $status)
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('receipt_number', 'like', "%{$search}%")
                      ->orWhereHas('registration.user', function ($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      })->orWhereHas('registration', function ($rq) use ($search) {
                          $rq->where('registration_number', 'like', "%{$search}%");
                      });
                });
            }

            $verifiedPayments = Payment::where('status', Payment::STATUS_DISETUJUI)
                ->whereBetween('verified_at', [$startDate, $endDate]);

            $totalVerifiedAmount = (clone $verifiedPayments)->sum('amount');
            $totalTransactions = (clone $verifiedPayments)->count();
            $totalDpAmount = (clone $verifiedPayments)->where('type', Payment::TYPE_DP)->sum('amount');
            $totalPelunasanAmount = (clone $verifiedPayments)->where('type', Payment::TYPE_PELUNASAN)->sum('amount');

            $payments = $query->paginate(15)->withQueryString();

            $exportParams = array_merge($periodData['queryParams'], [
                'search' => $request->input('search'),
                'status' => $status,
            ]);

            $viewData['payments'] = $payments;
            $viewData['currentStatus'] = $status;
            $viewData['summary'] = [
                'totalVerifiedAmount' => $totalVerifiedAmount,
                'totalTransactions' => $totalTransactions,
                'totalDpAmount' => $totalDpAmount,
                'totalPelunasanAmount' => $totalPelunasanAmount,
            ];
            $viewData['exportCsvUrl'] = route('admin.reports.payments.export', array_merge($exportParams, ['format' => 'csv']));
            $viewData['exportPdfUrl'] = route('admin.reports.payments.export', array_merge($exportParams, ['format' => 'pdf']));

        } elseif ($tab === 'piutang') {
            $query = Registration::with(['user', 'package', 'members', 'invoice'])
                ->whereHas('package', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('departure_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })
                ->where('status', '!=', Registration::STATUS_DIBATALKAN)
                ->where(function ($cq) {
                    $cq->whereNull('cancellation_status')
                       ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
                })
                ->orderBy('created_at', 'desc');

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                    })->orWhereHas('package', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    })->orWhere('registration_number', 'like', "%{$search}%");
                });
            }

            $allMatchingRegs = (clone $query)->get();
            $totalContract = $allMatchingRegs->sum(fn ($r) => (float) ($r->invoice->total_price ?? 0));
            $totalReceived = $allMatchingRegs->sum(fn ($r) => (float) ($r->invoice->total_paid ?? 0));
            $totalOutstanding = $allMatchingRegs->sum(fn ($r) => (float) ($r->invoice->remaining_balance ?? 0));
            $unpaidCount = $allMatchingRegs->filter(fn ($r) => ($r->invoice->remaining_balance ?? 0) > 0)->count();

            $registrations = $query->paginate(15)->withQueryString();

            $exportParams = array_merge($periodData['queryParams'], [
                'search' => $request->input('search'),
            ]);

            $viewData['registrations'] = $registrations;
            $viewData['summary'] = [
                'totalContract' => $totalContract,
                'totalReceived' => $totalReceived,
                'totalOutstanding' => $totalOutstanding,
                'unpaidCount' => $unpaidCount,
            ];
            $viewData['exportCsvUrl'] = route('admin.reports.receivables.export', array_merge($exportParams, ['format' => 'csv']));
            $viewData['exportPdfUrl'] = route('admin.reports.receivables.export', array_merge($exportParams, ['format' => 'pdf']));

        } elseif ($tab === 'keberangkatan') {
            $query = Package::whereBetween('departure_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->with(['variants.airlines', 'variants.hotelMakkah', 'variants.hotelMadinah'])
                ->orderBy('departure_date', 'asc');

            if ($search = $request->input('search')) {
                $query->where('name', 'like', "%{$search}%");
            }

            $allPackages = (clone $query)->get()->map(function ($pkg) {
                $activeRegs = $pkg->registrations()
                    ->where('status', '!=', Registration::STATUS_DIBATALKAN)
                    ->where(function ($cq) {
                        $cq->whereNull('cancellation_status')
                           ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
                    })
                    ->with('members')
                    ->get();

                $paxCount = $activeRegs->sum(fn ($r) => $r->members->count());
                $remainingQuota = max(0, $pkg->quota - $paxCount);
                $fillPercentage = $pkg->quota > 0 ? min(100, round(($paxCount / $pkg->quota) * 100)) : 0;

                $pkg->filled_pax = $paxCount;
                $pkg->remaining_quota = $remainingQuota;
                $pkg->fill_percentage = $fillPercentage;

                return $pkg;
            });

            $totalDepartures = $allPackages->count();
            $totalCapacity = $allPackages->sum('quota');
            $totalRegisteredPax = $allPackages->sum('filled_pax');
            $totalRemainingSeats = $allPackages->sum('remaining_quota');

            $packages = $query->paginate(15)->withQueryString();
            $packages->getCollection()->transform(function ($pkg) {
                $activeRegs = $pkg->registrations()
                    ->where('status', '!=', Registration::STATUS_DIBATALKAN)
                    ->where(function ($cq) {
                        $cq->whereNull('cancellation_status')
                           ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
                    })
                    ->with('members')
                    ->get();

                $paxCount = $activeRegs->sum(fn ($r) => $r->members->count());
                $pkg->filled_pax = $paxCount;
                $pkg->remaining_quota = max(0, $pkg->quota - $paxCount);
                $pkg->fill_percentage = $pkg->quota > 0 ? min(100, round(($paxCount / $pkg->quota) * 100)) : 0;

                return $pkg;
            });

            $exportParams = array_merge($periodData['queryParams'], [
                'search' => $request->input('search'),
            ]);

            $viewData['packages'] = $packages;
            $viewData['summary'] = [
                'totalDepartures' => $totalDepartures,
                'totalCapacity' => $totalCapacity,
                'totalRegisteredPax' => $totalRegisteredPax,
                'totalRemainingSeats' => $totalRemainingSeats,
            ];
            $viewData['exportCsvUrl'] = route('admin.reports.departures.export', array_merge($exportParams, ['format' => 'csv']));
            $viewData['exportPdfUrl'] = route('admin.reports.departures.export', array_merge($exportParams, ['format' => 'pdf']));
        }

        if ($request->ajax()) {
            return view('admin.reports.partials.' . $tab, $viewData);
        }

        return view('admin.reports.index', $viewData);
    }

    /**
     * Alias method untuk kompatibilitas route / test lama
     */
    public function jamaah(Request $request)
    {
        $request->merge(['tab' => 'jamaah']);
        return $this->index($request);
    }

    public function payments(Request $request)
    {
        $request->merge(['tab' => 'pembayaran']);
        return $this->index($request);
    }

    public function receivables(Request $request)
    {
        $request->merge(['tab' => 'piutang']);
        return $this->index($request);
    }

    public function departures(Request $request)
    {
        $request->merge(['tab' => 'keberangkatan']);
        return $this->index($request);
    }

    /**
     * Ekspor Laporan Jamaah (CSV / PDF)
     */
    public function exportJamaah(Request $request, string $format = 'csv')
    {
        $periodData = $this->periodFilterService->parse($request);
        $startDate = $periodData['startDate'];
        $endDate = $periodData['endDate'];

        $query = Registration::with(['user', 'package', 'packageVariant', 'members', 'invoice'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('package', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $items = $query->get();
        $filename = 'Laporan_Pendaftaran_Jamaah_' . $periodData['startDate']->format('Ymd') . '_' . $periodData['endDate']->format('Ymd');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.pdf_jamaah', [
                'items' => $items,
                'periodData' => $periodData,
                'generatedAt' => now(),
            ])->setPaper('a4', 'landscape');

            return $pdf->download($filename . '.pdf');
        }

        // CSV Export
        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM untuk Microsoft Excel
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No.', 'No. Pendaftaran', 'Tanggal Daftar', 'Nama Pemesan',
                'Email', 'No. HP', 'Paket Umrah/Haji', 'Varian', 'Kamar',
                'Jumlah Jamaah', 'Status', 'Total Biaya (Rp)', 'Sudah Bayar (Rp)', 'Sisa Piutang (Rp)'
            ]);

            foreach ($items as $idx => $reg) {
                fputcsv($handle, [
                    $idx + 1,
                    $reg->registration_number,
                    $reg->created_at->format('d/m/Y H:i'),
                    $reg->user->name ?? '-',
                    $reg->user->email ?? '-',
                    $reg->user->phone ?? '-',
                    $reg->package->name ?? '-',
                    $reg->packageVariant->name ?? 'Standar',
                    ucfirst($reg->room_type ?? 'Quad'),
                    $reg->members->count(),
                    $reg->status_label,
                    $reg->invoice->total_price ?? 0,
                    $reg->invoice->total_paid ?? 0,
                    $reg->invoice->remaining_balance ?? 0,
                ]);
            }
            fclose($handle);
        }, $filename . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }

    /**
     * Ekspor Laporan Pembayaran (CSV / PDF)
     */
    public function exportPayments(Request $request, string $format = 'csv')
    {
        $periodData = $this->periodFilterService->parse($request);
        $startDate = $periodData['startDate'];
        $endDate = $periodData['endDate'];
        $status = $request->input('status', Payment::STATUS_DISETUJUI);

        $query = Payment::with(['registration.user', 'registration.package'])
            ->latest('verified_at');

        if ($status === Payment::STATUS_DISETUJUI) {
            $query->where('status', Payment::STATUS_DISETUJUI)
                  ->whereBetween('verified_at', [$startDate, $endDate]);
        } elseif ($status === 'all') {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $query->where('status', $status)
                  ->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('registration.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $items = $query->get();
        $filename = 'Laporan_Pembayaran_' . $periodData['startDate']->format('Ymd') . '_' . $periodData['endDate']->format('Ymd');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.pdf_payments', [
                'items' => $items,
                'periodData' => $periodData,
                'generatedAt' => now(),
            ])->setPaper('a4', 'landscape');

            return $pdf->download($filename . '.pdf');
        }

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No.', 'No. Kwitansi', 'Tanggal Verifikasi', 'Tanggal Upload',
                'No. Pendaftaran', 'Nama Jamaah', 'Paket Umrah', 'Jenis Pembayaran',
                'Nominal (Rp)', 'Status Verifikasi'
            ]);

            foreach ($items as $idx => $p) {
                fputcsv($handle, [
                    $idx + 1,
                    $p->receipt_number ?? '-',
                    $p->verified_at ? $p->verified_at->format('d/m/Y H:i') : '-',
                    $p->created_at->format('d/m/Y H:i'),
                    $p->registration->registration_number ?? '-',
                    $p->registration->user->name ?? '-',
                    $p->registration->package->name ?? '-',
                    $p->type_label,
                    $p->amount,
                    $p->status_label,
                ]);
            }
            fclose($handle);
        }, $filename . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }

    /**
     * Ekspor Laporan Piutang (CSV / PDF)
     */
    public function exportReceivables(Request $request, string $format = 'csv')
    {
        $periodData = $this->periodFilterService->parse($request);
        $startDate = $periodData['startDate'];
        $endDate = $periodData['endDate'];

        $query = Registration::with(['user', 'package', 'members', 'invoice'])
            ->whereHas('package', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('departure_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->where('status', '!=', Registration::STATUS_DIBATALKAN)
            ->where(function ($cq) {
                $cq->whereNull('cancellation_status')
                   ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
            });

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        $items = $query->get();
        $filename = 'Laporan_Piutang_Keberangkatan_' . $periodData['startDate']->format('Ymd') . '_' . $periodData['endDate']->format('Ymd');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.pdf_receivables', [
                'items' => $items,
                'periodData' => $periodData,
                'generatedAt' => now(),
            ])->setPaper('a4', 'landscape');

            return $pdf->download($filename . '.pdf');
        }

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No.', 'No. Pendaftaran', 'Nama Jamaah', 'No. HP', 'Paket Umrah',
                'Tgl Keberangkatan', 'Jatuh Tempo', 'Jumlah Jamaah',
                'Total Tagihan (Rp)', 'Sudah Dibayar (Rp)', 'Sisa Piutang (Rp)', 'Status Pembayaran'
            ]);

            foreach ($items as $idx => $reg) {
                $inv = $reg->invoice;
                $statusPay = ($inv && $inv->remaining_balance <= 0) ? 'Lunas' : 'Belum Lunas';

                fputcsv($handle, [
                    $idx + 1,
                    $reg->registration_number,
                    $reg->user->name ?? '-',
                    $reg->user->phone ?? '-',
                    $reg->package->name ?? '-',
                    $reg->package?->departure_date ? $reg->package->departure_date->format('d/m/Y') : '-',
                    $inv?->due_date ? $inv->due_date->format('d/m/Y') : '-',
                    $reg->members->count(),
                    $inv->total_price ?? 0,
                    $inv->total_paid ?? 0,
                    $inv->remaining_balance ?? 0,
                    $statusPay,
                ]);
            }
            fclose($handle);
        }, $filename . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }

    /**
     * Ekspor Laporan Keberangkatan (CSV / PDF)
     */
    public function exportDepartures(Request $request, string $format = 'csv')
    {
        $periodData = $this->periodFilterService->parse($request);
        $startDate = $periodData['startDate'];
        $endDate = $periodData['endDate'];

        $query = Package::whereBetween('departure_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with(['variants.airlines', 'variants.hotelMakkah', 'variants.hotelMadinah'])
            ->orderBy('departure_date', 'asc');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->get()->map(function ($pkg) {
            $activeRegs = $pkg->registrations()
                ->where('status', '!=', Registration::STATUS_DIBATALKAN)
                ->where(function ($cq) {
                    $cq->whereNull('cancellation_status')
                       ->orWhere('cancellation_status', '!=', RegistrationCancellation::STATUS_APPROVED);
                })
                ->with('members')
                ->get();

            $paxCount = $activeRegs->sum(fn ($r) => $r->members->count());
            $pkg->filled_pax = $paxCount;
            $pkg->remaining_quota = max(0, $pkg->quota - $paxCount);
            $pkg->fill_percentage = $pkg->quota > 0 ? min(100, round(($paxCount / $pkg->quota) * 100)) : 0;

            return $pkg;
        });

        $filename = 'Laporan_Keberangkatan_' . $periodData['startDate']->format('Ymd') . '_' . $periodData['endDate']->format('Ymd');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.pdf_departures', [
                'items' => $items,
                'periodData' => $periodData,
                'generatedAt' => now(),
            ])->setPaper('a4', 'landscape');

            return $pdf->download($filename . '.pdf');
        }

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No.', 'Nama Paket', 'Tgl Keberangkatan', 'Durasi', 'Maskapai',
                'Hotel Makkah', 'Hotel Madinah', 'Total Kuota', 'Terisi (Pax)',
                'Sisa Kuota', 'Keterisian (%)', 'Harga Dasar (Rp)', 'Status Paket'
            ]);

            foreach ($items as $idx => $pkg) {
                fputcsv($handle, [
                    $idx + 1,
                    $pkg->name,
                    $pkg->departure_date ? $pkg->departure_date->format('d/m/Y') : '-',
                    ($pkg->duration ?? 9) . ' Hari',
                    $pkg->airline->name ?? '-',
                    $pkg->hotelMakkah->name ?? '-',
                    $pkg->hotelMadinah->name ?? '-',
                    $pkg->quota,
                    $pkg->filled_pax,
                    $pkg->remaining_quota,
                    $pkg->fill_percentage . '%',
                    $pkg->price,
                    ucfirst($pkg->status),
                ]);
            }
            fclose($handle);
        }, $filename . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }
}
