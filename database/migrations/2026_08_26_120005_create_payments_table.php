<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PRD Section 9 - Data Pembayaran:
     * Jenis (DP/pelunasan), nominal, bukti transfer,
     * status verifikasi, tanggal
     *
     * PRD Section 11 - Status Pembayaran:
     * Menunggu Verifikasi → Disetujui / Ditolak
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->string('type');              // dp / pelunasan
            $table->decimal('amount', 15, 2);    // nominal
            $table->string('proof_file');         // bukti transfer
            $table->string('status')->default('menunggu_verifikasi');
            $table->timestamps();                // tanggal = created_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
