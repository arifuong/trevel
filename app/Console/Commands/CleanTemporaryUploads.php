<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanTemporaryUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jamaah:clean-temp-files {--hours=2 : Usia file sementara dalam jam sebelum dihapus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan berkas temporary upload yang tidak disubmit atau kedaluwarsa';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours)->timestamp;

        $this->info("Memulai pembersihan berkas sementara (temporary upload) yang lebih lama dari {$hours} jam...");

        $tempDirectories = [
            'temp',
            'temp/jamaah',
            'temp/documents',
        ];

        $deletedCount = 0;

        foreach ($tempDirectories as $dir) {
            if (Storage::disk('public')->exists($dir)) {
                $files = Storage::disk('public')->allFiles($dir);
                foreach ($files as $file) {
                    $lastModified = Storage::disk('public')->lastModified($file);
                    if ($lastModified <= $cutoffTime) {
                        Storage::disk('public')->delete($file);
                        $deletedCount++;
                    }
                }
            }
        }

        $this->info("Pembersihan selesai. Total {$deletedCount} berkas sementara berhasil dihapus.");
        Log::info("CleanTemporaryUploads: Dihapus {$deletedCount} berkas sementara.");

        return Command::SUCCESS;
    }
}
