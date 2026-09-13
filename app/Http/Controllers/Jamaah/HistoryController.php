<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Halaman Riwayat Perjalanan Ibadah Jamaah (/portal/riwayat).
     * Menampilkan seluruh booking milik jamaah yang berstatus 'selesai'.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $completedRegistrations = $user->registrations()
            ->where('status', Registration::STATUS_SELESAI)
            ->with([
                'package',
                'packageVariant',
                'members',
                'payments',
                'invoice'
            ])
            ->latest('updated_at')
            ->paginate(10);

        return view('jamaah.history.index', [
            'user' => $user,
            'completedRegistrations' => $completedRegistrations,
        ]);
    }
}
