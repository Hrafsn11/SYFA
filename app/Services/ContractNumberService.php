<?php

namespace App\Services;

use App\Models\PengajuanPeminjaman;
use App\Models\PengajuanInvestasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContractNumberService
{
    /**
     * Generate nomor kontrak 
     * Format: KODE_PERUSAHAAN-RUNNING_NUMBER-DDMMYYYY
     * 
     * 
     * @param string 
     * @param string 
     * @param string|null 
     * @return string 
     */
    public static function generate(string $kodePerusahaan, string $jenisPembiayaan, ?string $tanggal = null): string
    {
        if (empty($kodePerusahaan)) {
            throw new \InvalidArgumentException(
                'Kode perusahaan debitur tidak boleh kosong untuk generate nomor kontrak.'
            );
        }

        return DB::transaction(function () use ($kodePerusahaan, $jenisPembiayaan, $tanggal) {
            $lastContract = PengajuanPeminjaman::where('jenis_pembiayaan', $jenisPembiayaan)
                ->whereNotNull('no_kontrak')
                ->where('no_kontrak', '!=', '')
                ->orderBy('created_at', 'DESC')
                ->lockForUpdate()
                ->first();

            $runningNumber = 1;

            if ($lastContract && $lastContract->no_kontrak) {
                $parts = explode('-', $lastContract->no_kontrak);

                if (count($parts) >= 2) {
                    $lastNumber = (int) $parts[1];
                    $runningNumber = $lastNumber + 1;
                }
            }

            $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
            $formattedDate = $date->format('dmY');

            return strtoupper($kodePerusahaan) . '-' . $runningNumber . '-' . $formattedDate;
        });
    }

    /**
     * Validasi format nomor kontrak
     * 
     * @param string 
     * @return bool
     */
    public static function isValidFormat(string $nomorKontrak): bool
    {
        $pattern = '/^[A-Z0-9]{2,4}-\d+-\d{8}$/';
        return preg_match($pattern, $nomorKontrak) === 1;
    }

    /**
     * 
     * @param string
     * @return array|null 
     */
    public static function parse(string $nomorKontrak): ?array
    {
        if (!self::isValidFormat($nomorKontrak)) {
            return null;
        }

        $parts = explode('-', $nomorKontrak);

        return [
            'kode_perusahaan' => $parts[0],
            'running_number' => (int) $parts[1],
            'tanggal' => $parts[2],
        ];
    }

    /**
     * Generate nomor kontrak untuk investasi
     * Format: KODE_PERUSAHAAN-RUNNING_NUMBER-DDMMYYYY
     * 
     * @param string $kodePerusahaan
     * @param string $jenisDeposito (Reguler/Khusus/Finlog)
     * @param string|null $tanggal
     * @return string
     */
    public static function generateInvestasi(string $kodePerusahaan, string $jenisDeposito, ?string $tanggal = null): string
    {
        return DB::transaction(function () use ($kodePerusahaan, $jenisDeposito, $tanggal) {
            $extractNumber = "MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(nomor_kontrak, '-', 2), '-', -1) AS UNSIGNED))";

            if ($jenisDeposito === 'Finlog') {
                $maxNumber = \App\Models\PengajuanInvestasiFinlog::whereNotNull('nomor_kontrak')
                    ->where('nomor_kontrak', '!=', '')
                    ->lockForUpdate()
                    ->selectRaw("{$extractNumber} as max_num")
                    ->value('max_num') ?? 0;
            } else {
                $maxNumber = PengajuanInvestasi::whereNotNull('nomor_kontrak')
                    ->where('nomor_kontrak', '!=', '')
                    ->lockForUpdate()
                    ->selectRaw("{$extractNumber} as max_num")
                    ->value('max_num') ?? 0;
            }

            $runningNumber = ((int) $maxNumber) + 1;
            $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
            $formattedDate = $date->format('dmY');

            return strtoupper($kodePerusahaan) . '-' . $runningNumber . '-' . $formattedDate;
        });
    }
}
