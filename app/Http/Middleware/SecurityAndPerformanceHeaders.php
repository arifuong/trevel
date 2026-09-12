<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityAndPerformanceHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security headers for Best Practices
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Cache headers:
        // Static assets (compiled CSS/JS, images, on-demand img delivery, fonts) are cached long-term with immutability.
        if ($request->is('build/*') || $request->is('images/*') || $request->is('img/*') || $request->is('fonts/*') || $request->is('favicon.ico')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } else {
            // Dynamic web/API responses MUST NOT be cached by browser or intermediate proxies.
            // This ensures single source of truth and immediate state synchronization after login,
            // logout, CRUD, profile changes, and status transitions without manual F5 refresh.
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        // Gzip compression for compressible responses (HTML, JSON, SVG) when requested by browser
        if (function_exists('gzencode') && str_contains($request->header('Accept-Encoding', ''), 'gzip')) {
            $contentType = $response->headers->get('Content-Type', '');
            if (empty($contentType) || str_contains($contentType, 'text/') || str_contains($contentType, 'application/json') || str_contains($contentType, 'image/svg+xml')) {
                $content = $response->getContent();
                if ($content && strlen($content) > 1024) {
                    $compressed = gzencode($content, 6);
                    if ($compressed !== false) {
                        $response->setContent($compressed);
                        $response->headers->set('Content-Encoding', 'gzip');
                        $response->headers->set('Content-Length', (string) strlen($compressed));
                        $response->headers->set('Vary', 'Accept-Encoding', false);
                    }
                }
            }
        }

        return $response;
    }
}
