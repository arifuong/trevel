<?php

namespace Tests\Unit;

use App\Services\OcrService;
use PHPUnit\Framework\TestCase;

class OcrServiceTest extends TestCase
{
    protected OcrService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OcrService();
    }

    public function test_it_normalizes_confused_ocr_digits()
    {
        // Letters O, I, S, B, Z, D, b -> 0, 1, 5, 8, 2, 0, 6
        $raw = "32O1-I234-S678-B9OZ";
        $cleaned = $this->service->normalizeDigits($raw);
        $this->assertEquals("3201123456788902", $cleaned);
        $this->assertEquals(16, strlen($cleaned));
    }

    public function test_it_parses_ktp_text_accurately()
    {
        $ktpRawText = "PROVINSI JAWA BARAT\n" .
                      "KABUPATEN BANDUNG\n" .
                      "NIK : 3204123456780001\n" .
                      "Nama : AHMAD ABDULLAH\n" .
                      "Tempat/Tgl Lahir : BANDUNG, 15-08-1990\n" .
                      "Jenis Kelamin : LAKI-LAKI\n" .
                      "Alamat : JL. RAYA SOREANG NO. 123\n" .
                      "RT/RW : 002/005\n" .
                      "Agama : ISLAM\n" .
                      "Status Perkawinan: KAWIN\n" .
                      "Pekerjaan : KARYAWAN SWASTA\n" .
                      "Kewarganegaraan : WNI\n" .
                      "Berlaku Hingga : SEUMUR HIDUP";

        $parsed = $this->service->parseKtp($ktpRawText);

        $this->assertEquals('3204123456780001', $parsed['nik']);
        $this->assertEquals('AHMAD ABDULLAH', $parsed['name']);
        $this->assertEquals('BANDUNG', $parsed['birth_place']);
        $this->assertEquals('1990-08-15', $parsed['birth_date']);
        $this->assertEquals('laki-laki', $parsed['gender']);
        $this->assertStringContainsString('JL. RAYA SOREANG', $parsed['address']);
    }

    public function test_it_parses_kk_text_accurately()
    {
        // Standard format: "No." on same line as number
        $kkRawText = "REPUBLIK INDONESIA\n" .
                     "KARTU KELUARGA\n" .
                     "No. 3204987654320005\n" .
                     "Nama Kepala Keluarga : AHMAD ABDULLAH\n" .
                     "Alamat : JL. RAYA SOREANG NO. 123";

        $parsed = $this->service->parseKk($kkRawText);

        $this->assertEquals('3204987654320005', $parsed['no_kk']);
        $this->assertEquals('high', $parsed['no_kk_confidence']);
    }

    public function test_kk_label_no_kk_on_same_line()
    {
        $text = "REPUBLIK INDONESIA\n" .
                "KARTU KELUARGA\n" .
                "NO. KK : 3204112233440001\n" .
                "Nama Kepala Keluarga : BUDI\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3204112233440001', $parsed['no_kk']);
        $this->assertEquals('high', $parsed['no_kk_confidence']);
    }

    public function test_kk_number_on_next_line_after_label()
    {
        $text = "REPUBLIK INDONESIA\n" .
                "KARTU KELUARGA\n" .
                "NO. KK :\n" .
                "3204112233440002\n" .
                "Nama Kepala Keluarga : SITI\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3204112233440002', $parsed['no_kk']);
        $this->assertEquals('high', $parsed['no_kk_confidence']);
    }

    public function test_kk_does_not_pick_family_nik_as_kk_number()
    {
        // The critical test: KK has No. KK + table with NIK entries
        $text = "REPUBLIK INDONESIA\n" .
                "KARTU KELUARGA\n" .
                "NO. KK : 3204999988887777\n" .
                "Nama Kepala Keluarga : AHMAD\n" .
                "NIK : 3204123456780001\n" .
                "NIK : 3204123456780002\n" .
                "NIK : 3204123456780003\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3204999988887777', $parsed['no_kk']);
        // Family NIKs should NOT include the KK number
        $this->assertNotContains('3204999988887777', $parsed['family_niks']);
        // Family NIKs should include the member NIKs
        $this->assertContains('3204123456780001', $parsed['family_niks']);
        $this->assertContains('3204123456780002', $parsed['family_niks']);
        $this->assertContains('3204123456780003', $parsed['family_niks']);
    }

    public function test_kk_spaced_number_format()
    {
        // No. KK printed with spaces: "3204 1234 5678 9012"
        $text = "KARTU KELUARGA\n" .
                "No. KK : 3204 1234 5678 9012\n" .
                "Nama : AHMAD\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3204123456789012', $parsed['no_kk']);
    }

    public function test_kk_ocr_confused_characters_limited_correction()
    {
        // Some digits read as letters: 32O4 → 3204 (O→0)
        $text = "KARTU KELUARGA\n" .
                "NO. KK : 32O4987654321OO5\n" .
                "Nama : BUDI\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3204987654321005', $parsed['no_kk']);
    }

    public function test_kk_without_explicit_label_falls_back_to_header()
    {
        // No "No. KK" label, but number appears in header area
        $text = "REPUBLIK INDONESIA\n" .
                "KARTU KELUARGA\n" .
                "3204556677889900\n" .
                "Nama Kepala Keluarga : AHMAD\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3204556677889900', $parsed['no_kk']);
    }

    public function test_kk_noisy_text_with_many_niks_selects_correct_kk()
    {
        // Real-world scenario: many 16-digit numbers, must pick the right one
        $text = "REPUBLIK INDONESIA\n" .
                "KARTU KELUARGA\n" .
                "No. KK : 3204111122223333\n" .
                "Nama Kepala Keluarga : AHMAD ABDULLAH\n" .
                "Alamat : JL. SOREANG NO. 123\n" .
                "1. AHMAD ABDULLAH NIK: 3204123456780001 Laki-laki 15-08-1990\n" .
                "2. SITI NURHALIZA NIK: 3204123456780002 Perempuan 20-03-1992\n" .
                "3. ANAK PERTAMA  NIK: 3204123456780003 Laki-laki 01-01-2015\n" .
                "4. ANAK KEDUA    NIK: 3204123456780004 Perempuan 05-05-2018\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3204111122223333', $parsed['no_kk']);
        $this->assertEquals('high', $parsed['no_kk_confidence']);
        // None of the family NIKs should be picked as KK number
        $this->assertNotEquals('3204123456780001', $parsed['no_kk']);
        $this->assertNotEquals('3204123456780002', $parsed['no_kk']);
    }

    public function test_kk_real_document_format_with_no_prefix_only()
    {
        // Berdasarkan dokumen KK asli yang di-upload user:
        // Pola: "KARTU KELUARGA" lalu baris "No. 3277032911240001"
        // Bukan "NO. KK" — hanya "No." diikuti angka langsung.
        // Di bawahnya ada 4 anggota keluarga dengan NIK masing-masing.
        $text = "KARTU KELUARGA\n" .
                "No. 3277032911240001\n" .
                "Nama Kepala Keluarga : ROSIDAH\n" .
                "Alamat : JL. PESANTREN GG MUNTISAH\n" .
                "RT/RW : 008/007\n" .
                "Desa/Kelurahan : CIBABAT\n" .
                "Kecamatan : CIMAHI UTARA\n" .
                "Kabupaten/Kota : KOTA CIMAHI\n" .
                "Provinsi : JAWA BARAT\n" .
                "No Nama Lengkap NIK Jenis Kelamin Tempat Lahir\n" .
                "1 ROSIDAH 3203066811840009 PEREMPUAN BANDUNG\n" .
                "2 SINTA ANGGRAENI 3203065104050004 PEREMPUAN CIMAHI\n" .
                "3 AHMAD FARID KAMALUDIN 3203060508070008 LAKI-LAKI BANDUNG\n" .
                "4 MUHAMAD IBRAHIM 3277032601170006 LAKI-LAKI CIMAHI\n";

        $parsed = $this->service->parseKk($text);

        // HARUS ambil No. KK, BUKAN NIK anggota
        $this->assertEquals('3277032911240001', $parsed['no_kk']);
        $this->assertEquals('high', $parsed['no_kk_confidence']);

        // NIK anggota harus masuk ke family_niks, BUKAN ke no_kk
        $this->assertContains('3203066811840009', $parsed['family_niks']);
        $this->assertContains('3203065104050004', $parsed['family_niks']);
        $this->assertContains('3203060508070008', $parsed['family_niks']);
        $this->assertContains('3277032601170006', $parsed['family_niks']);

        // No. KK TIDAK BOLEH ada di family_niks
        $this->assertNotContains('3277032911240001', $parsed['family_niks']);
    }

    public function test_kk_real_document_with_table_header_nik()
    {
        // Skenario OCR dimana kolom header "NIK" terbaca bersama data tabel
        // dan No. KK terbaca terpisah dari "KARTU KELUARGA"
        $text = "REPUBLIK INDONESIA\n" .
                "KARTU KELUARGA\n" .
                "No. 3277032911240001\n" .
                "Nama Kepala Keluarga : ROSIDAH\n" .
                "Alamat : JL. PESANTREN GG MUNTISAH RT/RW 008/007\n" .
                "No Nama Lengkap NIK Jenis Kelamin Tempat Lahir Tanggal Lahir\n" .
                "(1) (2) (3) (4) (5)\n" .
                "1 ROSIDAH 3203066811840009 PEREMPUAN BANDUNG 29-11-1984\n" .
                "2 SINTA ANGGRAENI 3203065104050004 PEREMPUAN CIMAHI 11-04-2005\n" .
                "3 AHMAD FARID KAMALUDIN 3203060508070008 LAKI-LAKI BANDUNG 05-08-2007\n" .
                "4 MUHAMAD IBRAHIM 3277032601170006 LAKI-LAKI CIMAHI 26-01-2017\n" .
                "Dikeluarkan Tanggal: 29-11-2024\n" .
                "NIP. 196502271991022001\n";

        $parsed = $this->service->parseKk($text);

        $this->assertEquals('3277032911240001', $parsed['no_kk']);
        $this->assertEquals('high', $parsed['no_kk_confidence']);

        // NIP (19 digit) TIDAK BOLEH masuk ke family_niks atau no_kk
        $this->assertNotEquals('1965022719910220', $parsed['no_kk']);
    }

    public function test_kk_handles_kartu_kluarga_typo_and_wide_spacing()
    {
        // Variasi teks seperti "KARTU KLUARGA" dengan spasi lebar "No.   3277032911240001"
        $text = "KARTU KLUARGA\n" .
                "No.   3277032911240001\n" .
                "Nama Kepala Keluarga : ROSIDAH\n" .
                "NIK: 3203066811840009\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3277032911240001', $parsed['no_kk']);
        $this->assertEquals('high', $parsed['no_kk_confidence']);
    }

    public function test_kk_handles_stacked_split_multiline_digits()
    {
        // OCR membaca 4 baris angka 4-digit berturut-turut di area header
        $text = "KARTU KELUARGA\n" .
                "3204\n" .
                "1234\n" .
                "5678\n" .
                "9012\n" .
                "Nama Kepala Keluarga : AHMAD\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3204123456789012', $parsed['no_kk']);
        $this->assertEquals('high', $parsed['no_kk_confidence']);
    }

    public function test_kk_candidate_scoring_prefers_header_over_table()
    {
        // Ada angka 16 digit di header dan ada banyak di tabel
        $text = "REPUBLIK INDONESIA\n" .
                "KARTU KELUARGA\n" .
                "No. 3277032911240001\n" .
                "Nama Kepala Keluarga : ROSIDAH\n" .
                "No Nama Lengkap NIK Jenis Kelamin\n" .
                "1 ROSIDAH 3203066811840009 PEREMPUAN\n" .
                "2 SINTA 3203065104050004 PEREMPUAN\n";

        $parsed = $this->service->parseKk($text);
        $this->assertEquals('3277032911240001', $parsed['no_kk']);
        $this->assertNotEquals('3203066811840009', $parsed['no_kk']);
    }

    public function test_it_parses_passport_text_accurately()
    {
        $passRawText = "PASPOR / PASSPORT\n" .
                       "INDONESIA\n" .
                       "No. Paspor : B1234567\n" .
                       "P<IDNABDULLAH<<AHMAD<<<<<<<<<<<<<<<<<<<<<<<\n" .
                       "B1234567<8IDN9008151M2810204<<<<<<<<<<<<<<02";

        $parsed = $this->service->parsePassport($passRawText);

        $this->assertEquals('B1234567', $parsed['no_passport']);
        $this->assertStringContainsString('ABDULLAH', $parsed['name']);
    }

    public function test_it_handles_empty_or_noisy_text_gracefully()
    {
        $parsedKtp = $this->service->parseKtp("");
        $this->assertEquals('', $parsedKtp['nik']);
        $this->assertEquals('', $parsedKtp['name']);

        $parsedKk = $this->service->parseKk("Gambar kabur dan tidak terbaca");
        $this->assertEquals('', $parsedKk['no_kk']);

        $parsedPass = $this->service->parsePassport("Invalid text");
        $this->assertEquals('', $parsedPass['no_passport']);
    }

    public function test_it_validates_16_digits()
    {
        $this->assertTrue($this->service->isValid16Digits('3204123456780001'));
        $this->assertFalse($this->service->isValid16Digits('320412345678000'));
        $this->assertFalse($this->service->isValid16Digits('32041234567800011'));
        $this->assertFalse($this->service->isValid16Digits('320412345678000A'));
        $this->assertFalse($this->service->isValid16Digits(''));
    }

    public function test_it_cross_checks_nik_with_kk()
    {
        $ktpNik = '3204123456780001';
        $kkTextMatched = "KARTU KELUARGA\nNo. 3204987654320005\n1. AHMAD (NIK: 3204123456780001)\n2. SITI (NIK: 3204123456780002)";
        $kkTextDifferent = "KARTU KELUARGA\nNo. 3204987654320005\n1. BUDI (NIK: 3204999999990001)";

        $this->assertEquals('matched', $this->service->crossCheckNik($ktpNik, $kkTextMatched));
        $this->assertEquals('different', $this->service->crossCheckNik($ktpNik, $kkTextDifferent));
        $this->assertEquals('unknown', $this->service->crossCheckNik('', $kkTextMatched));
    }
}
