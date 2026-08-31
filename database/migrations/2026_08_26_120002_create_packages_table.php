<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PRD Section 9 - Data Paket:
     * Nama paket, harga, tanggal keberangkatan, durasi, fasilitas,
     * kuota kursi, status (aktif/sold out)
     *
     * slug ditambahkan karena route existing /paket/{slug} sudah memakainya.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 15, 2);
            $table->date('departure_date');
            $table->integer('duration');
            $table->text('facilities');
            $table->integer('quota');
            $table->string('status')->default('aktif'); // aktif / sold_out
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
