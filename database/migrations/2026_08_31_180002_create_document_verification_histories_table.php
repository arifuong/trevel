<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel audit trail untuk mencatat setiap perubahan status dokumen.
     * Setiap kali admin verify/reject atau jamaah resubmit, satu baris history dibuat.
     */
    public function up(): void
    {
        Schema::create('document_verification_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_member_id')->constrained('registration_members')->cascadeOnDelete();
            $table->string('status'); // menunggu_verifikasi, disetujui, ditolak
            $table->text('reason')->nullable(); // alasan penolakan (jika ditolak)
            $table->foreignId('action_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_verification_histories');
    }
};
