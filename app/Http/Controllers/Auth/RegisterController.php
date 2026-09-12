<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Tampilkan form registrasi jamaah.
     */
    public function showForm()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi akun jamaah langsung aktif.
     */
    public function register(RegisterRequest $request)
    {
        $phone = $request->phone;
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($digits, '08')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $digits,
            'password' => $request->password,
            'role' => 'jamaah',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('jamaah.dashboard')
            ->with('success', 'Pendaftaran akun berhasil! Selamat datang di PT. Zein Internasional, ' . $user->name . '.');
    }
}
