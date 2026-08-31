<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom audit verifikasi dokumen ke tabel registration_members.
     * Mencatat siapa yang memverifikasi/menolak dan kapan.
     */
    public function up(): void
    {
        Schema::table('registration_members', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable()->after('rejection_reason');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable()->after('verified_by');
            $table->foreignId('rejected_by')->nullable()->after('rejected_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_members', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['verified_at', 'verified_by', 'rejected_at', 'rejected_by']);
        });
    }
};
