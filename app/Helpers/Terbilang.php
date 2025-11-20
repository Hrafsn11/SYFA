<?php

namespace App\Helpers;

class Terbilang
{
    /**
     * Convert number to Indonesian words
     *
     * @param int|float $angka
     * @return string
     */
    public static function convert($angka): string
    {
        $angka = abs($angka);
        $huruf = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
            'sepuluh', 'sebelas'
        ];
        
        $temp = '';
        
        if ($angka < 12) {
            $temp = ' ' . $huruf[$angka];
        } elseif ($angka < 20) {
            $temp = self::convert($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            $temp = self::convert($angka / 10) . ' puluh' . self::convert($angka % 10);
        } elseif ($angka < 200) {
            $temp = ' seratus' . self::convert($angka - 100);
        } elseif ($angka < 1000) {
            $temp = self::convert($angka / 100) . ' ratus' . self::convert($angka % 100);
        } elseif ($angka < 2000) {
            $temp = ' seribu' . self::convert($angka - 1000);
        } elseif ($angka < 1000000) {
            $temp = self::convert($angka / 1000) . ' ribu' . self::convert($angka % 1000);
        } elseif ($angka < 1000000000) {
            $temp = self::convert($angka / 1000000) . ' juta' . self::convert($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $temp = self::convert($angka / 1000000000) . ' milyar' . self::convert(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $temp = self::convert($angka / 1000000000000) . ' trilyun' . self::convert(fmod($angka, 1000000000000));
        }
        
        return trim($temp);
    }

    /**
     * Convert amount to currency text (Rupiah)
     *
     * @param int|float $angka
     * @return string
     */
    public static function rupiah($angka): string
    {
        return ucwords(self::convert($angka) . ' rupiah');
    }
}
