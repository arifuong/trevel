<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageDeliveryController extends Controller
{
    /**
     * Folder-folder publik yang diizinkan untuk diakses & dikonversi on-demand.
     * DOKUMEN JAMAAH (documents/, payments/, dsb) DILARANG KERAS diakses melalui rute ini.
     */
    protected array $allowedPrefixes = [
        'packages/',
        'galleries/',
        'hotels/',
        'heroes/',
        'avatars/',
        'airlines/',
        'images/',
    ];

    /**
     * Ekstensi gambar yang diizinkan.
     */
    protected array $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
    ];

    /**
     * Layani gambar publik dengan negosiasi format WebP on-demand & caching disk.
     */
    public function deliver(Request $request, string $path): Response
    {
        // 1. Sanitasi path dari backslash, null bytes, dan path traversal
        $normalized = str_replace('\\', '/', $path);
        if (str_contains($normalized, '..') || str_contains($normalized, "\0")) {
            abort(404, 'Path tidak valid.');
        }

        $cleanPath = ltrim($normalized, '/');

        // 2. Proteksi Folder: Hanya folder publik yang diizinkan
        $isAllowed = false;
        foreach ($this->allowedPrefixes as $prefix) {
            if (str_starts_with($cleanPath, $prefix)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            abort(404, 'Berkas tidak ditemukan atau berada di luar folder publik.');
        }

        // 3. Validasi ekstensi berkas
        $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions, true)) {
            abort(404, 'Format berkas tidak didukung.');
        }

        // 4. Pastikan file sumber fisik ada di disk public
        $disk = Storage::disk('public');
        if (!$disk->exists($cleanPath)) {
            abort(404, 'Berkas gambar tidak ditemukan di server.');
        }

        $fullSourcePath = $disk->path($cleanPath);

        // 5. Cek apakah browser mendukung format WebP melalui Accept header
        $acceptHeader = $request->header('Accept', '');
        $supportsWebp = str_contains($acceptHeader, 'image/webp');

        // Jika browser mendukung WebP dan file asli berupa JPG atau PNG -> Layani WebP (dari cache atau buat baru)
        if ($supportsWebp && in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            $cacheRelativePath = 'cache/webp/' . $cleanPath . '.webp';

            if (!$disk->exists($cacheRelativePath)) {
                try {
                    $manager = new ImageManager(new Driver());
                    $image = $manager->decodePath($fullSourcePath);
                    $encodedWebp = $image->encode(new WebpEncoder(quality: 82));
                    $disk->put($cacheRelativePath, (string) $encodedWebp);
                } catch (\Throwable $e) {
                    Log::warning("[ImageDeliveryController] Gagal konversi on-demand WebP untuk {$cleanPath}: " . $e->getMessage());
                    // Fallback menyajikan file asli jika konversi gagal
                    return $this->serveFile($fullSourcePath, $this->resolveMimeType($ext), $request);
                }
            }

            return $this->serveFile($disk->path($cacheRelativePath), 'image/webp', $request);
        }

        // Jika browser tidak mendukung WebP atau file asli sudah WebP/GIF -> Layani file asli langsung
        return $this->serveFile($fullSourcePath, $this->resolveMimeType($ext), $request);
    }

    /**
     * Sajikan file dengan HTTP caching headers (Cache-Control, ETag, Last-Modified, 304 Not Modified).
     */
    protected function serveFile(string $filePath, string $mimeType, Request $request): Response
    {
        if (!file_exists($filePath)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $lastModified = filemtime($filePath);
        $fileSize = filesize($filePath);
        $etag = '"' . md5($lastModified . $fileSize) . '"';

        // Tangani Conditional GET (304 Not Modified)
        $ifNoneMatch = $request->headers->get('If-None-Match');
        $ifModifiedSince = $request->headers->get('If-Modified-Since');

        if ($ifNoneMatch === $etag || ($ifModifiedSince && strtotime($ifModifiedSince) >= $lastModified)) {
            return response('', 304, [
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'ETag'          => $etag,
                'Last-Modified' => gmdate('D, d M Y H:i:s T', $lastModified),
            ]);
        }

        return response()->file($filePath, [
            'Content-Type'  => $mimeType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag'          => $etag,
            'Last-Modified' => gmdate('D, d M Y H:i:s T', $lastModified),
        ]);
    }

    /**
     * Resolusi MIME type berdasarkan ekstensi berkas.
     */
    protected function resolveMimeType(string $ext): string
    {
        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'webp'        => 'image/webp',
            'gif'         => 'image/gif',
            default       => 'application/octet-stream',
        };
    }
}
