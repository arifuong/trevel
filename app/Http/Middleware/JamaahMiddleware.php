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

        return $next($request);
    }
}
