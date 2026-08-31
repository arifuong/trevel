<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PRD Section 9 - Data Perlengkapan:
     * Status (Belum Dikirim/Sudah Dikirim) per jamaah
     *
     * PRD Section 11 - Status Perlengkapan:
     * Belum Dikirim → Sudah Dikirim/Diterima
     */
    public function up(): void
    {
        Schema::create('equipment_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('belum_dikirim'); // belum_dikirim / sudah_dikirim
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_distributions');
    }
};
