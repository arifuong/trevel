<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PRD Section 11 - Status Pendaftaran Jamaah:
     * Menunggu Verifikasi Dokumen → Menunggu Pembayaran DP →
     * Menunggu Verifikasi Pembayaran DP → Jamaah (DP Disetujui) →
     * Menabung/Cicilan Pelunasan → Lunas → Berangkat
     * (+ Dibatalkan — bisa dari status manapun sebelum Lunas)
     *
     * Business Rule: Satu akun hanya bisa aktif di satu pendaftaran
     * pada satu waktu (Section 6.1).
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('menunggu_verifikasi_dokumen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
