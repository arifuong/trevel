<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentVerificationHistory;
use App\Models\Registration;
use App\Models\RegistrationMember;
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
        $query = Registration::with(['user', 'package', 'members', 'invoice'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('package', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $registrations = $query->paginate(10)->withQueryString();

        return view('admin.registrations.index', [
            'registrations' => $registrations,
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
            'pendingPaymentCount' => Registration::where('status', Registration::STATUS_MENUNGGU_PEMBAYARAN_DP)
                ->where(function ($cq) {
                    $cq->whereNull('cancellation_status')
                       ->orWhere('cancellation_status', '!=', \App\Models\RegistrationCancellation::STATUS_APPROVED);
                })->count(),
            'verifiedJamaahCount' => Registration::whereIn('status', [
                Registration::STATUS_JAMAAH,
                Registration::STATUS_CICILAN_PELUNASAN,
                Registration::STATUS_LUNAS,
                Registration::STATUS_BERANGKAT
            ])->where(function ($cq) {
                $cq->whereNull('cancellation_status')
                   ->orWhere('cancellation_status', '!=', \App\Models\RegistrationCancellation::STATUS_APPROVED);
            })->count(),
        ]);
    }

    /**
     * Detail pendaftaran jamaah & verifikasi dokumen.
     * PRD Section 6.6
     */
    public function show(Registration $registration)
    {
        $registration->load(['user', 'package', 'members', 'invoice', 'payments']);

        $totalMembers = $registration->members->count();
        $approvedMembers = $registration->members->where('document_status', RegistrationMember::DOC_STATUS_DISETUJUI)->count();
        $rejectedMembers = $registration->members->where('document_status', RegistrationMember::DOC_STATUS_DITOLAK)->count();
        $pendingMembers = $registration->members->where('document_status', RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI)->count();

        return view('admin.registrations.show', [
            'registration' => $registration,
            'totalMembers' => $totalMembers,
            'approvedMembers' => $approvedMembers,
            'rejectedMembers' => $rejectedMembers,
            'pendingMembers' => $pendingMembers,
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
}
