<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->text('about_summary')->nullable()->after('name');
            $table->text('about_description')->nullable()->after('about_summary');
            $table->string('ppiu_number', 100)->nullable()->default('IZIN KEMENAG RI NOMOR U.255 TAHUN 2020')->after('about_description');
            $table->string('pihk_number', 100)->nullable()->default('IZIN KEMENAG RI NOMOR 599 TAHUN 2021')->after('ppiu_number');
            $table->text('visi')->nullable()->after('hero_image');
            $table->json('misi')->nullable()->after('visi');
            $table->json('tujuan')->nullable()->after('misi');
            $table->json('keunggulan')->nullable()->after('tujuan');
            $table->json('stats')->nullable()->after('keunggulan');
        });

        // Seed data default yang sudah ada saat ini ke row id = 1
        $defaultMisi = [
            'Membantu para calon Jemaah Umrah/Haji dalam pelaksanaan ibadahnya agar benar dan sempurna untuk mencapai ibadah yang mabrur.',
            'Mengembangkan perusahaan penyelenggara perjalanan ibadah Umrah/Haji yang baik, serta menjadi pembimbing ibadah yang siqah dengan pelayanan prima.',
            "Mengembangkan Ukhuwah Islamiyah, Silaturrahim dan kerja sama untuk mencapai kehidupan yang rahmatan lil 'alamin.",
        ];

        $defaultTujuan = [
            'Mengelola usaha penyelenggara perjalanan ibadah yang berdimensi dua kebaikan.',
            'Menjadi salah satu sumber pendapatan yang barokah.',
            'Menjadi pintu masuk untuk mengembangkan berbagai usaha lain yang berkaitan.',
        ];

        $defaultKeunggulan = [
            [
                'icon' => 'currency',
                'title' => 'Harga Paket Terjangkau',
                'desc' => 'Harga paket relatif lebih murah dengan pelayanan terbaik.',
            ],
            [
                'icon' => 'sliders',
                'title' => 'Pilihan Paket Fleksibel',
                'desc' => 'Disediakan pilihan paket sesuai dengan kemampuan/kebutuhan jamaah.',
            ],
            [
                'icon' => 'kaaba',
                'title' => 'Fasilitas Umrah Sunnah',
                'desc' => 'Memfasilitasi jamaah untuk melakukan Umrah sunnah.',
            ],
            [
                'icon' => 'bolt',
                'title' => 'Fast Track Imigrasi',
                'desc' => 'Layanan cepat imigrasi di bandara Soekarno Hatta.',
            ],
            [
                'icon' => 'lounge',
                'title' => 'Lounge Bandara',
                'desc' => 'Di bandara disediakan lounge umrah.',
            ],
            [
                'icon' => 'guide',
                'title' => 'Pembimbing Profesional',
                'desc' => 'Pembimbing yang profesional di bidangnya.',
            ],
        ];

        $defaultStats = [
            ['number' => '12000', 'suffix' => '+', 'label' => 'Jamaah Diberangkatkan'],
            ['number' => '99', 'suffix' => '%', 'label' => 'Tingkat Kepuasan'],
            ['number' => '14', 'suffix' => '+', 'label' => 'Tahun Pengalaman'],
            ['number' => '100', 'suffix' => '%', 'label' => 'Izin Resmi Kemenag'],
        ];

        DB::table('company_profiles')->where('id', 1)->update([
            'about_summary' => 'PT. ZEIN INTERNASIONAL (Zeintour) adalah salah satu perusahaan penyelenggara perjalanan Ibadah Umrah yang didirikan pada 19 Oktober 2012 di Bandung oleh H. Zenal Abidin, dengan motivasi membangun dua kebaikan (kebaikan dunia dan akhirat).',
            'about_description' => 'Berdasarkan motivasi tersebut, Zeintour bertekad untuk menjadi pelayan para tamu Allah SWT, memberikan kemudahan atas segala hal yang berkaitan dengan proses pelaksanaan ibadahnya, mulai dari persiapan keberangkatan, pelaksanaan ibadah di Tanah Suci, sampai kembali ke tanah air dengan ikhlas dan tawakal kepadaNya, agar semua rangkaian ibadahnya diterima Allah SWT.',
            'ppiu_number' => 'IZIN KEMENAG RI NOMOR U.255 TAHUN 2020',
            'pihk_number' => 'IZIN KEMENAG RI NOMOR 599 TAHUN 2021',
            'visi' => "Menjadi penyelenggara Haji dan Umrah dengan pelayanan terbaik berbasis Al Qur'an di Indonesia.",
            'misi' => json_encode($defaultMisi),
            'tujuan' => json_encode($defaultTujuan),
            'keunggulan' => json_encode($defaultKeunggulan),
            'stats' => json_encode($defaultStats),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'about_summary',
                'about_description',
                'ppiu_number',
                'pihk_number',
                'visi',
                'misi',
                'tujuan',
                'keunggulan',
                'stats',
            ]);
        });
    }
};
