<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom identitas KTP ke tabel registration_members:
     * - birth_place: Tempat Lahir sesuai KTP
     * - birth_date: Tanggal Lahir sesuai KTP
     * - gender: Jenis Kelamin sesuai KTP (laki-laki / perempuan)
     * - address: Alamat Tinggal sesuai KTP
     */
    public function up(): void
    {
        Schema::table('registration_members', function (Blueprint $table) {
            $table->string('birth_place', 100)->nullable()->after('name');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('gender', 20)->nullable()->after('birth_date');
            $table->text('address')->nullable()->after('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_members', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'birth_date',
                'gender',
                'address',
            ]);
        });
    }
};
