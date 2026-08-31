<?php

namespace App\Helpers;

class TerbilangHelper
{
    private static array $angka = [
        '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
    ];

    /**
     * Konversi bilangan numerik ke teks terbilang Bahasa Indonesia.
     * Contoh: 33900000 -> "Tiga Puluh Tiga Juta Sembilan Ratus Ribu Rupiah"
     */
    public static function terbilang(float|int|string $nominal, bool $withRupiah = true): string
    {
        $nominal = abs((float) $nominal);

        if ($nominal == 0) {
            return $withRupiah ? 'Nol Rupiah' : 'Nol';
        }

        $hasil = self::penyebut($nominal);
        $clean = preg_replace('/\s+/', ' ', trim($hasil));

        return $clean . ($withRupiah ? ' Rupiah' : '');
    }

    private static function penyebut(float|int $nilai): string
    {
        $nilai = floor($nilai);
        $hasil = '';

        if ($nilai < 12) {
            $hasil = ' ' . self::$angka[$nilai];
        } elseif ($nilai < 20) {
            $hasil = self::penyebut($nilai - 10) . ' Belas';
        } elseif ($nilai < 100) {
            $hasil = self::penyebut(floor($nilai / 10)) . ' Puluh' . self::penyebut($nilai % 10);
        } elseif ($nilai < 200) {
            $hasil = ' Seratus' . self::penyebut($nilai - 100);
        } elseif ($nilai < 1000) {
            $hasil = self::penyebut(floor($nilai / 100)) . ' Ratus' . self::penyebut($nilai % 100);
        } elseif ($nilai < 2000) {
            $hasil = ' Seribu' . self::penyebut($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $hasil = self::penyebut(floor($nilai / 1000)) . ' Ribu' . self::penyebut($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            $hasil = self::penyebut(floor($nilai / 1000000)) . ' Juta' . self::penyebut(fmod($nilai, 1000000));
        } elseif ($nilai < 1000000000000) {
            $hasil = self::penyebut(floor($nilai / 1000000000)) . ' Miliar' . self::penyebut(fmod($nilai, 1000000000));
        } elseif ($nilai < 1000000000000000) {
            $hasil = self::penyebut(floor($nilai / 1000000000000)) . ' Triliun' . self::penyebut(fmod($nilai, 1000000000000));
        }

        return $hasil;
    }
}
