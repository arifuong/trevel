<?php

namespace App\Helpers;

class YouTubeHelper
{
    /**
     * Ekstrak YouTube Video ID (11 karakter) dari berbagai variasi format URL atau raw ID.
     * 
     * Mendukung:
     * - https://youtu.be/QrYcpXEC0RU
     * - https://www.youtube.com/watch?v=QrYcpXEC0RU
     * - https://www.youtube.com/watch?v=QrYcpXEC0RU&feature=shared
     * - https://www.youtube.com/embed/QrYcpXEC0RU
     * - https://www.youtube.com/v/QrYcpXEC0RU
     * - https://www.youtube.com/shorts/QrYcpXEC0RU
     * - QrYcpXEC0RU (raw ID)
     */
    public static function extractVideoId(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);

        // Jika user langsung memasukkan 11 karakter ID YouTube
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        // Regex untuk berbagai format URL YouTube
        $patterns = [
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/i',
            '/youtube\.com\/(?:embed|v|shorts)\/([a-zA-Z0-9_-]{11})/i',
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Hasilkan URL embed resmi YouTube.
     */
    public static function getEmbedUrl(?string $url): ?string
    {
        $videoId = self::extractVideoId($url);

        if (!$videoId) {
            return null;
        }

        return "https://www.youtube.com/embed/{$videoId}";
    }
}