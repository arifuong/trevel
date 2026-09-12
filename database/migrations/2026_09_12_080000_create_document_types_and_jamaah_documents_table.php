<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Jenis Dokumen (document_types)
        if (!Schema::hasTable('document_types')) {
            Schema::create('document_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('phase')->default('awal'); // 'awal' atau 'keberangkatan'
                $table->boolean('is_required')->default(true);
                $table->text('description')->nullable();
                $table->string('allowed_mimes')->default('jpg,jpeg,png,webp,pdf');
                $table->unsignedInteger('max_size_kb')->default(10240); // 10MB
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('document_types', 'phase')) {
                Schema::table('document_types', function (Blueprint $table) {
                    $table->string('phase')->default('awal')->after('code');
                });
            }
        }

        // 2. Tabel Dokumen Jamaah (jamaah_documents)
        if (!Schema::hasTable('jamaah_documents')) {
            Schema::create('jamaah_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('registration_member_id')->constrained('registration_members')->cascadeOnDelete();
                $table->foreignId('document_type_id')->constrained('document_types')->cascadeOnDelete();
                $table->string('file_path');
                $table->string('status')->default('menunggu_verifikasi'); // menunggu_verifikasi, valid, ditolak
                $table->text('rejection_reason')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();

                $table->unique(['registration_member_id', 'document_type_id'], 'member_doctype_unique');
            });
        }

        // 3. Seed Master Dokumen Tahap Awal & Tahap Keberangkatan
        $now = now();
        $initialDocTypes = [
            // Tahap Awal
            [
                'name' => 'KTP (Kartu Tanda Penduduk)',
                'code' => 'KTP',
                'phase' => 'awal',
                'is_required' => true,
                'description' => 'Foto atau scan KTP asli yang jelas dan terbaca.',
                'allowed_mimes' => 'jpg,jpeg,png,webp',
                'max_size_kb' => 10240,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kartu Keluarga (KK)',
                'code' => 'KK',
                'phase' => 'awal',
                'is_required' => true,
                'description' => 'Foto atau scan Kartu Keluarga asli/legalisir.',
                'allowed_mimes' => 'jpg,jpeg,png,webp',
                'max_size_kb' => 10240,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Buku Paspor',
                'code' => 'PASPOR',
                'phase' => 'awal',
                'is_required' => false,
                'description' => 'Scan halaman identitas paspor dengan masa berlaku minimal 7 bulan.',
                'allowed_mimes' => 'jpg,jpeg,png,webp',
                'max_size_kb' => 10240,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Buku Nikah',
                'code' => 'BUKU_NIKAH',
                'phase' => 'awal',
                'is_required' => false,
                'description' => 'Buku nikah bagi pendaftar suami-istri.',
                'allowed_mimes' => 'jpg,jpeg,png,webp',
                'max_size_kb' => 10240,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Akta Kelahiran',
                'code' => 'AKTA_LAHIR',
                'phase' => 'awal',
                'is_required' => false,
                'description' => 'Akta kelahiran bagi jamaah anak/balita.',
                'allowed_mimes' => 'jpg,jpeg,png,webp',
                'max_size_kb' => 10240,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Tahap Keberangkatan (3 Dokumen Baru Sesuai Spesifikasi)
            [
                'name' => 'Visa Umrah',
                'code' => 'VISA',
                'phase' => 'keberangkatan',
                'is_required' => true,
                'description' => 'File visa resmi yang diterbitkan oleh Kementerian Haji dan Umrah Arab Saudi (Format PDF / JPG).',
                'allowed_mimes' => 'jpg,jpeg,png,webp,pdf',
                'max_size_kb' => 10240,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sertifikat Vaksin Meningitis',
                'code' => 'VAKSIN_MENINGITIS',
                'phase' => 'keberangkatan',
                'is_required' => true,
                'description' => 'Sertifikat vaksinasi meningitis (Buku Kuning / ICV resmi Kemenkes).',
                'allowed_mimes' => 'jpg,jpeg,png,webp,pdf',
                'max_size_kb' => 10240,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Foto Visa',
                'code' => 'FOTO_VISA',
                'phase' => 'keberangkatan',
                'is_required' => true,
                'description' => 'Pas foto khusus visa ukuran 4x6 dengan latar belakang putih/biru, fokus wajah 80%.',
                'allowed_mimes' => 'jpg,jpeg,png,webp',
                'max_size_kb' => 10240,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($initialDocTypes as $docType) {
            DB::table('document_types')->updateOrInsert(
                ['code' => $docType['code']],
                $docType
            );
        }

        // 4. Backfill Data Dokumen Awal yang Sudah Ada pada RegistrationMember
        $docTypeMap = DB::table('document_types')->pluck('id', 'code');

        $members = DB::table('registration_members')->get();
        foreach ($members as $member) {
            $memberStatus = match ($member->document_status) {
                'disetujui' => 'valid',
                'ditolak' => 'ditolak',
                default => 'menunggu_verifikasi',
            };

            $filesToSync = [
                'KTP' => $member->ktp_file,
                'KK' => $member->kk_file,
                'PASPOR' => $member->passport_file,
                'BUKU_NIKAH' => $member->marriage_book_file,
                'AKTA_LAHIR' => $member->birth_certificate_file,
            ];

            foreach ($filesToSync as $code => $filePath) {
                if (!empty($filePath) && isset($docTypeMap[$code])) {
                    DB::table('jamaah_documents')->updateOrInsert(
                        [
                            'registration_member_id' => $member->id,
                            'document_type_id' => $docTypeMap[$code],
                        ],
                        [
                            'file_path' => $filePath,
                            'status' => $memberStatus,
                            'rejection_reason' => $member->rejection_reason,
                            'verified_by' => $member->verified_by,
                            'verified_at' => $member->verified_at,
                            'created_at' => $member->created_at ?? $now,
                            'updated_at' => $member->updated_at ?? $now,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jamaah_documents');
        Schema::dropIfExists('document_types');
    }
};
