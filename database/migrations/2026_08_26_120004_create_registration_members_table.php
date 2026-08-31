<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PRD Section 9 - Data Anggota Keluarga (per orang):
     * NIK + file KTP, No. KK + file KK, No. Paspor + file Paspor,
     * Hubungan keluarga, file Buku Nikah (jika menikah),
     * file Akta Kelahiran (jika anak)
     *
     * PRD Section 11 - Status Dokumen:
     * Menunggu Verifikasi → Disetujui / Ditolak (dengan alasan)
     *
     * name: diperlukan untuk identifikasi anggota keluarga.
     */
    public function up(): void
    {
        Schema::create('registration_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('nik');
            $table->string('ktp_file')->nullable();
            $table->string('no_kk');
            $table->string('kk_file')->nullable();
            $table->string('no_passport')->nullable();
            $table->string('passport_file')->nullable();
            $table->string('relationship');
            $table->string('marriage_book_file')->nullable();
            $table->string('birth_certificate_file')->nullable();
            $table->string('document_status')->default('menunggu_verifikasi');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_members');
    }
};
