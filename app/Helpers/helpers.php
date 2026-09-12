<?php

use App\Helpers\ImageHelper;

if (!function_exists('img_url')) {
    /**
     * Helper global untuk mendapatkan URL pengiriman gambar on-demand (/img/{path}).
     */
    function img_url(?string $path, ?string $fallback = null): ?string
    {
        return ImageHelper::url($path, $fallback);
    }
}
