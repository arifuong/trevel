<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CleanLegacyDocumentFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:clean-legacy {--force : Paksa hapus tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan berkas dokumen invoice/kwitansi statis (PDF/Excel) di server storage karena sistem telah beralih ke generate on-the-fly';

    /**
     * Direktori-direktori penyimpanan dokumen lama yang akan dipindai dan dibersihkan.
     */
    protected array $targetDirectories = [
        'app/documents/invoices',
        'app/documents/receipts',
        'app/public/invoices',
        'app/public/receipts',
        'app/invoices',
        'app/receipts',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pemindaian berkas invoice dan kwitansi statis di storage...');

        $filesToDelete = [];
        $totalBytes = 0;

        foreach ($this->targetDirectories as $relDir) {
            $fullDir = storage_path($relDir);
            if (File::isDirectory($fullDir)) {
                $files = File::files($fullDir);
                foreach ($files as $file) {
                    $filename = $file->getFilename();
                    // Abaikan file .gitignore
                    if ($filename === '.gitignore') {
                        continue;
                    }
                    $size = $file->getSize();
                    $filesToDelete[] = [
                        'path' => $file->getRealPath(),
                        'filename' => $filename,
                        'directory' => $relDir,
                        'size' => $size,
                        'size_formatted' => $this->formatBytes($size),
                    ];
                    $totalBytes += $size;
                }
            }
        }

        $count = count($filesToDelete);

        if ($count === 0) {
            $this->info('✓ Tidak ditemukan berkas dokumen lama. Storage server sudah bersih (0 berkas, 0 B).');
            return Command::SUCCESS;
        }

        $this->table(
            ['No', 'Direktori', 'Nama Berkas', 'Ukuran'],
            collect($filesToDelete)->map(fn ($item, $idx) => [
                $idx + 1,
                $item['directory'],
                $item['filename'],
                $item['size_formatted'],
            ])->toArray()
        );

        $totalFormatted = $this->formatBytes($totalBytes);
        $this->warn("Ditemukan {$count} berkas dengan total ukuran {$totalFormatted}.");

        if (!$this->option('force') && !$this->confirm("Apakah Anda yakin ingin menghapus seluruh {$count} berkas tersebut?", true)) {
            $this->info('Operasi pembersihan dibatalkan.');
            return Command::SUCCESS;
        }

        $deletedCount = 0;
        $deletedBytes = 0;

        foreach ($filesToDelete as $item) {
            if (File::exists($item['path'])) {
                File::delete($item['path']);
                $deletedCount++;
                $deletedBytes += $item['size'];
            }
        }

        $freedFormatted = $this->formatBytes($deletedBytes);
        $this->info("✓ Pembersihan selesai! {$deletedCount} berkas berhasil dihapus permanen. Total storage yang dibebaskan: {$freedFormatted}.");
        Log::info("CleanLegacyDocumentFiles: {$deletedCount} berkas dibersihkan, {$freedFormatted} storage dibebaskan.");

        return Command::SUCCESS;
    }

    /**
     * Format bytes ke unit manusiawi (B, KB, MB, GB).
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
