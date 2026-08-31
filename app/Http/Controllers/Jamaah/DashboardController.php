<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard jamaah - halaman utama setelah login.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $registration = $user->registrations()
            ->with(['package', 'members', 'payments', 'invoice', 'latestCancellation'])
            ->latest()
            ->first();

        return view('jamaah.dashboard', [
            'user' => $user,
            'registration' => $registration,
        ]);
    }
}
