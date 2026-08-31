<?php

namespace App\Services;

class OcrService
{
    /**
     * Kode Wilayah Provinsi Indonesia (11 s/d 96)
     */
    protected const VALID_PROVINCES = [
        '11', '12', '13', '14', '15', '16', '17', '18', '19',
        '21', '31', '32', '33', '34', '35', '36', '51', '52',
        '53', '61', '62', '63', '64', '65', '71', '72', '73',
        '74', '75', '76', '81', '82', '91', '92', '93', '94', '95', '96'
    ];

    /**
     * Bersihkan teks dari karakter non-digit dan normalisasi kesalahan umum OCR
     */
    public function normalizeDigits(?string $text): string
    {
        if (!$text) {
            return '';
        }

        // Konversi huruf yang sering tertukar dengan angka
        $replacements = [
            'O' => '0', 'o' => '0', 'Q' => '0', 'q' => '0', 'D' => '0',
            'I' => '1', 'i' => '1', 'l' => '1', 'L' => '1', '|' => '1', '!' => '1',
            'Z' => '2', 'z' => '2',
            'E' => '3', 'e' => '3',
            'A' => '4', 'a' => '4',
            'S' => '5', 's' => '5',
            'b' => '6',
            'T' => '7', 't' => '7',
            'B' => '8',
            'g' => '9', 'G' => '9',
        ];

        $cleaned = strtr($text, $replacements);
        return preg_replace('/[^0-9]/', '', $cleaned);
    }

    /**
     * Validasi apakah string adalah tepat 16 digit angka
     */
    public function isValid16Digits(?string $text): bool
    {
        return !empty($text) && preg_match('/^[0-9]{16}$/', $text) === 1;
    }

    /**
     * Ekstraksi data KTP dari teks hasil scan OCR
     * Menghasilkan: nik (16 digit), nik_confidence, nama, tempat_lahir, tgl_lahir, jenis_kelamin, alamat
     */
    public function parseKtp(string $rawText): array
    {
        $result = [
            'nik' => '',
            'nik_confidence' => 'none',
            'name' => '',
            'birth_place' => '',
            'birth_date' => '',
            'gender' => '',
            'address' => '',
            'raw_text' => $rawText,
        ];

        if (trim($rawText) === '') {
            return $result;
        }

        $lines = array_values(array_filter(array_map('trim', explode("\n", $rawText))));

        // 1. Ekstraksi NIK (Berdasarkan Kedekatan Label NIK)
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            if (preg_match('/NIK|N\.?I\.?K|N!K|N\s*I\s*K|N1K/i', $line)) {
                // Bersihkan label NIK di baris yang sama
                $valuePart = preg_replace('/^.*?(?:NIK|N\.?I\.?K|N!K|N\s*I\s*K|N1K)\s*[:=\s\-]*/i', '', $line);
                $cleaned = $this->normalizeDigits($valuePart);
                if (preg_match('/[0-9]{16}/', $cleaned, $match)) {
                    $result['nik'] = $match[0];
                    $result['nik_confidence'] = 'high';
                    break;
                }

                // Cek baris berikutnya jika NIK berada di baris baru
                if (isset($lines[$i + 1])) {
                    $nextLine = $lines[$i + 1];
                    if (!preg_match('/nama|neme|tempat|tgl|lahir/i', $nextLine)) {
                        $strippedNext = preg_replace('/^.*?(?:NIK|N\.?I\.?K|N!K|N\s*I\s*K|N1K|NO|NOMOR)\s*[:=\s\-]*/i', '', $nextLine);
                        $cleanedNext = $this->normalizeDigits($strippedNext);
                        if (preg_match('/[0-9]{16}/', $cleanedNext, $matchNext)) {
                            $result['nik'] = $matchNext[0];
                            $result['nik_confidence'] = 'high';
                            break;
                        }
                    }
                }
            }
        }

        // Fallback NIK: Cari seluruh teks untuk kandidat 16 digit yang memiliki kode provinsi valid
        if (empty($result['nik'])) {
            $strippedText = preg_replace('/(?:NIK|N\.?I\.?K|N!K|N\s*I\s*K|N1K|NO|NOMOR|KARTU|KELUARGA|PROVINSI|KABUPATEN|KOTA|REPUBLIK|INDONESIA)/i', ' ', $rawText);
            $allDigits = $this->normalizeDigits($strippedText);
            if (preg_match_all('/[0-9]{16}/', $allDigits, $matches)) {
                foreach ($matches[0] as $candidate) {
                    $provCode = substr($candidate, 0, 2);
                    if (in_array($provCode, self::VALID_PROVINCES, true)) {
                        $result['nik'] = $candidate;
                        $result['nik_confidence'] = 'medium';
                        break;
                    }
                }

                if (empty($result['nik']) && !empty($matches[0])) {
                    $result['nik'] = $matches[0][0];
                    $result['nik_confidence'] = 'low';
                }
            }
        }

        // 2. Ekstraksi Nama
        foreach ($lines as $line) {
            if (preg_match('/(?:Nama|Neme|Name)\s*[:\s\-]\s*([A-Za-z\s\.,\']+)/i', $line, $match)) {
                $name = trim(preg_replace('/^[:\-\s]+/', '', $match[1]));
                $name = trim(preg_replace('/[^A-Za-z\s\.,\']/', '', $name));
                if (strlen($name) >= 3 && !preg_match('/tempat|tgl|lahir|nik|gol|darah/i', $name)) {
                    $result['name'] = strtoupper($name);
                    break;
                }
            }
        }

        // 3. Ekstraksi Tempat & Tanggal Lahir
        foreach ($lines as $line) {
            if (preg_match('/(?:Tempat|Tmp|Tgl|Lahir)[^\n\r:]*[:\s\-]+([A-Za-z\s]+)[,\s]+(\d{1,2}[-\/\s\.]\d{1,2}[-\/\s\.]\d{2,4})/i', $line, $match)) {
                $result['birth_place'] = strtoupper(trim($match[1]));
                $rawDate = str_replace(['/', '.', ' '], '-', $match[2]);
                $parts = explode('-', $rawDate);
                if (count($parts) === 3) {
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $year = strlen($parts[2]) === 2 ? '19' . $parts[2] : $parts[2];
                    $result['birth_date'] = "{$year}-{$month}-{$day}";
                }
                break;
            }
        }

        // 4. Jenis Kelamin
        if (preg_match('/LAKI|LAK!-LAK!|LAKI-LAKI|PRIA/i', $rawText)) {
            $result['gender'] = 'laki-laki';
        } elseif (preg_match('/PEREMPUAN|WANITA/i', $rawText)) {
            $result['gender'] = 'perempuan';
        }

        // 5. Alamat
        foreach ($lines as $line) {
            if (preg_match('/(?:Alamat|Alamat\s*[:\s])([^\n\r]+)/i', $line, $match)) {
                $addr = trim(preg_replace('/^[:\-\s]+/', '', $match[1]));
                if (strlen($addr) >= 3) {
                    $result['address'] = strtoupper($addr);
                }
                break;
            }
        }

        return $result;
    }

    /**
     * Ekstraksi No. KK dari teks hasil scan Kartu Keluarga
     *
     * Strategi:
     *  1. Pembentukan NIK Exclusion List dari tabel anggota keluarga.
     *  2. Pengumpulan seluruh kandidat 16 digit (sebaris label, di bawah label, di bawah judul KK, baris No. header, digit terpisah).
     *  3. Scoring multi-faktor (kedekatan label, area header, validitas kode provinsi, penalti NIK/tabel/tanggal).
     *  4. Pemilihan kandidat dengan skor tertinggi.
     */
    public function parseKk(string $rawText): array
    {
        $result = [
            'no_kk' => '',
            'no_kk_confidence' => 'none',
            'family_niks' => [],
            'raw_text' => $rawText,
        ];

        if (trim($rawText) === '') {
            return $result;
        }

        $lines = array_values(array_filter(array_map('trim', explode("\n", $rawText))));

        // 1. Deteksi Batas Tabel & NIK Exclusions
        $nikExclusions = [];
        $tableStartLineIndex = count($lines);

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            // Deteksi header tabel anggota keluarga
            if (preg_match('/(?:Nama\s+Lengkap|Jenis\s+Kelamin|Tempat\s+Lahir|Tanggal\s+Lahir|Status\s+Perkawinan)/i', $line)) {
                if ($tableStartLineIndex === count($lines)) {
                    $tableStartLineIndex = $i;
                }
            }

            // Baris yang mengandung label NIK (bukan No. KK / Kartu Keluarga)
            if (preg_match('/\bNIK\b|N\.?I\.?K\b/i', $line) && !preg_match('/KARTU\s*KELUARGA|KARTU\s*KLUARGA|NO\.?\s*KK|NOMOR\s*KK/i', $line)) {
                $digitsOnLine = $this->extractCleanDigitSequences($line);
                foreach ($digitsOnLine as $d) {
                    if (strlen($d) === 16) {
                        $nikExclusions[] = $d;
                    }
                }
                if (isset($lines[$i + 1])) {
                    $nextDigits = $this->extractCleanDigitSequences($lines[$i + 1]);
                    foreach ($nextDigits as $d) {
                        if (strlen($d) === 16) {
                            $nikExclusions[] = $d;
                        }
                    }
                }
            }

            // Baris tabel anggota keluarga (pola: "1. NAMA NIK: 320412..." atau "1 ROSIDAH 320306...")
            if (preg_match('/(?:NIK\s*[:=]\s*)?(\d[\d\s]{14,18}\d)/i', $line, $nikMatch)) {
                if ($i >= $tableStartLineIndex) {
                    $cleaned = preg_replace('/\s/', '', $nikMatch[1]);
                    if (strlen($cleaned) === 16) {
                        $nikExclusions[] = $cleaned;
                    }
                }
            }
        }
        $nikExclusions = array_unique($nikExclusions);

        // 2. Pengumpulan Seluruh Kandidat Nomor KK
        $candidates = [];
        $kkLabelPattern = '/(?:NO\.?\s*(?:KK|KARTU\s*KELUARGA|KLUARGA)|NOMOR\s*(?:KK|KARTU\s*KELUARGA|KLUARGA))/i';
        $kkTitlePattern = '/(?:K[A4]RTU\s*K[E3]?[L1]U?A[R1]?[G6][A4]?|KARTU\s*KLUARGA|KARTU\s*KELUARGA)/i';
        $stopPattern = '/\b(?:Nama\s+Kepala|Alamat|NIK|Kepala\s+Keluarga|RT\/RW|Kelurahan|Kecamatan|Kabupaten|Kota|Provinsi)\b/i';

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            $isHeaderRegion = ($i < 8) || ($i < $tableStartLineIndex);

            // Pola A: Baris berlabel "NO. KK" / "NOMOR KK"
            if (preg_match($kkLabelPattern, $line)) {
                $valuePart = preg_replace('/^.*?(?:NO\.?\s*(?:KK|KARTU\s*KELUARGA|KLUARGA)|NOMOR\s*(?:KK|KARTU\s*KELUARGA|KLUARGA))\s*[:=\s\-]*/i', '', $line);
                $cand = $this->extractKkCandidate($valuePart ?? '');
                if ($cand) {
                    $candidates[] = [
                        'value' => $cand,
                        'source' => 'same_line_after_kk_label',
                        'isDirectlyRightOfLabel' => true,
                        'isHeaderRegion' => true,
                        'isNearKkLabel' => true,
                        'lineIndex' => $i,
                        'confidence' => 90
                    ];
                }

                // Cek 1-3 baris berikutnya di bawah label
                for ($j = 1; $j <= 3 && isset($lines[$i + $j]); $j++) {
                    $nextLine = $lines[$i + $j];
                    if (preg_match($stopPattern, $nextLine)) break;
                    $nextCand = $this->extractKkCandidate($nextLine);
                    if ($nextCand) {
                        $candidates[] = [
                            'value' => $nextCand,
                            'source' => 'line_below_kk_label',
                            'isDirectlyBelowKkLabel' => true,
                            'isHeaderRegion' => true,
                            'isNearKkLabel' => true,
                            'lineIndex' => $i + $j,
                            'confidence' => 85
                        ];
                        break;
                    }
                }
            }

            // Pola B: Baris berlabel "KARTU KELUARGA" / "KARTU KLUARGA"
            if (preg_match($kkTitlePattern, $line)) {
                $valuePart = preg_replace($kkTitlePattern, '', $line);
                $valuePart = preg_replace('/^(?:No\.?|Nomor|N0\.?|N\.)\s*[:=\s\-]*/i', '', $valuePart);
                $cand = $this->extractKkCandidate($valuePart);
                if ($cand) {
                    $candidates[] = [
                        'value' => $cand,
                        'source' => 'same_line_after_kk_title',
                        'isDirectlyRightOfLabel' => true,
                        'isHeaderRegion' => true,
                        'isNearKkLabel' => true,
                        'lineIndex' => $i,
                        'confidence' => 90
                    ];
                }

                // Cek 1-4 baris di bawah judul (seperti baris "No.  3277032911240001")
                for ($j = 1; $j <= 4 && isset($lines[$i + $j]); $j++) {
                    $nextLine = $lines[$i + $j];
                    if (preg_match($stopPattern, $nextLine)) break;
                    $stripped = preg_replace('/^(?:No\.?|Nomor|N0\.?|N\.)\s*[:=\s\-]*/i', '', $nextLine);
                    $nextCand = $this->extractKkCandidate($stripped);
                    if ($nextCand) {
                        $candidates[] = [
                            'value' => $nextCand,
                            'source' => 'line_below_kk_title',
                            'isDirectlyBelowKkTitle' => true,
                            'isHeaderRegion' => true,
                            'isNearKkLabel' => true,
                            'lineIndex' => $i + $j,
                            'confidence' => 88
                        ];
                        break;
                    }
                }
            }

            // Pola C: Baris diawali "No." / "Nomor" / "N0." di area header
            if ($isHeaderRegion && preg_match('/^(?:No\.?|Nomor|N0\.?|N\.)\s*[:=\s\-]*/i', $line)) {
                $stripped = preg_replace('/^(?:No\.?|Nomor|N0\.?|N\.)\s*[:=\s\-]*/i', '', $line);
                $cand = $this->extractKkCandidate($stripped);
                if ($cand) {
                    $candidates[] = [
                        'value' => $cand,
                        'source' => 'no_prefix_header_line',
                        'isNearKkLabel' => true,
                        'isHeaderRegion' => true,
                        'lineIndex' => $i,
                        'confidence' => 85
                    ];
                }
            }

            // Pola D: Angka 16 digit murni / spasi pada baris di area header
            if ($isHeaderRegion) {
                $seqs = $this->extractCleanDigitSequences($line);
                foreach ($seqs as $seq) {
                    $candidates[] = [
                        'value' => $seq,
                        'source' => 'clean_digits_header_line',
                        'isHeaderRegion' => true,
                        'lineIndex' => $i,
                        'confidence' => 80
                    ];
                }
            }
        }

        // Pola E: Multi-baris terpisah (4 baris berturut-turut masing-masing 4 digit)
        if (count($lines) >= 4) {
            for ($i = 0; $i <= min(count($lines) - 4, 6); $i++) {
                $chunk0 = preg_replace('/\D/', '', $lines[$i]);
                $chunk1 = preg_replace('/\D/', '', $lines[$i + 1]);
                $chunk2 = preg_replace('/\D/', '', $lines[$i + 2]);
                $chunk3 = preg_replace('/\D/', '', $lines[$i + 3]);
                if (strlen($chunk0) === 4 && strlen($chunk1) === 4 && strlen($chunk2) === 4 && strlen($chunk3) === 4) {
                    $combined = $chunk0 . $chunk1 . $chunk2 . $chunk3;
                    $candidates[] = [
                        'value' => $combined,
                        'source' => 'stacked_multiline_digits',
                        'isHeaderRegion' => true,
                        'lineIndex' => $i,
                        'confidence' => 75
                    ];
                }
            }
        }

        // 3. Scoring Seluruh Kandidat
        $scoredCandidates = [];
        $seen = [];

        foreach ($candidates as $cand) {
            $val = $cand['value'] ?? '';
            if (!$val || strlen($val) !== 16 || !ctype_digit($val)) continue;
            $key = $val . ($cand['source'] ?? '');
            if (isset($seen[$key])) continue;
            $seen[$key] = true;

            $score = 10;

            if (!empty($cand['isDirectlyRightOfLabel'])) {
                $score += 60;
            }
            if (!empty($cand['isDirectlyBelowKkTitle'])) {
                $score += 50;
            }
            if (!empty($cand['isDirectlyBelowKkLabel'])) {
                $score += 45;
            }
            if (!empty($cand['isNearKkLabel']) && empty($cand['isDirectlyRightOfLabel']) && empty($cand['isDirectlyBelowKkTitle'])) {
                $score += 35;
            }
            if (!empty($cand['isHeaderRegion'])) {
                $score += 30;
            }

            // Validitas Kode Provinsi Indonesia
            $provCode = substr($val, 0, 2);
            if (in_array($provCode, self::VALID_PROVINCES, true)) {
                $score += 25;
            } else {
                $score -= 15;
            }

            // Confidence
            $conf = $cand['confidence'] ?? 80;
            $score += (int)round($conf * 0.15);

            // Penalti NIK (Kritis)
            if (in_array($val, $nikExclusions, true)) {
                $score -= 120;
            }

            // Penalti Tabel Anggota Keluarga
            if (($cand['lineIndex'] ?? 0) >= $tableStartLineIndex) {
                $score -= 80;
            }

            // Penalti format tanggal / NIP
            if (preg_match('/^(?:19|20)\d{2}/', $val) && (str_contains($val, '2024') || str_contains($val, '2025') || str_contains($val, '2026') || str_contains($val, '1990'))) {
                $score -= 20;
            }

            $cand['score'] = $score;
            $scoredCandidates[] = $cand;
        }

        // Urutkan berdasarkan skor tertinggi
        usort($scoredCandidates, fn($a, $b) => $b['score'] <=> $a['score']);

        $best = $scoredCandidates[0] ?? null;
        if ($best && $best['score'] >= 45) {
            $result['no_kk'] = $best['value'];
            $result['no_kk_confidence'] = $best['score'] >= 70 ? 'high' : 'medium';
        }

        // 4. Kumpulkan family NIKs
        $all16Digits = [];
        foreach ($lines as $line) {
            foreach ($this->extractCleanDigitSequences($line) as $seq) {
                $all16Digits[] = $seq;
            }
        }
        $all16Digits = array_unique($all16Digits);
        $result['family_niks'] = array_values(array_filter($all16Digits, fn($c) => $c !== $result['no_kk']));

        return $result;
    }

    /**
     * Ekstrak kandidat 16 digit dari potongan teks, dengan koreksi OCR terbatas.
     *
     * Berbeda dengan normalizeDigits() yang mengkonversi SEMUA huruf menjadi angka,
     * fungsi ini hanya memperbaiki karakter yang "terlihat seperti angka" di dalam
     * substring yang sudah mayoritas digit.
     */
    protected function extractKkCandidate(string $text): ?string
    {
        // Hapus spasi, titik, strip, koma dari teks
        $cleaned = preg_replace('/[\s.\-,:]/', '', $text);

        // Cek dulu angka murni 16 digit
        if (preg_match('/(\d{16})/', $cleaned, $match)) {
            return $match[1];
        }

        // Koreksi terbatas: hanya karakter umum OCR-confused dalam konteks angka
        // O→0, I/l/|→1, S→5, B→8, G→6, Z→2
        $safeReplacements = [
            'O' => '0', 'o' => '0',
            'I' => '1', 'l' => '1', '|' => '1', '!' => '1',
            'S' => '5', 's' => '5',
            'B' => '8',
            'G' => '6',
            'Z' => '2', 'z' => '2',
        ];

        // Cari substring yang mayoritas digit (minimal 12 dari 16-20 karakter)
        if (preg_match_all('/[\d' . preg_quote('OoIl|!SsBGZz', '/') . ']{14,20}/', $cleaned, $chunks)) {
            foreach ($chunks[0] as $chunk) {
                // Hitung berapa digit murni
                $pureDigitCount = preg_match_all('/\d/', $chunk);
                // Minimal 10 digit murni dari total panjang
                if ($pureDigitCount >= 10) {
                    $corrected = strtr($chunk, $safeReplacements);
                    $corrected = preg_replace('/[^0-9]/', '', $corrected);
                    if (strlen($corrected) >= 16) {
                        return substr($corrected, 0, 16);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Ekstrak semua deretan digit murni dari sebuah baris teks.
     * Tidak melakukan konversi huruf → angka. Hanya mengambil angka yang
     * sudah pasti angka.
     */
    protected function extractCleanDigitSequences(string $text): array
    {
        // Hapus spasi di antara digit (KK format: "3204 1234 5678 9012")
        $normalized = preg_replace('/(\d)\s+(?=\d)/', '$1', $text);
        preg_match_all('/\d{16,}/', $normalized, $matches);

        $results = [];
        foreach ($matches[0] ?? [] as $m) {
            // Ambil setiap 16-digit substring
            for ($i = 0; $i <= strlen($m) - 16; $i++) {
                $results[] = substr($m, $i, 16);
            }
        }
        return $results;
    }

    /**
     * Ekstraksi No. Paspor dari teks hasil scan Paspor
     */
    public function parsePassport(string $rawText): array
    {
        $result = [
            'no_passport' => '',
            'name' => '',
            'raw_text' => $rawText,
        ];

        if (trim($rawText) === '') {
            return $result;
        }

        // Paspor Indonesia (1 huruf diikuti 7 atau 8 angka)
        if (preg_match('/\b([A-Z][0-9]{7,8})\b/i', $rawText, $match)) {
            $result['no_passport'] = strtoupper($match[1]);
        }

        // Format MRZ P<IDN
        if (preg_match('/P<IDN([A-Z<]+)/i', $rawText, $match)) {
            $nameClean = trim(preg_replace('/<+/', ' ', $match[1]));
            if ($nameClean) {
                $result['name'] = strtoupper($nameClean);
            }
        }

        return $result;
    }

    /**
     * Cross-check kecocokan NIK antara KTP dan Kartu Keluarga
     */
    public function crossCheckNik(?string $ktpNik, ?string $kkRawText): string
    {
        if (!$this->isValid16Digits($ktpNik) || empty($kkRawText)) {
            return 'unknown';
        }

        $kkData = $this->parseKk($kkRawText);
        if (in_array($ktpNik, $kkData['family_niks'], true) || str_contains($kkRawText, $ktpNik)) {
            return 'matched';
        }

        if (!empty($kkData['family_niks'])) {
            return 'different';
        }

        return 'unknown';
    }
}
