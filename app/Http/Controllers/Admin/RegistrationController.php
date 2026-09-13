<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DocumentType;
use App\Models\DocumentVerificationHistory;
use App\Models\JamaahDocument;
use App\Models\Package;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Services\BookingStatusService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

    /**
     * Tampilkan daftar seluruh pendaftaran jamaah.
     * PRD Section 6.6 & 6.11
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'aktif');
        if (!in_array($tab, ['aktif', 'riwayat'])) {
            $tab = 'aktif';
        }

        $query = Registration::with(['user', 'package', 'members', 'invoice'])->latest();

        if ($tab === 'riwayat') {
            $query->where('status', Registration::STATUS_SELESAI);
        } else {
            $query->where('status', '!=', Registration::STATUS_SELESAI);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('package', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($tab === 'riwayat' && $status === Registration::STATUS_SELESAI) {
                $query->where('status', $status);
            } elseif ($tab === 'aktif' && $status !== Registration::STATUS_SELESAI) {
                $query->where('status', $status);
            }
        }

        // Filter berdasarkan Phase dokumen (Tahap Awal vs Tahap Keberangkatan)
        if ($tab === 'aktif' && $phase = $request->input('phase')) {
            if ($phase === 'awal') {
                $query->where('status', Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN);
            } elseif ($phase === 'keberangkatan') {
                $query->whereIn('status', [
                    Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
                    Registration::STATUS_BERANGKAT,
                ]);
            }
        }

        $registrations = $query->paginate(10)->withQueryString();

        $activeCount = Registration::where('status', '!=', Registration::STATUS_SELESAI)->count();
        $historyCount = Registration::where('status', Registration::STATUS_SELESAI)->count();

        // Additional stats for riwayat tab
        $completedMembersCount = DB::table('registration_members')
            ->join('registrations', 'registration_members.registration_id', '=', 'registrations.id')
            ->where('registrations.status', Registration::STATUS_SELESAI)
            ->count();

        $completedRevenue = DB::table('invoices')
            ->join('registrations', 'invoices.registration_id', '=', 'registrations.id')
            ->where('registrations.status', Registration::STATUS_SELESAI)
            ->sum('invoices.total_price');

        return view('admin.registrations.index', [
            'registrations' => $registrations,
            'currentTab' => $tab,
            'activeCount' => $activeCount,
            'historyCount' => $historyCount,
            'completedMembersCount' => $completedMembersCount,
            'completedRevenue' => $completedRevenue,
            'currentPhase' => $request->input('phase'),
            'totalRegistrations' => Registration::where('status', '!=', Registration::STATUS_DIBATALKAN)
                ->where(function ($cq) {
                    $cq->whereNull('cancellation_status')
                       ->orWhere('cancellation_status', '!=', \App\Models\RegistrationCancellation::STATUS_APPROVED);
                })->count(),
            'pendingDocsCount' => Registration::where('status', Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN)
                ->where(function ($cq) {
                    $cq->whereNull('cancellation_status')
                       ->orWhere('cancellation_status', '!=', \App\Models\RegistrationCancellation::STATUS_APPROVED);
                })->count(),
            'pendingDepartureDocsCount' => Registration::where('status', Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN)
                ->where(function ($cq) {
                    $cq->whereNull('cancellation_status')
                       ->orWhere('cancellation_status', '!=', \App\Models\RegistrationCancellation::STATUS_APPROVED);
                })->count(),
            'pendingPaymentCount' => Registration::where('status', Registration::STATUS_MENUNGGU_PEMBAYARAN_DP)
                ->where(function ($cq) {
                    $cq->whereNull('cancellation_status')
                       ->orWhere('cancellation_status', '!=', \App\Models\RegistrationCancellation::STATUS_APPROVED);
                })->count(),
            'verifiedJamaahCount' => Registration::whereIn('status', [
                Registration::STATUS_JAMAAH,
                Registration::STATUS_CICILAN_PELUNASAN,
                Registration::STATUS_LUNAS,
                Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
                Registration::STATUS_BERANGKAT
            ])->where(function ($cq) {
                $cq->whereNull('cancellation_status')
                    ->orWhere('cancellation_status', '!=', \App\Models\RegistrationCancellation::STATUS_APPROVED);
            })->count(),
        ]);
    }

    /**
     * Detail pendaftaran jamaah & verifikasi dokumen (Tahap Awal & Tahap Keberangkatan).
     * PRD Section 6.6
     */
    public function show(Registration $registration, BookingStatusService $bookingStatusService)
    {
        $registration->load([
            'user',
            'package',
            'packageVariant',
            'members.documents.documentType',
            'invoice',
            'payments'
        ]);

        $totalMembers = $registration->members->count();
        $approvedMembers = $registration->members->where('document_status', RegistrationMember::DOC_STATUS_DISETUJUI)->count();
        $rejectedMembers = $registration->members->where('document_status', RegistrationMember::DOC_STATUS_DITOLAK)->count();
        $pendingMembers = $registration->members->where('document_status', RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI)->count();

        $departureSummary = $bookingStatusService->getDepartureDocumentsSummary($registration);

        return view('admin.registrations.show', [
            'registration' => $registration,
            'totalMembers' => $totalMembers,
            'approvedMembers' => $approvedMembers,
            'rejectedMembers' => $rejectedMembers,
            'pendingMembers' => $pendingMembers,
            'departureSummary' => $departureSummary,
        ]);
    }

    /**
     * Verifikasi dokumen anggota jamaah (Approve / Reject).
     *
     * STATE TRANSITION RULES (Strict Backend Enforcement):
     * - PENDING  -> APPROVE -> VERIFIED (Disetujui, final)
     * - PENDING  -> REJECT  -> REJECTED (Ditolak)
     * - REJECTED -> APPROVE -> DITOLAK OLEH BACKEND (Tidak boleh disetujui tanpa pengajuan ulang)
     * - VERIFIED -> REJECT  -> DITOLAK OLEH BACKEND (Tidak boleh ditolak karena sudah final)
     * - VERIFIED -> APPROVE -> Idempotent warning
     */
    public function verifyMember(Request $request, RegistrationMember $member)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ], [
            'action.required' => 'Aksi verifikasi harus dipilih.',
            'action.in' => 'Aksi verifikasi tidak valid.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        if ($request->action === 'reject') {
            $request->validate([
                'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
            ], [
                'rejection_reason.required' => 'Alasan penolakan dokumen wajib diisi.',
                'rejection_reason.min' => 'Alasan penolakan minimal 5 karakter.',
                'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
            ]);

            if (trim($request->rejection_reason) === '') {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Alasan penolakan tidak boleh hanya berisi spasi.'], 422);
                }
                return back()->withErrors(['rejection_reason' => 'Alasan penolakan tidak boleh hanya berisi spasi.'])->withInput();
            }
        }

        $admin = $request->user();

        return DB::transaction(function () use ($member, $request, $admin) {
            // Lock record row to prevent race conditions and ensure latest state from DB
            $lockedMember = RegistrationMember::where('id', $member->id)->lockForUpdate()->firstOrFail();
            $registration = $lockedMember->registration;

            // ════════════════════════════════════════════
            // ACTION: APPROVE
            // ════════════════════════════════════════════
            if ($request->action === 'approve') {
                // 1. Idempotent check: Jika sudah diverifikasi sebelumnya
                if ($lockedMember->isVerified()) {
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => true, 'message' => "Dokumen untuk {$lockedMember->name} sudah diverifikasi sebelumnya."]);
                    }
                    return back()->with('warning', "Dokumen untuk {$lockedMember->name} sudah diverifikasi sebelumnya.");
                }

                // 2. CRITICAL GUARD: Dokumen yang berstatus DITOLAK TIDAK BOLEH disetujui!
                if ($lockedMember->isRejected()) {
                    $errorMessage = "Dokumen untuk {$lockedMember->name} berstatus Ditolak dan tidak dapat disetujui. Jamaah harus melakukan pengajuan ulang dengan mengunggah seluruh dokumen persyaratan terlebih dahulu.";
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => $errorMessage], 422);
                    }
                    return back()->with('error', $errorMessage);
                }

                // 3. Status guard: Hanya status pending yang dapat disetujui
                if (!$lockedMember->isPending()) {
                    $errorMessage = "Status dokumen untuk {$lockedMember->name} tidak valid untuk disetujui.";
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => $errorMessage], 422);
                    }
                    return back()->with('error', $errorMessage);
                }

                $lockedMember->update([
                    'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
                    'rejection_reason' => null,
                    'verified_at' => now(),
                    'verified_by' => $admin->id,
                    'rejected_at' => null,
                    'rejected_by' => null,
                ]);

                // Simpan history audit
                DocumentVerificationHistory::create([
                    'registration_member_id' => $lockedMember->id,
                    'status' => RegistrationMember::DOC_STATUS_DISETUJUI,
                    'reason' => null,
                    'action_by' => $admin->id,
                    'created_at' => now(),
                ]);

                // Cek apakah SEMUA anggota di registrasi ini sudah disetujui
                $unapprovedCount = $registration->members()
                    ->where('document_status', '!=', RegistrationMember::DOC_STATUS_DISETUJUI)
                    ->count();

                if ($unapprovedCount === 0) {
                    $registration->update([
                        'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
                    ]);
                }

                $registration->refresh();
                $successMessage = $registration->status === Registration::STATUS_MENUNGGU_PEMBAYARAN_DP
                    ? "Dokumen untuk {$lockedMember->name} disetujui. Seluruh anggota keluarga telah terverifikasi! Status pendaftaran otomatis diperbarui menjadi 'Menunggu Pembayaran DP'."
                    : "Dokumen untuk {$lockedMember->name} berhasil disetujui.";

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $successMessage]);
                }

                return back()->with('success', $successMessage);
            }

            // ════════════════════════════════════════════
            // ACTION: REJECT
            // ════════════════════════════════════════════
            if ($request->action === 'reject') {
                // 1. CRITICAL GUARD: Dokumen yang sudah VERIFIED tidak boleh di-REJECT
                if ($lockedMember->isVerified()) {
                    $errorMessage = "Dokumen untuk {$lockedMember->name} sudah diverifikasi dan tidak dapat ditolak. Status verifikasi bersifat final.";
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => $errorMessage], 422);
                    }
                    return back()->with('error', $errorMessage);
                }

                // 2. Idempotent check: Jika sudah ditolak sebelumnya
                if ($lockedMember->isRejected()) {
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => true, 'message' => "Dokumen untuk {$lockedMember->name} sudah berstatus Ditolak sebelumnya."]);
                    }
                    return back()->with('warning', "Dokumen untuk {$lockedMember->name} sudah berstatus Ditolak sebelumnya.");
                }

                // 3. Status guard: Hanya status pending yang dapat ditolak
                if (!$lockedMember->isPending()) {
                    $errorMessage = "Status dokumen untuk {$lockedMember->name} tidak valid untuk ditolak.";
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => $errorMessage], 422);
                    }
                    return back()->with('error', $errorMessage);
                }

                $lockedMember->update([
                    'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
                    'rejection_reason' => trim($request->rejection_reason),
                    'rejected_at' => now(),
                    'rejected_by' => $admin->id,
                ]);

                // Simpan history audit
                DocumentVerificationHistory::create([
                    'registration_member_id' => $lockedMember->id,
                    'status' => RegistrationMember::DOC_STATUS_DITOLAK,
                    'reason' => trim($request->rejection_reason),
                    'action_by' => $admin->id,
                    'created_at' => now(),
                ]);

                // Jika ada anggota ditolak dan status registrasi tadinya menunggu pembayaran dp, kembalikan ke menunggu verifikasi
                if ($registration->status === Registration::STATUS_MENUNGGU_PEMBAYARAN_DP) {
                    $registration->update([
                        'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
                    ]);
                }

                $successMessage = "Dokumen untuk {$lockedMember->name} ditolak dengan alasan yang tercatat.";
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $successMessage]);
                }

                return back()->with('success', $successMessage);
            }
        });
    }

    /**
     * Admin: Unggah / Ganti dokumen anggota jamaah dengan kompresi terpusat.
     * GUARD: Tidak boleh mengubah dokumen yang sudah VERIFIED.
     */
    public function updateMemberDocument(Request $request, RegistrationMember $member)
    {
        // GUARD: Dokumen verified tidak boleh diganti via workflow normal
        if ($member->isVerified()) {
            return back()->with('error', 'Dokumen yang sudah diverifikasi tidak dapat diubah.');
        }

        $request->validate([
            'document_type' => ['required', 'in:ktp_file,kk_file,passport_file,marriage_book_file,birth_certificate_file'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'document_type.required' => 'Jenis dokumen harus ditentukan.',
            'document_type.in' => 'Jenis dokumen tidak valid.',
            'file.required' => 'File dokumen wajib diunggah.',
            'file.mimes' => 'Format file harus JPG, JPEG, PNG, atau WEBP.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $docType = $request->document_type;
        $dirMapping = [
            'ktp_file' => 'documents/ktp',
            'kk_file' => 'documents/kk',
            'passport_file' => 'documents/passport',
            'marriage_book_file' => 'documents/marriage',
            'birth_certificate_file' => 'documents/birth',
        ];

        $directory = $dirMapping[$docType] ?? 'documents';
        $oldPath = $member->{$docType};

        // Gunakan ImageUploadService::replaceFile() untuk kompresi aman dan pembersihan file lama
        $newPath = $this->imageUploadService->replaceFile(
            oldPath: $oldPath,
            newFile: $request->file('file'),
            directory: $directory,
            type: 'document'
        );

        $member->update([
            $docType => $newPath,
            'document_status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Dokumen berhasil diperbarui dan dikompresi otomatis.');
    }

    /**
     * Admin: Hapus berkas dokumen anggota jamaah dari database dan physical storage.
     * GUARD: Tidak boleh menghapus dokumen yang sudah VERIFIED.
     */
    public function deleteMemberDocument(RegistrationMember $member, string $documentType)
    {
        // GUARD: Dokumen verified tidak boleh dihapus
        if ($member->isVerified()) {
            return back()->with('error', 'Dokumen yang sudah diverifikasi tidak dapat dihapus.');
        }

        $validTypes = ['ktp_file', 'kk_file', 'passport_file', 'marriage_book_file', 'birth_certificate_file'];

        if (!in_array($documentType, $validTypes, true)) {
            return back()->with('error', 'Jenis dokumen tidak valid.');
        }

        $oldPath = $member->{$documentType};

        if ($oldPath) {
            $this->imageUploadService->deleteFile($oldPath);
            $member->update([
                $documentType => null,
            ]);
        }

        return back()->with('success', 'Dokumen fisik berhasil dihapus dari storage.');
    }

    /**
     * Admin: Verifikasi Dokumen Keberangkatan (Visa Umrah, Vaksin Meningitis, Foto Visa).
     */
    public function verifyDepartureDocument(Request $request, JamaahDocument $document, BookingStatusService $bookingStatusService)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ], [
            'action.required' => 'Aksi verifikasi harus ditentukan.',
            'action.in' => 'Aksi verifikasi tidak valid.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        if ($request->action === 'reject') {
            $request->validate([
                'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
            ], [
                'rejection_reason.required' => 'Alasan penolakan dokumen keberangkatan wajib diisi.',
                'rejection_reason.min' => 'Alasan penolakan minimal 5 karakter.',
                'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
            ]);
        }

        $admin = $request->user();
        $member = $document->member;
        $registration = $member->registration;
        $docType = $document->documentType;

        // GUARD 1: Pendaftaran berstatus Selesai (arsip) atau Dibatalkan tidak boleh dimutasi lagi
        if (in_array($registration->status, [Registration::STATUS_SELESAI, Registration::STATUS_DIBATALKAN], true)) {
            abort(422, 'Pendaftaran telah berstatus ' . $registration->status_label . ' dan tidak dapat diverifikasi lagi.');
        }

        // GUARD 2: Dokumen yang SUDAH VALID terkunci dan tidak dapat ditolak/dibatalkan
        // KECUALI status pendaftaran saat ini adalah 'berangkat' (mode koreksi darurat)
        if ($request->action === 'reject' && $document->status === JamaahDocument::STATUS_VALID) {
            if ($registration->status !== Registration::STATUS_BERANGKAT) {
                abort(422, 'Dokumen yang sudah valid terkunci dan tidak dapat dibatalkan/ditolak karena status pendaftaran belum mencapai "Siap Berangkat".');
            }
        }

        return DB::transaction(function () use ($document, $request, $admin, $member, $registration, $docType, $bookingStatusService) {
            $lockedDoc = JamaahDocument::where('id', $document->id)->lockForUpdate()->firstOrFail();

            // Double check guard inside lock
            if ($request->action === 'reject' && $lockedDoc->status === JamaahDocument::STATUS_VALID) {
                if ($registration->status !== Registration::STATUS_BERANGKAT) {
                    abort(422, 'Dokumen yang sudah valid terkunci dan tidak dapat dibatalkan/ditolak karena status pendaftaran belum mencapai "Siap Berangkat".');
                }
            }

            if ($request->action === 'approve') {
                $lockedDoc->update([
                    'status' => JamaahDocument::STATUS_VALID,
                    'rejection_reason' => null,
                    'verified_by' => $admin->id,
                    'verified_at' => now(),
                ]);

                // Simpan history audit
                DocumentVerificationHistory::create([
                    'registration_member_id' => $member->id,
                    'status' => RegistrationMember::DOC_STATUS_DISETUJUI,
                    'reason' => "Dokumen keberangkatan {$docType->name} disetujui oleh Admin.",
                    'action_by' => $admin->id,
                    'created_at' => now(),
                ]);

                // Evaluasi apakah seluruh dokumen keberangkatan wajib sudah lengkap
                $bookingStatusService->evaluateStatus($registration);
                $registration->refresh();

                $msg = $registration->status === Registration::STATUS_BERANGKAT
                    ? "Dokumen {$docType->name} untuk {$member->name} disetujui. Seluruh dokumen keberangkatan jamaah telah LENGKAP & VALID! Status pendaftaran otomatis diperbarui menjadi 'Siap Berangkat'."
                    : "Dokumen {$docType->name} untuk {$member->name} berhasil disetujui.";

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $msg]);
                }

                return back()->with('success', $msg);
            } else {
                $wasValid = $lockedDoc->status === JamaahDocument::STATUS_VALID;

                $lockedDoc->update([
                    'status' => JamaahDocument::STATUS_DITOLAK,
                    'rejection_reason' => trim($request->rejection_reason),
                    'verified_by' => $admin->id,
                    'verified_at' => now(),
                ]);

                // Simpan history audit
                DocumentVerificationHistory::create([
                    'registration_member_id' => $member->id,
                    'status' => RegistrationMember::DOC_STATUS_DITOLAK,
                    'reason' => ($wasValid ? "Koreksi darurat: " : "") . "Dokumen keberangkatan {$docType->name} ditolak: " . trim($request->rejection_reason),
                    'action_by' => $admin->id,
                    'created_at' => now(),
                ]);

                // Jika status pendaftaran tadinya berangkat, kembalikan ke menunggu_kelengkapan_keberangkatan
                if ($registration->status === Registration::STATUS_BERANGKAT) {
                    $registration->update([
                        'status' => Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
                    ]);
                }

                $msg = $wasValid
                    ? "Dokumen {$docType->name} untuk {$member->name} berhasil dibatalkan (koreksi). Status pendaftaran dikembalikan ke 'Kelengkapan Dokumen Keberangkatan'."
                    : "Dokumen {$docType->name} untuk {$member->name} ditolak. Catatan perbaikan telah dicatat.";

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $msg]);
                }

                return back()->with('success', $msg);
            }
        });
    }

    /**
     * Admin: Tandai pendaftaran individual sebagai Selesai (Tahap 9).
     * Rule: Hanya boleh berpindah dari status 'berangkat' ke 'selesai'.
     */
    public function markCompleted(Request $request, Registration $registration)
    {
        if ($registration->status !== Registration::STATUS_BERANGKAT) {
            return back()->with('error', 'Hanya pendaftaran yang berstatus "Siap Berangkat" yang dapat ditandai selesai.');
        }

        $admin = $request->user();

        DB::transaction(function () use ($registration, $admin) {
            $registration->update([
                'status' => Registration::STATUS_SELESAI,
            ]);

            AuditLog::record(
                action: AuditLog::ACTION_MARK_COMPLETED_INDIVIDUAL,
                description: "Admin {$admin->name} menandai pendaftaran {$registration->registration_number} (Jamaah: {$registration->user->name}) sebagai Selesai.",
                auditable: $registration,
                user: $admin
            );
        });

        return back()->with('success', "Pendaftaran {$registration->registration_number} berhasil ditandai sebagai Selesai.");
    }

    /**
     * Admin: Tandai semua peserta keberangkatan paket sebagai Selesai (Bulk Action).
     * Rule: Mengubah seluruh pendaftaran berstatus 'berangkat' pada paket/keberangkatan ini menjadi 'selesai'.
     */
    public function completeDeparture(Request $request, Package $package)
    {
        $admin = $request->user();

        return DB::transaction(function () use ($package, $admin) {
            $registrations = $package->registrations()
                ->where('status', Registration::STATUS_BERANGKAT)
                ->lockForUpdate()
                ->get();

            if ($registrations->isEmpty()) {
                return back()->with('warning', "Tidak ada peserta dengan status 'Siap Berangkat' pada jadwal paket/keberangkatan '{$package->name}'.");
            }

            $count = 0;
            foreach ($registrations as $registration) {
                $registration->update([
                    'status' => Registration::STATUS_SELESAI,
                ]);
                $count++;
            }

            AuditLog::record(
                action: AuditLog::ACTION_MARK_COMPLETED_BULK,
                description: "Admin {$admin->name} menandai {$count} pendaftaran peserta pada keberangkatan '{$package->name}' sebagai Selesai.",
                auditable: $package,
                user: $admin
            );

            return back()->with('success', "Sebanyak {$count} peserta keberangkatan paket '{$package->name}' berhasil ditandai sebagai Selesai.");
        });
    }
}
