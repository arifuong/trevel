<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JamaahMiddleware
{
    /**
     * Middleware pembatas akses area jamaah.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'jamaah') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            return redirect()->route('login')
                ->with('error', 'Silakan login sebagai jamaah.');
        }

        if (!$request->user()->phone_verified_at) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun belum diverifikasi OTP.'], 403);
            }

            session(['otp_user_id' => $request->user()->id]);
            return redirect()->route('otp.verify')
                ->with('warning', 'Akun belum diverifikasi. Silakan verifikasi OTP.');
        }

        return $next($request);
    }
}
