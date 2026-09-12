<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan form login jamaah (Email & Password).
     */
    public function showForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login jamaah via email & password.
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // 1. Cek apakah email terdaftar
        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun dengan email ini belum terdaftar.']);
        }

        // 2. Verifikasi kredensial password
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Password yang Anda masukkan tidak sesuai.']);
        }

        $user = Auth::user();

        // 3. Hanya role jamaah yang bisa login di portal publik
        if ($user->role !== 'jamaah') {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun ini bukan akun jamaah. Silakan masuk melalui portal Admin.']);
        }

        // 4. Regenerate session dan arahkan ke dashboard
        $request->session()->regenerate();

        return redirect()->intended(route('jamaah.dashboard'))
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }
}
