<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to remove all OTP tables and columns.
     */
    public function up(): void
    {
        // 1. Hapus tabel otp_verifications
        Schema::dropIfExists('otp_verifications');

        // 2. Hapus kolom-kolom terkait OTP pada tabel users
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('users', 'otp_code')) {
                $columnsToDrop[] = 'otp_code';
            }
            if (Schema::hasColumn('users', 'otp_expires_at')) {
                $columnsToDrop[] = 'otp_expires_at';
            }
            if (Schema::hasColumn('users', 'phone_verified_at')) {
                $columnsToDrop[] = 'phone_verified_at';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->string('otp_code')->nullable()->after('phone_verified_at');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
        });
    }
};
