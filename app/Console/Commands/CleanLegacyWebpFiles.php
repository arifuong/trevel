<?php

namespace App\Console\Commands;

use App\Models\Gallery;
use App\Models\Hotel;
use App\Models\HotelPhoto;
use App\Models\JamaahDocument;
use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\Payment;
use App\Models\RegistrationMember;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanLegacyWebpFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:clean-legacy-webp {--force : Jalankan penghapusan tanpa konfirmasi interaktif} {--public-only : Hanya bersihkan folder publik tanpa menyentuh dokumen privat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan berkas .webp statis yang ter-generate di muka (eager) pada server storage karena sistem telah beralih ke konversi on-demand dengan cache';

    /**
     * Folder publik yang dipindai untuk pembersihan berkas .webp legacy.
     */
    protected array $publicFolders = [
        'packages',
        'galleries',
        'hotels',
        'heroes',
        'avatars',
        'airlines',
    ];

    /**
     * Folder privat/dokumen yang sebelumnya tidak sengaja ikut meng-generate berkas pendamping .webp.
     */
    protected array $privateFolders = [
        'documents',
        'payments',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('========================================================================');
        $this->info('  Pembersihan Berkas .webp Statis Warisan (Legacy Cleanup)');
        $this->info('========================================================================');

        $disk = Storage::disk('public');

        // 1. Periksa & Perbarui referensi .webp di Database jika ada
        $this->info('1. Memindai & memperbarui referensi berkas .webp di Database...');
        $updatedDbCount = $this->updateDatabaseReferences();
        if ($updatedDbCount > 0) {
            $this->info("   ✓ Berhasil memperbarui {$updatedDbCount} kolom database ke format file asli (.jpg/.png).");
        } else {
            $this->line('   ✓ Tidak ditemukan path .webp statis di database. Seluruh database telah mengacu ke file asli.');
        }

        // 2. Tentukan folder target pemindaian
        $targetFolders = $this->publicFolders;
        if (!$this->option('public-only')) {
            $targetFolders = array_merge($targetFolders, $this->privateFolders);
        }

        $this->info('2. Memindai berkas .webp legacy pada folder: ' . implode(', ', $targetFolders) . '...');

        $foundFiles = [];
        $totalBytes = 0;

        foreach ($targetFolders as $folder) {
            $allFiles = $disk->allFiles($folder);
            foreach ($allFiles as $relPath) {
                if (strtolower(pathinfo($relPath, PATHINFO_EXTENSION)) === 'webp') {
                    $size = $disk->size($relPath);
                    $foundFiles[] = [
                        'relative' => str_replace('\\', '/', $relPath),
                        'size' => $size,
                        'size_kb' => round($size / 1024, 2),
                    ];
                    $totalBytes += $size;
                }
            }
        }

        if (empty($foundFiles)) {
            $this->info('   ✓ Tidak ditemukan berkas .webp legacy di storage. Storage sudah bersih!');
            return Command::SUCCESS;
        }

        $totalKb = round($totalBytes / 1024, 2);
        $totalMb = round($totalBytes / (1024 * 1024), 2);
        $count = count($foundFiles);

        $this->warn("   Ditemukan {$count} berkas .webp legacy yang memakan ruang {$totalKb} KB (~{$totalMb} MB).");

        // Tampilkan sampel file yang ditemukan
        $tableRows = array_map(fn($f) => [$f['relative'], $f['size_kb'] . ' KB'], array_slice($foundFiles, 0, 15));
        if ($count > 15) {
            $tableRows[] = ['... dan ' . ($count - 15) . ' berkas lainnya', '...'];
        }
        $this->table(['File Path (storage/app/public/)', 'Ukuran'], $tableRows);

        // Konfirmasi sebelum menghapus
        if (!$this->option('force') && !$this->confirm("Apakah Anda yakin ingin menghapus {$count} berkas .webp ini secara permanen?", true)) {
            $this->comment('Proses pembersihan dibatalkan oleh pengguna.');
            return Command::SUCCESS;
        }

        // 3. Eksekusi penghapusan berkas
        $deletedCount = 0;
        $freedBytes = 0;

        foreach ($foundFiles as $fileInfo) {
            try {
                if ($disk->exists($fileInfo['relative'])) {
                    $disk->delete($fileInfo['relative']);
                    $deletedCount++;
                    $freedBytes += $fileInfo['size'];
                }
            } catch (\Throwable $e) {
                $this->error("Gagal menghapus file: {$fileInfo['relative']} - " . $e->getMessage());
                Log::error("[CleanLegacyWebpFiles] Gagal hapus: {$fileInfo['relative']}: " . $e->getMessage());
            }
        }

        $freedKb = round($freedBytes / 1024, 2);
        $freedMb = round($freedBytes / (1024 * 1024), 2);

        $this->info('========================================================================');
        $this->info("✓ BERHASIL: {$deletedCount} berkas .webp legacy dihapus.");
        $this->info("✓ RUANG STORAGE DIBEBASKAN: {$freedKb} KB (~{$freedMb} MB).");
        $this->info('✓ Seluruh permintaan gambar WebP selanjutnya akan diproses secara on-demand via rute /img/{path}.');
        $this->info('========================================================================');

        return Command::SUCCESS;
    }

    /**
     * Pindai tabel database dan normalkan kembali setiap referensi .webp ke .jpg / .png asli.
     */
    protected function updateDatabaseReferences(): int
    {
        $updated = 0;

        // 1. Packages
        $packages = Package::where('main_photo', 'LIKE', '%.webp')->get();
        foreach ($packages as $pkg) {
            $newPath = preg_replace('/\.webp$/i', '.jpg', $pkg->main_photo);
            $pkg->update(['main_photo' => $newPath]);
            $updated++;
        }

        // 2. PackageVariants
        $variants = PackageVariant::where('main_photo', 'LIKE', '%.webp')->get();
        foreach ($variants as $var) {
            $newPath = preg_replace('/\.webp$/i', '.jpg', $var->main_photo);
            $var->update(['main_photo' => $newPath]);
            $updated++;
        }

        // 3. Galleries
        $galleries = Gallery::where('image_path', 'LIKE', '%.webp')->get();
        foreach ($galleries as $gal) {
            $newPath = preg_replace('/\.webp$/i', '.jpg', $gal->image_path);
            $gal->update(['image_path' => $newPath]);
            $updated++;
        }

        // 4. Hotels
        $hotels = Hotel::where('main_photo', 'LIKE', '%.webp')->get();
        foreach ($hotels as $hot) {
            $newPath = preg_replace('/\.webp$/i', '.jpg', $hot->main_photo);
            $hot->update(['main_photo' => $newPath]);
            $updated++;
        }

        // 5. HotelPhotos
        $hotelPhotos = HotelPhoto::where('photo_path', 'LIKE', '%.webp')->get();
        foreach ($hotelPhotos as $hp) {
            $newPath = preg_replace('/\.webp$/i', '.jpg', $hp->photo_path);
            $hp->update(['photo_path' => $newPath]);
            $updated++;
        }

        // 6. RegistrationMembers
        $members = RegistrationMember::where('ktp_file', 'LIKE', '%.webp')
            ->orWhere('kk_file', 'LIKE', '%.webp')
            ->orWhere('passport_file', 'LIKE', '%.webp')
            ->get();
        foreach ($members as $mem) {
            $updates = [];
            if ($mem->ktp_file && str_ends_with(strtolower($mem->ktp_file), '.webp')) {
                $updates['ktp_file'] = preg_replace('/\.webp$/i', '.jpg', $mem->ktp_file);
            }
            if ($mem->kk_file && str_ends_with(strtolower($mem->kk_file), '.webp')) {
                $updates['kk_file'] = preg_replace('/\.webp$/i', '.jpg', $mem->kk_file);
            }
            if ($mem->passport_file && str_ends_with(strtolower($mem->passport_file), '.webp')) {
                $updates['passport_file'] = preg_replace('/\.webp$/i', '.jpg', $mem->passport_file);
            }
            if (!empty($updates)) {
                $mem->update($updates);
                $updated += count($updates);
            }
        }

        // 7. Payments
        $payments = Payment::where('proof_file', 'LIKE', '%.webp')->get();
        foreach ($payments as $pay) {
            $newPath = preg_replace('/\.webp$/i', '.jpg', $pay->proof_file);
            $pay->update(['proof_file' => $newPath]);
            $updated++;
        }

        // 8. JamaahDocuments
        $jamaahDocs = JamaahDocument::where('file_path', 'LIKE', '%.webp')->get();
        foreach ($jamaahDocs as $jd) {
            $newPath = preg_replace('/\.webp$/i', '.jpg', $jd->file_path);
            $jd->update(['file_path' => $newPath]);
            $updated++;
        }

        return $updated;
    }
}
