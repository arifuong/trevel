<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegistrationRequest;
use App\Models\DocumentType;
use App\Models\DocumentVerificationHistory;
use App\Models\Invoice;
use App\Models\JamaahDocument;
use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Services\BookingStatusService;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

    /**
     * Tampilkan form pendaftaran paket & anggota keluarga.
     * PRD Section 6.2 & 6.3: Cek aturan "1 akun hanya boleh 1 pendaftaran aktif".
     */
    public function create(Request $request)
    {
        $user = $request->user();

        // Business Rule PRD Section 6.1 & 6.2:
        // Satu akun hanya boleh memiliki 1 pendaftaran aktif pada satu waktu.
        if ($user->hasActiveRegistration()) {
            return redirect()->route('jamaah.my-registration')
                ->with('warning', 'Anda sudah memiliki 1 pendaftaran yang sedang aktif. Anda tidak dapat membuat pendaftaran baru sampai pendaftaran sebelumnya selesai atau dibatalkan.');
        }

        // Ambil paket yang aktif & masih ada kuota beserta varian-variannya
        $packages = Package::with(['variants' => function($q) {
            $q->where('status', 'aktif')->orderBy('sort_order')->with(['prices' => function($qp) {
                $qp->where('is_active', true)->orderBy('sort_order');
            }]);
        }])->where('status', 'aktif')->get()->filter(function ($pkg) {
            return $pkg->remaining_quota > 0;
        })->values();

        // Cari paket yang dipilih dari parameter (opsional)
        $selectedPackage = null;
        if ($pkgId = $request->query('package_id')) {
            $selectedPackage = Package::where('status', 'aktif')->with(['variants' => function($q) {
                $q->where('status', 'aktif')->with(['prices' => fn($qp) => $qp->where('is_active', true)]);
            }])->find($pkgId);
        } elseif ($slug = $request->query('package')) {
            $selectedPackage = Package::where('status', 'aktif')->with(['variants' => function($q) {
                $q->where('status', 'aktif')->with(['prices' => fn($qp) => $qp->where('is_active', true)]);
            }])->where('slug', $slug)->first();
        }

        $selectedVariantId = $request->query('variant_id');
        $selectedRoomType = $request->query('room_type', 'quad');

        return view('jamaah.registration.create', [
            'user' => $user,
            'packages' => $packages,
            'selectedPackage' => $selectedPackage,
            'selectedVariantId' => $selectedVariantId,
            'selectedRoomType' => $selectedRoomType,
        ]);
    }

    /**
     * Simpan pendaftaran paket dan seluruh anggota keluarga + upload & kompresi dokumen.
     * PRD Section 6.3, 6.4, 9, 10, 11
     */
    public function store(StoreRegistrationRequest $request)
    {
        $user = $request->user();
        $membersData = $request->input('members', []);
        $memberCount = count($membersData);
        $files = $request->file('members', []);
        $uploadedPaths = [];

        DB::beginTransaction();

        try {
            // Lock and read fresh records from database, ensuring only active package and variant are processed
            $package = Package::lockForUpdate()->where('status', 'aktif')->findOrFail($request->package_id);
            $variant = null;
            if ($request->filled('package_variant_id')) {
                $variant = PackageVariant::lockForUpdate()
                    ->where('package_id', $package->id)
                    ->where('status', 'aktif')
                    ->findOrFail($request->package_variant_id);
            }
            $roomType = $request->input('room_type', 'quad');

            // Concurrency-safe fresh quota validation
            if ($variant) {
                $variantRemaining = $variant->getRemainingQuota();
                if ($variant->status !== 'aktif' || $variantRemaining < $memberCount) {
                    DB::rollBack();
                    if ($variantRemaining <= 0) {
                        return back()->withInput()->with('error', 'Sub-paket yang dipilih sedang tidak aktif atau kuota kursi telah habis.');
                    }
                    return back()->withInput()->with('error', "Sisa kuota sub-paket ({$variantRemaining} kursi) tidak mencukupi untuk mendaftarkan {$memberCount} jamaah.");
                }
            }

            $packageRemaining = $package->getRemainingQuota();
            if ($package->status !== 'aktif' || $packageRemaining < $memberCount) {
                DB::rollBack();
                if ($packageRemaining <= 0) {
                    return back()->withInput()->with('error', 'Paket yang Anda pilih sudah Sold Out / kuota kursi telah habis.');
                }
                return back()->withInput()->with('error', "Sisa kuota paket ({$packageRemaining} kursi) tidak mencukupi untuk mendaftarkan {$memberCount} jamaah.");
            }

            // 1. Buat Pendaftaran Utama
            $registration = Registration::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'package_variant_id' => $variant?->id,
                'room_type' => $roomType,
                'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
            ]);

            // 2. Simpan Anggota Keluarga & Upload Dokumen dengan Kompresi Otomatis
            foreach ($membersData as $index => $member) {
                $memberFiles = $files[$index] ?? [];

                // Upload & Kompres KTP
                $ktpPath = null;
                if (isset($memberFiles['ktp_file'])) {
                    $ktpPath = $this->imageUploadService->uploadDocument($memberFiles['ktp_file'], 'documents/ktp');
                    $uploadedPaths[] = $ktpPath;
                }

                // Upload & Kompres KK
                $kkPath = null;
                if (isset($memberFiles['kk_file'])) {
                    $kkPath = $this->imageUploadService->uploadDocument($memberFiles['kk_file'], 'documents/kk');
                    $uploadedPaths[] = $kkPath;
                }

                // Upload & Kompres Paspor
                $passportPath = null;
                if (isset($memberFiles['passport_file'])) {
                    $passportPath = $this->imageUploadService->uploadDocument($memberFiles['passport_file'], 'documents/passport');
                    $uploadedPaths[] = $passportPath;
                }

                // Upload & Kompres Buku Nikah (jika suami/istri)
                $marriagePath = null;
                if (isset($memberFiles['marriage_book_file'])) {
                    $marriagePath = $this->imageUploadService->uploadDocument($memberFiles['marriage_book_file'], 'documents/marriage');
                    $uploadedPaths[] = $marriagePath;
                }

                // Upload & Kompres Akta Lahir (jika anak)
                $birthPath = null;
                if (isset($memberFiles['birth_certificate_file'])) {
                    $birthPath = $this->imageUploadService->uploadDocument($memberFiles['birth_certificate_file'], 'documents/birth');
                    $uploadedPaths[] = $birthPath;
                }

                RegistrationMember::create([
                    'registration_id' => $registration->id,
                    'name' => $member['name'],
                    'birth_place' => $member['birth_place'] ?? null,
                    'birth_date' => $member['birth_date'] ?? null,
                    'gender' => $member['gender'] ?? null,
                    'nik' => $member['nik'],
                    'address' => $member['address'] ?? null,
                    'ktp_file' => $ktpPath,
                    'no_kk' => $member['no_kk'],
                    'kk_file' => $kkPath,
                    'no_passport' => $member['no_passport'] ?? null,
                    'passport_file' => $passportPath,
                    'relationship' => $member['relationship'],
                    'marriage_book_file' => $marriagePath,
                    'birth_certificate_file' => $birthPath,
                    'document_status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
                ]);

                // Sinkronkan ke profil user jika pemesan utama
                if ($member['relationship'] === 'diri_sendiri') {
                    $user->update([
                        'birth_place' => $member['birth_place'] ?? $user->birth_place,
                        'birth_date' => $member['birth_date'] ?? $user->birth_date,
                        'gender' => $member['gender'] ?? $user->gender,
                        'address' => $member['address'] ?? $user->address,
                    ]);
                }
            }

            // 3. Hitung Harga Resmi Database & Buat Tagihan Invoice (Batas DP = 7 hari)
            $totalPrice = $registration->calculateOfficialTotalPrice();
            $dpDueDate = Carbon::now()->addDays(7); // PRD Rule: 7 hari batas waktu pembayaran DP

            Invoice::create([
                'registration_id' => $registration->id,
                'total_price' => $totalPrice,
                'total_paid' => 0,
                'remaining_balance' => $totalPrice,
                'due_date' => $dpDueDate,
            ]);

            DB::commit();

            return redirect()->route('jamaah.my-registration')
                ->with('success', 'Pendaftaran berhasil dikirim! Dokumen Anda sedang menunggu verifikasi oleh tim PT. Zein Internasional.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Bersihkan file fisik yang sempat terunggah jika DB gagal commit
            foreach ($uploadedPaths as $path) {
                $this->imageUploadService->deleteFile($path);
            }

            return back()->withInput()->with('error', 'Terjadi kendala saat menyimpan pendaftaran: ' . $e->getMessage());
        }
    }

    /**
     * Halaman "Status Pendaftaran Saya" (/my-registration).
     * PRD Section 11: Menampilkan indikator tahapan (step indicator) dan detail pendaftaran.
     */
    public function show(Request $request, BookingStatusService $bookingStatusService)
    {
        $user = $request->user();
        
        $registrationQuery = $user->registrations()
            ->with([
                'package',
                'packageVariant',
                'members.documents.documentType',
                'invoice',
                'payments',
                'latestCancellation'
            ]);

        if ($reqId = $request->query('id')) {
            $registration = $registrationQuery->where('id', $reqId)->first();
        } else {
            $registration = $registrationQuery->latest()->first();
        }

        $departureSummary = null;
        if ($registration) {
            // Sinkronisasi status booking jika sudah lunas
            $bookingStatusService->evaluateStatus($registration);
            $registration->refresh();
            $departureSummary = $bookingStatusService->getDepartureDocumentsSummary($registration);
        }

        return view('jamaah.registration.status', [
            'user' => $user,
            'registration' => $registration,
            'departureSummary' => $departureSummary,
        ]);
    }

    /**
     * Jamaah: Pengajuan Ulang Lengkap (Revisi Total Data Identitas & Seluruh Dokumen).
     *
     * WORKFLOW RULES (REVISI TOTAL):
     * - Ketika status Ditolak, Jamaah WAJIB mengisi kembali SELURUH data identitas (Nama, NIK, No. KK)
     *   dan mengunggah kembali SELURUH dokumen persyaratan (KTP, KK, Paspor, Dokumen Pelengkap).
     * - Data lama tidak otomatis dianggap sebagai data final baru.
     * - Partial submission (hanya data tertentu atau dokumen tertentu) DITOLAK oleh backend.
     * - Setelah submit seluruh data & dokumen: DITOLAK -> MENUNGGU_VERIFIKASI (Pending).
     * - Dokumen yang sudah VERIFIED (Disetujui) tidak boleh diubah.
     */
    public function updateMemberDocument(Request $request, RegistrationMember $member)
    {
        $user = $request->user();

        // Pastikan anggota ini milik pendaftaran aktif user yang login
        if ($member->registration->user_id !== $user->id) {
            abort(403, 'Akses tidak sah.');
        }

        // CRITICAL GUARD: Dokumen yang sudah VERIFIED tidak boleh diubah
        if ($member->isVerified()) {
            return back()->with('error', 'Dokumen yang sudah diverifikasi tidak dapat diubah. Status verifikasi bersifat final.');
        }

        // ══════════════════════════════════════════════════════════════════════
        // REVISI TOTAL: Validasi Data Identitas + Seluruh Dokumen Persyaratan
        // ══════════════════════════════════════════════════════════════════════
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:laki-laki,perempuan'],
            'nik' => ['required', 'string', 'digits:16', 'regex:/^[0-9]{16}$/'],
            'address' => ['required', 'string', 'max:1000'],
            'no_kk' => ['required', 'string', 'digits:16', 'regex:/^[0-9]{16}$/'],
            'no_passport' => ['nullable', 'string', 'max:50'],
            'relationship' => ['required', 'in:diri_sendiri,suami,istri,anak,orang_tua,saudara'],
            'ktp_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'kk_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'passport_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];

        $messages = [
            'name.required' => 'Nama lengkap jamaah wajib diisi ulang sesuai KTP.',
            'birth_place.required' => 'Tempat lahir wajib diisi sesuai KTP.',
            'birth_date.required' => 'Tanggal lahir wajib diisi sesuai KTP.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'gender.required' => 'Jenis kelamin wajib dipilih sesuai KTP.',
            'gender.in' => 'Pilihan jenis kelamin harus Laki-laki atau Perempuan sesuai KTP.',
            'nik.required' => 'NIK wajib diisi ulang.',
            'nik.digits' => 'NIK harus berupa tepat 16 angka (0–9).',
            'nik.regex' => 'NIK hanya boleh berisi angka (0–9) tanpa huruf, spasi, atau simbol.',
            'address.required' => 'Alamat tinggal wajib diisi persis sesuai KTP.',
            'no_kk.required' => 'Nomor Kartu Keluarga (KK) wajib diisi ulang.',
            'no_kk.digits' => 'Nomor Kartu Keluarga (KK) harus berupa tepat 16 angka (0–9).',
            'no_kk.regex' => 'Nomor Kartu Keluarga (KK) hanya boleh berisi angka (0–9) tanpa huruf, spasi, atau simbol.',
            'relationship.required' => 'Hubungan keluarga wajib dipilih.',
            'relationship.in' => 'Hubungan keluarga tidak valid.',
            'ktp_file.required' => 'Foto / Scan KTP wajib diunggah ulang.',
            'ktp_file.mimes' => 'Format file KTP harus berupa JPG, JPEG, PNG, atau WEBP.',
            'ktp_file.max' => 'Ukuran file KTP maksimal 10 MB.',
            'kk_file.required' => 'Foto / Scan Kartu Keluarga (KK) wajib diunggah ulang.',
            'kk_file.mimes' => 'Format file KK harus berupa JPG, JPEG, PNG, atau WEBP.',
            'kk_file.max' => 'Ukuran file KK maksimal 10 MB.',
            'passport_file.mimes' => 'Format file Paspor harus berupa JPG, JPEG, PNG, atau WEBP.',
            'passport_file.max' => 'Ukuran file Paspor maksimal 10 MB.',
        ];

        if (in_array($request->relationship, ['suami', 'istri'])) {
            $rules['marriage_book_file'] = ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'];
            $messages['marriage_book_file.required'] = 'Scan Buku Nikah wajib diunggah ulang untuk pasangan suami/istri.';
            $messages['marriage_book_file.mimes'] = 'Format file Buku Nikah harus berupa JPG, JPEG, PNG, atau WEBP.';
            $messages['marriage_book_file.max'] = 'Ukuran file Buku Nikah maksimal 10 MB.';
        }

        if ($request->relationship === 'anak') {
            $rules['birth_certificate_file'] = ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'];
            $messages['birth_certificate_file.required'] = 'Scan Akta Kelahiran wajib diunggah ulang untuk pendaftaran anak.';
            $messages['birth_certificate_file.mimes'] = 'Format file Akta Kelahiran harus berupa JPG, JPEG, PNG, atau WEBP.';
            $messages['birth_certificate_file.max'] = 'Ukuran file Akta Kelahiran maksimal 10 MB.';
        }

        $request->validate($rules, $messages);

        DB::transaction(function () use ($member, $request, $user) {
            // Upload & ganti file KTP
            $newKtpPath = $this->imageUploadService->replaceFile(
                oldPath: $member->ktp_file,
                newFile: $request->file('ktp_file'),
                directory: 'documents/ktp',
                type: 'document'
            );

            // Upload & ganti file KK
            $newKkPath = $this->imageUploadService->replaceFile(
                oldPath: $member->kk_file,
                newFile: $request->file('kk_file'),
                directory: 'documents/kk',
                type: 'document'
            );

            // Upload & ganti file Paspor (opsional)
            $newPassportPath = $member->passport_file;
            if ($request->hasFile('passport_file')) {
                $newPassportPath = $this->imageUploadService->replaceFile(
                    oldPath: $member->passport_file,
                    newFile: $request->file('passport_file'),
                    directory: 'documents/passport',
                    type: 'document'
                );
            }

            $updateData = [
                'name' => trim($request->name),
                'birth_place' => trim($request->birth_place),
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'nik' => trim($request->nik),
                'address' => trim($request->address),
                'no_kk' => trim($request->no_kk),
                'no_passport' => $request->no_passport ? trim($request->no_passport) : null,
                'relationship' => $request->relationship,
                'ktp_file' => $newKtpPath,
                'kk_file' => $newKkPath,
                'passport_file' => $newPassportPath,
                'document_status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
                'rejection_reason' => null,
                'rejected_at' => null,
                'rejected_by' => null,
            ];

            // Upload & ganti Buku Nikah jika ada
            if ($request->hasFile('marriage_book_file')) {
                $updateData['marriage_book_file'] = $this->imageUploadService->replaceFile(
                    oldPath: $member->marriage_book_file,
                    newFile: $request->file('marriage_book_file'),
                    directory: 'documents/marriage',
                    type: 'document'
                );
            }

            // Upload & ganti Akta Lahir jika ada
            if ($request->hasFile('birth_certificate_file')) {
                $updateData['birth_certificate_file'] = $this->imageUploadService->replaceFile(
                    oldPath: $member->birth_certificate_file,
                    newFile: $request->file('birth_certificate_file'),
                    directory: 'documents/birth',
                    type: 'document'
                );
            }

            $member->update($updateData);

            // Sinkronisasi data akun pemesan utama jika diri sendiri
            if ($member->relationship === 'diri_sendiri') {
                $userUpdates = ['name' => trim($request->name)];
                if ($request->filled('birth_place')) $userUpdates['birth_place'] = trim($request->birth_place);
                if ($request->filled('birth_date')) $userUpdates['birth_date'] = $request->birth_date;
                if ($request->filled('gender')) $userUpdates['gender'] = $request->gender;
                if ($request->filled('address')) $userUpdates['address'] = trim($request->address);
                if ($request->filled('phone')) $userUpdates['phone'] = trim($request->phone);
                $user->update($userUpdates);
            }

            // Simpan audit history
            DocumentVerificationHistory::create([
                'registration_member_id' => $member->id,
                'status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
                'reason' => 'Jamaah mengajukan ulang seluruh data identitas dan dokumen persyaratan (Revisi Total)',
                'action_by' => $user->id,
                'created_at' => now(),
            ]);

            // Pastikan status registrasi utama kembali ke MENUNGGU_VERIFIKASI_DOKUMEN
            $registration = $member->registration;
            if ($registration->status !== Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN) {
                $registration->update([
                    'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
                ]);
            }
        });

        return back()->with('success', 'Seluruh data identitas dan dokumen persyaratan berhasil diajukan ulang! Pengajuan dokumen ' . $member->name . ' kini kembali berstatus Menunggu Verifikasi.');
    }

    /**
     * Jamaah: Unggah / Ganti Dokumen Tahap Keberangkatan (Visa Umrah, Vaksin Meningitis, Foto Visa).
     */
    public function uploadDepartureDocument(Request $request, RegistrationMember $member, DocumentType $documentType, BookingStatusService $bookingStatusService)
    {
        $user = $request->user();

        // Validasi otorisasi kepemilikan
        if ($member->registration->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunggah dokumen anggota ini.');
        }

        // Pastikan dokumen adalah dokumen tahap keberangkatan
        if (!$documentType->isDeparturePhase()) {
            return back()->with('error', 'Jenis dokumen ini bukan dokumen tahap keberangkatan.');
        }

        // Cek dokumen existing
        $existingDoc = JamaahDocument::where('registration_member_id', $member->id)
            ->where('document_type_id', $documentType->id)
            ->first();

        if ($existingDoc && $existingDoc->isValid()) {
            return back()->with('warning', "Dokumen {$documentType->name} untuk {$member->name} sudah diverifikasi dan valid, tidak perlu diunggah ulang.");
        }

        if ($existingDoc && $existingDoc->isPending()) {
            return back()->with('warning', "Dokumen {$documentType->name} untuk {$member->name} sedang dalam proses verifikasi oleh admin.");
        }

        // Aturan validasi file
        $isPhoto = ($documentType->code === DocumentType::CODE_FOTO_VISA);
        $mimesRule = $isPhoto ? 'mimes:jpg,jpeg,png,webp' : 'mimes:jpg,jpeg,png,webp,pdf';

        $request->validate([
            'file' => ['required', 'file', $mimesRule, 'max:10240'],
        ], [
            'file.required' => "File {$documentType->name} wajib dipilih.",
            'file.mimes' => $isPhoto
                ? 'Format file foto visa harus berformat JPG, JPEG, PNG, atau WEBP.'
                : 'Format file harus JPG, JPEG, PNG, WEBP, atau PDF.',
            'file.max' => 'Ukuran file dokumen maksimal 10 MB.',
        ]);

        $file = $request->file('file');
        $directory = 'documents/departure/' . strtolower($documentType->code);

        DB::beginTransaction();
        try {
            $uploadedPath = null;
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'pdf') {
                // PDF disimpan langsung ke storage public
                $fileName = time() . '_' . \Illuminate\Support\Str::random(12) . '.' . $extension;
                $uploadedPath = $file->storeAs($directory, $fileName, 'public');

                // Hapus file lama jika ada
                if ($existingDoc && $existingDoc->file_path) {
                    $this->imageUploadService->deleteFile($existingDoc->file_path);
                }
            } else {
                // Gambar dikompresi melalui ImageUploadService (yang otomatis menghapus file lama jika ada)
                $uploadedPath = $this->imageUploadService->replaceFile(
                    oldPath: $existingDoc?->file_path,
                    newFile: $file,
                    directory: $directory,
                    type: 'document'
                );
            }

            JamaahDocument::updateOrCreate(
                [
                    'registration_member_id' => $member->id,
                    'document_type_id' => $documentType->id,
                ],
                [
                    'file_path' => $uploadedPath,
                    'status' => JamaahDocument::STATUS_MENUNGGU_VERIFIKASI,
                    'rejection_reason' => null,
                    'verified_by' => null,
                    'verified_at' => null,
                ]
            );

            // Simpan audit history
            DocumentVerificationHistory::create([
                'registration_member_id' => $member->id,
                'status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
                'reason' => ($existingDoc && $existingDoc->isRejected())
                    ? "Jamaah mengunggah ulang berkas perbaikan keberangkatan: {$documentType->name}"
                    : "Jamaah mengunggah berkas keberangkatan: {$documentType->name}",
                'action_by' => $user->id,
                'created_at' => now(),
            ]);

            DB::commit();

            // Evaluasi status booking melalui BookingStatusService
            $bookingStatusService->evaluateStatus($member->registration);

            return back()->with('success', "Dokumen {$documentType->name} untuk {$member->name} berhasil diunggah dan sedang menunggu verifikasi admin.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengunggah dokumen: ' . $e->getMessage());
        }
    }
}
