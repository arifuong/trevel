<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PackageDeletionService
{
    /**
     * Hapus paket umroh beserta seluruh varian dan fotonya secara aman.
     *
     * @param Package $package
     * @param int|null $adminId ID administrator yang melakukan aksi
     * @return array ['success' => bool, 'message' => string]
     */
    public function deletePackage(Package $package, ?int $adminId = null): array
    {
        // 1. Guard check: Cek apakah paket atau varian masih memiliki data pendaftaran aktif
        $hasActivePackageRegistrations = $package->registrations()
            ->where('status', '!=', Registration::STATUS_DIBATALKAN)
            ->exists();

        $variantIds = $package->variants()->pluck('id');
        $hasActiveVariantRegistrations = Registration::whereIn('package_variant_id', $variantIds)
            ->where('status', '!=', Registration::STATUS_DIBATALKAN)
            ->exists();

        if ($hasActivePackageRegistrations || $hasActiveVariantRegistrations) {
            return [
                'success' => false,
                'message' => 'Paket tidak dapat dihapus karena masih digunakan oleh data pendaftaran/transaksi. Nonaktifkan paket jika tidak ingin ditampilkan kepada publik.',
            ];
        }

        try {
            $packageName = $package->name;
            $packageId = $package->id;
            $filesToDelete = [];

            // 2. Kumpulkan file paket induk
            if ($package->main_photo) {
                $filesToDelete[] = $package->main_photo;
            }

            // 3. Kumpulkan file sub-paket / varian
            foreach ($package->variants as $variant) {
                if ($variant->main_photo) {
                    $filesToDelete[] = $variant->main_photo;
                }
                if ($variant->airline_departure_logo) {
                    $filesToDelete[] = $variant->airline_departure_logo;
                }
                if ($variant->airline_return_logo) {
                    $filesToDelete[] = $variant->airline_return_logo;
                }
                foreach ($variant->hotelPhotos as $hPhoto) {
                    if ($hPhoto->photo_path) {
                        $filesToDelete[] = $hPhoto->photo_path;
                    }
                }
            }

            // 4. Eksekusi penghapusan database dalam transaksi atomik
            DB::transaction(function () use ($package) {
                foreach ($package->variants as $variant) {
                    $variant->hotelFacilities()->detach();
                    $variant->hotelPhotos()->delete();
                    $variant->includes()->delete();
                    $variant->excludes()->delete();
                    $variant->prices()->delete();
                    $variant->delete();
                }

                $package->delete();
            });

            // 5. Bersihkan file fisik storage HANYA milik paket yang dihapus (termasuk WebP pendamping)
            foreach ($filesToDelete as $filePath) {
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $filePath);
                if ($webpPath !== $filePath && Storage::disk('public')->exists($webpPath)) {
                    Storage::disk('public')->delete($webpPath);
                }
            }

            // 6. Audit Logging
            $executor = $adminId ? "Admin ID {$adminId}" : "System/Admin";
            Log::info("{$executor} deleted Package ID {$packageId} ({$packageName}) at " . now()->toIso8601String());

            return [
                'success' => true,
                'message' => "Paket '{$packageName}' dan seluruh data terkait berhasil dihapus.",
            ];

        } catch (\Throwable $e) {
            Log::error("Error deleting package ID {$package->id}: " . $e->getMessage(), ['exception' => $e]);
            return [
                'success' => false,
                'message' => 'Paket gagal dihapus. Silakan coba lagi.',
            ];
        }
    }
}
