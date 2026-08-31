<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh jamaah untuk Admin.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'jamaah')
            ->with([
                'registrations' => function ($q) {
                    $q->latest()->with(['package', 'invoice', 'members', 'payments']);
                }
            ])
            ->latest();

        // 1. Pencarian Server-Side (Nama, NIK, No. KK, No. Paspor, Email, WhatsApp)
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('birth_place', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('registrations.members', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%")
                         ->orWhere('no_kk', 'like', "%{$search}%")
                         ->orWhere('no_passport', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Status Pendaftaran / Jamaah
        if ($status = $request->input('status', $request->input('reg_status'))) {
            if ($status === 'belum_daftar') {
                $query->doesntHave('registrations');
            } elseif ($status === 'menunggu') {
                $query->whereHas('registrations', function ($q) {
                    $q->whereIn('status', [
                        Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
                        Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
                        Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP,
                    ]);
                });
            } elseif ($status === 'terverifikasi') {
                $query->whereHas('registrations', function ($q) {
                    $q->whereIn('status', [
                        Registration::STATUS_JAMAAH,
                        Registration::STATUS_CICILAN_PELUNASAN,
                        Registration::STATUS_LUNAS,
                        Registration::STATUS_BERANGKAT,
                    ]);
                });
            } elseif ($status === 'pembayaran') {
                $query->whereHas('registrations', function ($q) {
                    $q->whereIn('status', [
                        Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
                        Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP,
                        Registration::STATUS_CICILAN_PELUNASAN,
                    ]);
                });
            } elseif ($status === 'dibatalkan') {
                $query->whereHas('registrations', function ($q) {
                    $q->where('status', Registration::STATUS_DIBATALKAN)
                      ->orWhere('cancellation_status', 'approved');
                });
            } else {
                $query->whereHas('registrations', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            }
        }

        // 3. Filter Status Dokumen
        if ($docStatus = $request->input('doc_status')) {
            $query->whereHas('registrations.members', function ($q) use ($docStatus) {
                $q->where('document_status', $docStatus);
            });
        }

        // 4. Pagination
        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Detail lengkap satu data jamaah.
     */
    public function show(User $user)
    {
        // Pastikan hanya role jamaah (atau admin) yang dibuka
        $user->load([
            'registrations' => function ($q) {
                $q->latest()->with([
                    'package',
                    'members',
                    'invoice',
                    'payments' => function ($pq) {
                        $pq->latest();
                    }
                ]);
            }
        ]);

        // Ambil data pendaftaran aktif atau terbaru
        $activeRegistration = $user->activeRegistration() ?? $user->registrations->first();

        // Ambil data anggota utama jika ada
        $primaryMember = null;
        if ($activeRegistration) {
            $primaryMember = $activeRegistration->members
                ->where('relationship', 'diri_sendiri')
                ->first() ?? $activeRegistration->members->first();
        }

        return view('admin.users.show', [
            'user' => $user,
            'activeRegistration' => $activeRegistration,
            'primaryMember' => $primaryMember,
        ]);
    }

    /**
     * Endpoint preview / stream dokumen yang aman khusus Admin.
     */
    public function viewDocument(RegistrationMember $member, string $type)
    {
        $validTypes = ['ktp_file', 'kk_file', 'passport_file', 'marriage_book_file', 'birth_certificate_file'];

        if (!in_array($type, $validTypes, true)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        $filePath = $member->{$type};

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Berkas dokumen tidak ditemukan di server.');
        }

        return Storage::disk('public')->response($filePath);
    }

    /**
     * Hapus akun jamaah dan seluruh data terkait secara aman menggunakan JamaahDeletionService.
     */
    public function destroy(User $user, \App\Services\JamaahDeletionService $deletionService)
    {
        $result = $deletionService->deleteJamaah($user, auth()->id());

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', $result['message']);
    }
}
