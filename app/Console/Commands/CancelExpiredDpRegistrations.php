<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelExpiredDpRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancel-expired-dp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis batalkan pendaftaran yang tidak membayar DP dalam 7 hari dan kembalikan kuota paket (PRD Section 11)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pengecekan pendaftaran DP yang kedaluwarsa...');

        $expiredRegistrations = Registration::where('status', Registration::STATUS_MENUNGGU_PEMBAYARAN_DP)
            ->whereHas('invoice', function ($query) {
                $query->where('due_date', '<', Carbon::now()->startOfDay());
            })
            ->with(['package', 'members', 'invoice'])
            ->get();

        if ($expiredRegistrations->isEmpty()) {
            $this->info('Tidak ada pendaftaran DP yang kedaluwarsa.');
            return Command::SUCCESS;
        }

        $canceledCount = 0;

        foreach ($expiredRegistrations as $registration) {
            DB::beginTransaction();

            try {
                $package = $registration->package;
                $memberCount = $registration->members->count();

                // 1. Ubah status registrasi menjadi 'dibatalkan'
                $registration->update([
                    'status' => Registration::STATUS_DIBATALKAN,
                ]);

                // 2. Status kuota kursi otomatis kembali tersedia via perhitungan dinamis non-dibatalkan
                if ($registration->packageVariant && $registration->packageVariant->status === 'sold_out') {
                    if ($registration->packageVariant->getRemainingQuota() > 0) {
                        $registration->packageVariant->update(['status' => 'aktif']);
                    }
                }
                if ($package && $package->status === 'sold_out') {
                    if ($package->getRemainingQuota() > 0) {
                        $package->update(['status' => 'aktif']);
                    }
                }

                DB::commit();

                $regId = $registration->registration_number;
                $logMsg = "[Auto Cancel DP] Pendaftaran {$regId} dibatalkan otomatis karena melewati batas waktu 7 hari. {$memberCount} kursi dikembalikan ke paket '{$package->name}'.";
                
                $this->warn($logMsg);
                Log::channel('single')->info($logMsg);

                $canceledCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Gagal membatalkan registrasi #{$registration->id}: " . $e->getMessage());
            }
        }

        $this->info("Selesai. Total {$canceledCount} pendaftaran berhasil dibatalkan dan kuota kursi dikembalikan.");

        return Command::SUCCESS;
    }
}
