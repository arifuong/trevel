<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    protected ImageManager $manager;

    /**
     * Batas maksimal ukuran input file asli (10 MB = 10240 KB).
     */
    public const MAX_INPUT_SIZE_KB = 10240;

    /**
     * MIME types yang diperbolehkan.
     */
    public const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Ekstensi file yang diperbolehkan.
     */
    public const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
    ];

    /**
     * Konfigurasi Dokumen (KTP, KK, Paspor, Buku Nikah, Akta Kelahiran, Bukti Bayar).
     * Maksimal dimensi 2400px (agar teks terbaca jelas), kualitas JPEG 82%.
     */
    public const DOC_MAX_DIMENSION = 2400;
    public const DOC_JPEG_QUALITY = 82;

    /**
     * Konfigurasi Foto (Foto profil, foto jamaah, avatar).
     * Maksimal dimensi 1800px, kualitas JPEG 78%.
     */
    public const PHOTO_MAX_DIMENSION = 1800;
    public const PHOTO_JPEG_QUALITY = 78;

    /**
     * Konfigurasi Foto Publik (Paket wisata, Galeri, Hero).
     * Maksimal dimensi 1200px, kualitas JPEG & WebP 82%.
     */
    public const PUBLIC_PHOTO_MAX_DIMENSION = 1200;
    public const PUBLIC_PHOTO_QUALITY = 82;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Validasi berkas gambar (status valid, ukuran maksimal 10MB, MIME riil, ekstensi).
     *
     * @throws ValidationException
     */
    public function validateImage(UploadedFile $file, int $maxKilobytes = self::MAX_INPUT_SIZE_KB): void
    {
        if (!$file->isValid()) {
            throw ValidationException::withMessages([
                'file' => 'File yang diunggah tidak valid atau mengalami kerusakan.',
            ]);
        }

        // Cek ukuran file asli
        $sizeKb = $file->getSize() / 1024;
        if ($sizeKb > $maxKilobytes) {
            throw ValidationException::withMessages([
                'file' => 'Ukuran file melebihi batas maksimal 10 MB.',
            ]);
        }

        // Cek MIME type riil dari header binary
        $mime = $file->getMimeType();
        if (!in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
            throw ValidationException::withMessages([
                'file' => 'Format file tidak didukung. Format yang diperbolehkan hanya JPG, JPEG, PNG, dan WEBP.',
            ]);
        }

        // Cek ekstensi file
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'file' => 'Ekstensi file tidak valid. Gunakan format .jpg, .jpeg, .png, atau .webp.',
            ]);
        }
    }

    /**
     * Upload & kompres dokumen identitas dan bukti transfer pembayaran.
     *
     * @param UploadedFile|string $file
     * @param string $directory Subfolder di storage/app/public/ (misal: 'documents/ktp', 'payments/dp')
     * @return string Path relatif untuk disimpan di database (misal: 'documents/ktp/uuid.jpg')
     */
    public function uploadDocument(UploadedFile|string $file, string $directory): string
    {
        return $this->processAndStore(
            file: $file,
            directory: $directory,
            maxDimension: self::DOC_MAX_DIMENSION,
            quality: self::DOC_JPEG_QUALITY
        );
    }

    /**
     * Upload & kompres foto profil / foto jamaah.
     *
     * @param UploadedFile|string $file
     * @param string $directory Subfolder di storage/app/public/ (misal: 'jamaah/foto')
     * @return string Path relatif untuk disimpan di database
     */
    public function uploadPhoto(UploadedFile|string $file, string $directory): string
    {
        return $this->processAndStore(
            file: $file,
            directory: $directory,
            maxDimension: self::PHOTO_MAX_DIMENSION,
            quality: self::PHOTO_JPEG_QUALITY
        );
    }

    /**
     * Upload & kompres foto publik (Paket wisata, Galeri, Hero).
     *
     * @param UploadedFile|string $file
     * @param string $directory Subfolder di storage/app/public/ (misal: 'packages/photos', 'galleries')
     * @return string Path relatif untuk disimpan di database
     */
    public function uploadPublicPhoto(UploadedFile|string $file, string $directory): string
    {
        return $this->processAndStore(
            file: $file,
            directory: $directory,
            maxDimension: self::PUBLIC_PHOTO_MAX_DIMENSION,
            quality: self::PUBLIC_PHOTO_QUALITY
        );
    }

    /**
     * Ganti file lama dengan file baru secara aman.
     * Upload & kompres file baru terlebih dahulu, baru kemudian menghapus file lama dari storage.
     *
     * @param string|null $oldPath Path relatif file lama di database
     * @param UploadedFile|string $newFile File baru yang diunggah
     * @param string $directory Folder tujuan
     * @param string $type 'document', 'photo', atau 'public_photo'
     * @return string Path relatif file baru
     */
    public function replaceFile(?string $oldPath, UploadedFile|string $newFile, string $directory, string $type = 'document'): string
    {
        $newPath = match ($type) {
            'photo'        => $this->uploadPhoto($newFile, $directory),
            'public_photo' => $this->uploadPublicPhoto($newFile, $directory),
            default        => $this->uploadDocument($newFile, $directory),
        };

        // Hapus file lama hanya setelah file baru berhasil dikompres dan disimpan
        if ($oldPath && $oldPath !== $newPath) {
            $this->deleteFile($oldPath);
        }

        return $newPath;
    }

    /**
     * Hapus file fisik dari local storage (disk public), termasuk versi WebP jika ada.
     */
    public function deleteFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            $deleted = false;
            if (Storage::disk('public')->exists($path)) {
                $deleted = Storage::disk('public')->delete($path);
            }

            // Hapus juga file WebP pendamping jika ada
            $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
            if ($webpPath !== $path && Storage::disk('public')->exists($webpPath)) {
                Storage::disk('public')->delete($webpPath);
            }

            // Hapus juga file cache on-demand WebP jika ada
            $cacheWebpPath = 'cache/webp/' . ltrim($path, '/') . '.webp';
            if (Storage::disk('public')->exists($cacheWebpPath)) {
                Storage::disk('public')->delete($cacheWebpPath);
            }

            return $deleted;
        } catch (\Throwable $e) {
            Log::error("[ImageUploadService] Gagal menghapus file fisik: {$path}. Error: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Core processing:
     * 1. Decode stream/file
     * 2. Resize proporsional (scaleDown - tidak upscale gambar kecil)
     * 3. Kompresi JPEG dengan kualitas terstandarisasi
     * 4. Simpan 1 file asli ke Local Storage disk public (storage/app/public/...) dengan nama UUID unik
     * (Konversi WebP dilakukan secara on-demand & di-cache saat diakses oleh browser via rute /img/{path})
     * 5. Bersihkan memori dan return relative path untuk database.
     */
    protected function processAndStore(
        UploadedFile|string $file,
        string $directory,
        int $maxDimension,
        int $quality
    ): string {
        if ($file instanceof UploadedFile) {
            $this->validateImage($file);
            $filePath = $file->getRealPath();
        } else {
            $filePath = $file;
        }

        try {
            // 1. Decode gambar dari path sementara (temp)
            $image = $this->manager->decodePath($filePath);

            // 2. Resize proporsional jika melebihi batas maksimal (tanpa upscaling)
            $image->scaleDown(width: $maxDimension, height: $maxDimension);

            // 3. Kompresi ke format JPEG
            $encodedJpeg = $image->encode(new JpegEncoder(quality: $quality));
            $binaryJpeg = (string) $encodedJpeg;

            // 4. Buat nama file unik berbasis UUID
            $uuid = Str::uuid()->toString();
            $filename = $uuid . '.jpg';
            $cleanDir = trim($directory, '/');
            $relativePath = $cleanDir . '/' . $filename;

            // 5. Simpan HANYA 1 file asli ke disk public (tanpa duplikasi format eager WebP)
            Storage::disk('public')->put($relativePath, $binaryJpeg);

            return $relativePath;
        } catch (\Throwable $e) {
            Log::error("[ImageUploadService] Gagal memproses/menyimpan gambar pada direktori {$directory}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw new \RuntimeException('Gagal memproses dan menyimpan gambar: ' . $e->getMessage(), 0, $e);
        }
    }
}
