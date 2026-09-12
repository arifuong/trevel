<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Konversi path berkas gambar publik menjadi URL delivery on-demand (/img/{path}).
     * Mendukung URL eksternal (Unsplash, YouTube), fallback jika kosong, dan normalisasi prefix storage/.
     *
     * @param string|null $path Path relatif di disk public atau URL eksternal
     * @param string|null $fallback URL cadangan jika path bernilai null / kosong
     * @return string|null
     */
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if (empty($path)) {
            return $fallback;
        }

        $trimmed = trim($path);

        // Jika sudah berupa URL eksternal atau data-URI
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://') || str_starts_with($trimmed, 'data:')) {
            return $trimmed;
        }

        // Jika sudah berawalan /img/
        if (str_starts_with($trimmed, '/img/')) {
            return url($trimmed);
        }

        // Bersihkan prefix 'storage/' atau '/storage/' jika ada
        $cleanPath = preg_replace('#^/?storage/#', '', $trimmed);
        $cleanPath = ltrim($cleanPath, '/');

        return url('/img/' . $cleanPath);
    }
}
