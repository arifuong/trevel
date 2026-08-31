<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
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
     * Ganti file lama dengan file baru secara aman.
     * Upload & kompres file baru terlebih dahulu, baru kemudian menghapus file lama dari storage.
     *
     * @param string|null $oldPath Path relatif file lama di database
     * @param UploadedFile|string $newFile File baru yang diunggah
     * @param string $directory Folder tujuan
     * @param string $type 'document' atau 'photo'
     * @return string Path relatif file baru
     */
    public function replaceFile(?string $oldPath, UploadedFile|string $newFile, string $directory, string $type = 'document'): string
    {
        $newPath = match ($type) {
            'photo' => $this->uploadPhoto($newFile, $directory),
            default => $this->uploadDocument($newFile, $directory),
        };

        // Hapus file lama hanya setelah file baru berhasil dikompres dan disimpan
        if ($oldPath && $oldPath !== $newPath) {
            $this->deleteFile($oldPath);
        }

        return $newPath;
    }

    /**
     * Hapus file fisik dari local storage (disk public).
     */
    public function deleteFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
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
     * 4. Simpan langsung ke Laravel Local Storage (disk 'public') dengan nama UUID unik
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
            $encoded = $image->encode(new JpegEncoder(quality: $quality));
            $binary = (string) $encoded;

            // 4. Buat nama file unik berbasis UUID
            $filename = Str::uuid()->toString() . '.jpg';
            $relativePath = trim($directory, '/') . '/' . $filename;

            // 5. Simpan langsung ke Local Storage disk public (storage/app/public/...)
            Storage::disk('public')->put($relativePath, $binary);

            return $relativePath;
        } catch (\Throwable $e) {
            Log::error("[ImageUploadService] Gagal memproses/menyimpan gambar pada direktori {$directory}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw new \RuntimeException('Gagal memproses dan menyimpan gambar: ' . $e->getMessage(), 0, $e);
        }
    }
}
