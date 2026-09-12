<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Registration;
use App\Models\RegistrationCancellation;
use App\Contracts\WhatsAppNotificationInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancellationController extends Controller
{
    public function __construct(
        protected WhatsAppNotificationInterface $whatsapp
    ) {}

    /**
     * Tampilkan daftar pengajuan pembatalan pendaftaran jamaah.
     */
    public function index(Request $request)
    {
        $query = RegistrationCancellation::with([
            'registration.user',
            'registration.package',
            'registration.members',
            'registration.invoice',
            'processedBy',
            'rejectedBy',
        ])->latest('id');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('registration.package', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })->orWhere('reason', 'like', "%{$search}%")
                  ->orWhere('registration_id', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $cancellations = $query->paginate(15)->withQueryString();

        return view('admin.cancellations.index', [
            'cancellations' => $cancellations,
            'totalCancellations' => RegistrationCancellation::count(),
            'pendingCount' => RegistrationCancellation::where('status', RegistrationCancellation::STATUS_PENDING)->count(),
            'approvedCount' => RegistrationCancellation::where('status', RegistrationCancellation::STATUS_APPROVED)->count(),
            'rejectedCount' => RegistrationCancellation::where('status', RegistrationCancellation::STATUS_REJECTED)->count(),
        ]);
    }

    /**
     * Validasi pengajuan pembatalan (Setujui / Tolak).
     */
    public function verify(Request $request, RegistrationCancellation $cancellation)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => [
                'required_if:action,reject',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->action === 'reject' && (!is_string($value) || trim($value) === '')) {
                        $fail('Alasan penolakan pengajuan pembatalan wajib diisi.');
                    }
                },
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'action.required' => 'Aksi validasi harus ditentukan.',
            'action.in' => 'Aksi validasi tidak valid.',
            'rejection_reason.required_if' => 'Alasan penolakan pengajuan pembatalan wajib diisi.',
            'rejection_reason.required' => 'Alasan penolakan pengajuan pembatalan wajib diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        $registration = $cancellation->registration;
        $user = $registration->user;
        $package = $registration->package;

        DB::beginTransaction();

        try {
            if ($request->action === 'approve') {
                // 1. Update status pengajuan pembatalan
                $cancellation->update([
                    'status' => RegistrationCancellation::STATUS_APPROVED,
                    'processed_by' => auth()->id(),
                    'processed_at' => Carbon::now(),
                    'rejection_reason' => null,
                    'rejected_by' => null,
                    'rejected_at' => null,
                ]);

                // 2. Update status registrasi menjadi 'dibatalkan'
                $registration->update([
                    'status' => Registration::STATUS_DIBATALKAN,
                    'cancellation_status' => RegistrationCancellation::STATUS_APPROVED,
                ]);

                // 3. Status kuota kursi otomatis kembali tersedia via perhitungan dinamis non-dibatalkan
                if ($registration->packageVariant && $registration->packageVariant->status === \App\Models\PackageVariant::STATUS_SOLD_OUT) {
                    if ($registration->packageVariant->getRemainingQuota() > 0) {
                        $registration->packageVariant->update(['status' => \App\Models\PackageVariant::STATUS_AKTIF]);
                    }
                }
                if ($package && $package->status === Package::STATUS_SOLD_OUT) {
                    if ($package->getRemainingQuota() > 0) {
                        $package->update(['status' => Package::STATUS_AKTIF]);
                    }
                }

                // 4. Kirim notifikasi WhatsApp ke jamaah
                $regId = $registration->registration_number;
                $packageName = $package->name ?? 'Paket Umrah/Haji';
                $refundFormatted = 'Rp ' . number_format((float) $cancellation->refund_amount, 0, ',', '.');
                $feeFormatted = 'Rp ' . number_format((float) $cancellation->fee_amount, 0, ',', '.');

                if ($cancellation->fee_amount > 0 || $cancellation->refund_amount > 0) {
                    $waMessage = "Assalamu'alaikum Wr. Wb. Bapak/Ibu {$user->name},\n\n" .
                        "Pengajuan pembatalan pendaftaran Anda ({$regId}) untuk {$packageName} telah DISETUJUI oleh tim PT. Zein Internasional.\n\n" .
                        "📋 Rincian Pembatalan & Pengembalian Dana:\n" .
                        "• Kategori: {$cancellation->category}\n" .
                        "• Potongan Biaya: {$feeFormatted}\n" .
                        "• Estimasi Refund: {$refundFormatted}\n\n" .
                        "Tim keuangan kami akan segera menghubungi Anda untuk koordinasi transfer pengembalian dana ke nomor rekening Anda.\n\n" .
                        "Jazakumullah khairan katsiran.\n" .
                        "PT. Zein Internasional";
                } else {
                    $waMessage = "Assalamu'alaikum Wr. Wb. Bapak/Ibu {$user->name},\n\n" .
                        "Pengajuan pembatalan pendaftaran Anda ({$regId}) untuk {$packageName} telah DISETUJUI oleh tim PT. Zein Internasional.\n\n" .
                        "Karena belum melakukan pembayaran DP, pembatalan ini tidak dikenakan biaya apapun.\n\n" .
                        "Terima kasih atas minat Anda bersama PT. Zein Internasional.";
                }

                $this->whatsapp->sendMessage($user->phone, $waMessage);

                DB::commit();

                return back()->with('success', 'Pengajuan pembatalan berhasil disetujui. Status pendaftaran diubah menjadi Dibatalkan, kuota kursi telah dikembalikan, dan notifikasi telah dikirim.');

            } else {
                // Reject Action
                $rejectionReason = trim($request->rejection_reason);

                $cancellation->update([
                    'status' => RegistrationCancellation::STATUS_REJECTED,
                    'rejection_reason' => $rejectionReason,
                    'rejected_by' => auth()->id(),
                    'rejected_at' => Carbon::now(),
                ]);

                $registration->update([
                    'cancellation_status' => RegistrationCancellation::STATUS_REJECTED,
                ]);

                // Kirim notifikasi WhatsApp penolakan pembatalan
                $regId = $registration->registration_number;
                $packageName = $package->name ?? 'Paket Umrah/Haji';
                $waMessage = "Assalamu'alaikum Wr. Wb. Bapak/Ibu {$user->name},\n\n" .
                    "Pengajuan pembatalan pendaftaran Anda ({$regId}) untuk {$packageName} TIDAK DAPAT DISETUJUI dengan alasan:\n\n" .
                    "\"{$rejectionReason}\"\n\n" .
                    "Pendaftaran dan jadwal ibadah Anda tetap aktif. Silakan akses dashboard atau hubungi kami untuk informasi lebih lanjut.\n\n" .
                    "PT. Zein Internasional";

                $this->whatsapp->sendMessage($user->phone, $waMessage);

                DB::commit();

                return back()->with('success', 'Pengajuan pembatalan telah ditolak dengan alasan penolakan yang dicatat.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses validasi pembatalan: ' . $e->getMessage());
        }
    }
}
