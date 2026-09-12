<?php

use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Smart Migration untuk data booking existing yang sudah berstatus LUNAS:
     * - Jika jadwal keberangkatan paket masih di masa depan (>= hari ini atau belum ditentukan):
     *   Alihkan status ke 'menunggu_kelengkapan_keberangkatan' karena jamaah wajib
     *   melengkapi Visa, Vaksin Meningitis, dan Foto Visa sebelum dapat berstatus 'berangkat'.
     * - Jika jadwal keberangkatan paket sudah lewat di masa lalu:
     *   Biarkan atau alihkan ke 'berangkat' (arsip perjalanan selesai).
     */
    public function up(): void
    {
        $today = Carbon::today()->toDateString();

        // 1. Booking berstatus 'lunas' yang keberangkatannya di masa depan -> alihkan ke menunggu_kelengkapan_keberangkatan
        $futureLunasRegistrations = DB::table('registrations')
            ->join('packages', 'registrations.package_id', '=', 'packages.id')
            ->where('registrations.status', 'lunas')
            ->where(function ($q) use ($today) {
                $q->whereNull('packages.departure_date')
                  ->orWhere('packages.departure_date', '>=', $today);
            })
            ->select('registrations.id')
            ->pluck('id');

        if ($futureLunasRegistrations->isNotEmpty()) {
            DB::table('registrations')
                ->whereIn('id', $futureLunasRegistrations)
                ->update(['status' => 'menunggu_kelengkapan_keberangkatan']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('registrations')
            ->where('status', 'menunggu_kelengkapan_keberangkatan')
            ->update(['status' => 'lunas']);
    }
};
