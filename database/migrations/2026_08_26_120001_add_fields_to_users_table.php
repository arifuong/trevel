<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah field sesuai PRD Section 9 (Data Akun Jamaah):
     * - phone: No. HP
     * - role: jamaah / admin (Section 3)
     * - phone_verified_at: status verifikasi OTP (Section 6.1)
     * - otp_code & otp_expires_at: untuk alur OTP WhatsApp (Section 6.1)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('role')->default('jamaah')->after('phone');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->string('otp_code')->nullable()->after('phone_verified_at');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'phone_verified_at', 'otp_code', 'otp_expires_at']);
        });
    }
};
