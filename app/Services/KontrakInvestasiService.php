<?php

namespace App\Services;

use App\Models\PengajuanInvestasi;
use App\Helpers\Terbilang;
use Carbon\Carbon;

class KontrakInvestasiService
{
    private const HARI_INDONESIA = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    private const BULAN_INDONESIA = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    /**
     * Generate kontrak data for preview
     *
     * @param PengajuanInvestasi $pengajuan
     * @param string|null $nomorKontrak
     * @return array
     */
    public function generateKontrakData(PengajuanInvestasi $pengajuan, ?string $nomorKontrak = null): array
    {
        $tanggalInvestasi = Carbon::parse($pengajuan->tanggal_investasi);
        $tanggalJatuhTempo = $this->calculateTanggalJatuhTempo($pengajuan, $tanggalInvestasi);
        $bagiHasil = $this->calculateBagiHasil($pengajuan);

        return [
            'id_investasi' => $pengajuan->id_pengajuan_investasi,
            'jenis_deposito' => strtoupper($pengajuan->jenis_investasi),
            'nomor_kontrak' => $nomorKontrak ?? $this->generateDefaultNomorKontrak(),
            'hari' => $this->getHariIndonesia($tanggalInvestasi),
            'tanggal_kontrak' => $this->formatTanggalIndonesia($tanggalInvestasi),
            'nama_investor' => $pengajuan->nama_pic_kontrak ?? $pengajuan->nama_investor ?? 'N/A',
            'perusahaan_investor' => $pengajuan->nama_investor ?? 'N/A',
            'alamat_investor' => $pengajuan->investor->alamat ?? 'N/A',
            'jumlah_investasi_angka' => 'Rp. ' . number_format((float) ($pengajuan->jumlah_investasi ?? 0), 0, ',', '.'),
            'jumlah_investasi_text' => Terbilang::rupiah((float) ($pengajuan->jumlah_investasi ?? 0)),
            'lama_investasi' => $pengajuan->lama_investasi . ' bulan',
            'tanggal_mulai' => $this->formatTanggalIndonesia($tanggalInvestasi),
            'tanggal_berakhir' => $this->formatTanggalIndonesia($tanggalJatuhTempo),
            'tanggal_jatuh_tempo' => $this->formatTanggalIndonesia($tanggalJatuhTempo),
            'bagi_hasil' => $bagiHasil,
            'bagi_hasil_persen' => $bagiHasil . '%',
            'tanggal_full' => $this->getHariIndonesia($tanggalInvestasi) . ', ' . $this->formatTanggalIndonesia($tanggalInvestasi),
            'tanda_tangan_investor' => $pengajuan->investor->tanda_tangan ?? null,
        ];
    }

    /**
     * Calculate tanggal jatuh tempo based on deposito type
     *
     * @param PengajuanInvestasi $pengajuan
     * @param Carbon $tanggalInvestasi
     * @return Carbon
     */
    private function calculateTanggalJatuhTempo(PengajuanInvestasi $pengajuan, Carbon $tanggalInvestasi): Carbon
    {
        if ($pengajuan->jenis_investasi === 'Reguler') {
            // Regular: Always 31 December of investment year
            return Carbon::createFromDate($tanggalInvestasi->year, 12, 31);
        }

        // Khusus: tanggal_investasi + lama_investasi months
        return $tanggalInvestasi->copy()->addMonths($pengajuan->lama_investasi);
    }

    /**
     * Calculate bagi hasil percentage
     *
     * @param PengajuanInvestasi $pengajuan
     * @return int
     */
    private function calculateBagiHasil(PengajuanInvestasi $pengajuan): int
    {
        return $pengajuan->jenis_investasi === 'Reguler' ? 10 : $pengajuan->bunga_pertahun;
    }

    /**
     * Get Indonesian day name
     *
     * @param Carbon $date
     * @return string
     */
    private function getHariIndonesia(Carbon $date): string
    {
        return self::HARI_INDONESIA[$date->format('l')] ?? $date->format('l');
    }

    /**
     * Format date to Indonesian format (d Month Y)
     *
     * @param Carbon $date
     * @return string
     */
    private function formatTanggalIndonesia(Carbon $date): string
    {
        return $date->day . ' ' . self::BULAN_INDONESIA[$date->month] . ' ' . $date->year;
    }

    /**
     * Generate default nomor kontrak
     *
     * @return string
     */
    private function generateDefaultNomorKontrak(): string
    {
        return 'XXX/SKI/INV/' . date('Y');
    }
}
