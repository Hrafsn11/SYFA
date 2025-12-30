<?php

namespace App\Services;

use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use App\Models\ArPerbulanFinlog;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\PengembalianInvestasiFinlog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PembiayaanSFinlogService
{
    /**
     * Get summary data for Disbursement Card ONLY
     * Filter hanya apply ke disbursement data, BUKAN ke pembayaran/sisa
     */
    public function getSummaryDisbursement(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        $previousMonth = Carbon::create($tahun, $bulanInt, 1)->subMonth();
        $startOfPreviousMonth = $previousMonth->copy()->startOfMonth();
        $endOfPreviousMonth = $previousMonth->copy()->endOfMonth();
        
        $totalDisbursement = $this->getTotalDisbursement($startOfMonth, $endOfMonth);
        $totalDisbursementPrevious = $this->getTotalDisbursement($startOfPreviousMonth, $endOfPreviousMonth);
        $disbursementPercentage = $this->calculatePercentageChange($totalDisbursementPrevious, $totalDisbursement);
        
        $bulanNama = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 
                      7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        
        return [
            'total_disbursement' => $totalDisbursement,
            'total_disbursement_percentage' => $disbursementPercentage['percentage'],
            'total_disbursement_is_increase' => $disbursementPercentage['is_increase'],
            'total_disbursement_is_new' => $disbursementPercentage['is_new'] ?? false,
            'previous_month_name' => $bulanNama[$previousMonth->month] ?? '',
            'previous_month_year' => $previousMonth->year,
        ];
    }

    /**
     * Get summary data for Pembayaran Card ONLY
     * Filter hanya apply ke pembayaran data, BUKAN ke disbursement/sisa
     */
    public function getSummaryPembayaran(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        $previousMonth = Carbon::create($tahun, $bulanInt, 1)->subMonth();
        $startOfPreviousMonth = $previousMonth->copy()->startOfMonth();
        $endOfPreviousMonth = $previousMonth->copy()->endOfMonth();
        
        $totalPembayaranMasuk = $this->getTotalPembayaranMasuk($startOfMonth, $endOfMonth);
        $totalPembayaranMasukPrevious = $this->getTotalPembayaranMasuk($startOfPreviousMonth, $endOfPreviousMonth);
        $pembayaranPercentage = $this->calculatePercentageChange($totalPembayaranMasukPrevious, $totalPembayaranMasuk);
        
        $bulanNama = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 
                      7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        
        return [
            'total_pembayaran_masuk' => $totalPembayaranMasuk,
            'total_pembayaran_masuk_percentage' => $pembayaranPercentage['percentage'],
            'total_pembayaran_masuk_is_increase' => $pembayaranPercentage['is_increase'],
            'total_pembayaran_masuk_is_new' => $pembayaranPercentage['is_new'] ?? false,
            'previous_month_name' => $bulanNama[$previousMonth->month] ?? '',
            'previous_month_year' => $previousMonth->year,
        ];
    }

    /**
     * Get summary data for Sisa Card ONLY
     * Filter hanya apply ke sisa data, BUKAN ke disbursement/pembayaran
     */
    public function getSummarySisa(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        $previousMonth = Carbon::create($tahun, $bulanInt, 1)->subMonth();
        $endOfPreviousMonth = $previousMonth->copy()->endOfMonth();
        
        $totalSisaBelumTerbayar = $this->getTotalSisaBelumTerbayar($endOfMonth);
        $totalSisaBelumTerbayarPrevious = $this->getTotalSisaBelumTerbayar($endOfPreviousMonth);
        $sisaPercentage = $this->calculatePercentageChange($totalSisaBelumTerbayarPrevious, $totalSisaBelumTerbayar);
        
        $bulanNama = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 
                      7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        
        return [
            'total_sisa_belum_terbayar' => $totalSisaBelumTerbayar,
            'total_sisa_belum_terbayar_percentage' => $sisaPercentage['percentage'],
            'total_sisa_belum_terbayar_is_increase' => $sisaPercentage['is_increase'],
            'total_sisa_belum_terbayar_is_new' => $sisaPercentage['is_new'] ?? false,
            'previous_month_name' => $bulanNama[$previousMonth->month] ?? '',
            'previous_month_year' => $previousMonth->year,
        ];
    }

    /**
     * Get summary data for Outstanding Piutang Card ONLY
     * GLOBAL - tidak di-filter per bulan, hanya tahun
     */
    public function getSummaryOutstanding(?int $tahun = null): array
    {
        $tahun = $tahun ?? date('Y');
        
        $endOfYear = Carbon::create($tahun, 12, 31)->endOfYear();
        $endOfPreviousYear = Carbon::create($tahun - 1, 12, 31)->endOfYear();
        
        $totalOutstandingPiutang = $this->getTotalOutstandingPiutang($endOfYear);
        $totalOutstandingPiutangPrevious = $this->getTotalOutstandingPiutang($endOfPreviousYear);
        $outstandingPercentage = $this->calculatePercentageChange($totalOutstandingPiutangPrevious, $totalOutstandingPiutang);
        
        return [
            'total_outstanding_piutang' => $totalOutstandingPiutang,
            'total_outstanding_piutang_percentage' => $outstandingPercentage['percentage'],
            'total_outstanding_piutang_is_increase' => $outstandingPercentage['is_increase'],
            'total_outstanding_piutang_is_new' => $outstandingPercentage['is_new'] ?? false,
        ];
    }

    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange(float $previousValue, float $currentValue): array
    {
        if ($previousValue == 0) {
            // When previous value is zero and current > 0, mark as "new" so UI can show a clearer label
            if ($currentValue > 0) {
                return [
                    'percentage' => 100,
                    'is_increase' => true,
                    'is_new' => true,
                ];
            }

            return [
                'percentage' => 0,
                'is_increase' => false,
                'is_new' => false,
            ];
        }

        $percentage = (($currentValue - $previousValue) / $previousValue) * 100;

        return [
            'percentage' => abs($percentage),
            'is_increase' => $percentage >= 0,
            'is_new' => false,
        ];
    }

    /**
     * Get total disbursement for a period
     */
    private function getTotalDisbursement(Carbon $startDate, Carbon $endDate): float
    {
        $result = PeminjamanFinlog::where('status', 'Selesai')
            ->whereDate('harapan_tanggal_pencairan', '>=', $startDate->toDateString())
            ->whereDate('harapan_tanggal_pencairan', '<=', $endDate->toDateString())
            ->selectRaw('COALESCE(SUM(total_pinjaman), 0) as total')
            ->first();

        return (float)($result->total ?? 0);
    }

    /**
     * Get total pembayaran masuk for a period (Pokok + Bagi Hasil)
     */
    private function getTotalPembayaranMasuk(Carbon $startDate, Carbon $endDate): float
    {
        // jumlah_pengembalian = pokok + bagi hasil yang dikembalikan
        $result = PengembalianPinjamanFinlog::whereDate('tanggal_pengembalian', '>=', $startDate->toDateString())
            ->whereDate('tanggal_pengembalian', '<=', $endDate->toDateString())
            ->selectRaw('COALESCE(SUM(jumlah_pengembalian), 0) as total')
            ->first();

        return (float)($result->total ?? 0);
    }

    /**
     * Get total sisa yang belum terbayar (Pokok + Bagi Hasil Sisa)
     * Only counts the latest pengembalian record per pinjaman (latest sisa state)
     */
    private function getTotalSisaBelumTerbayar(Carbon $endDate): float
    {
        // Ambil semua debitur yang punya transaksi sampai endDate
        $debiturs = DB::table('master_debitur_dan_investor')
            ->whereIn('id_debitur', function($q) use ($endDate) {
                $q->select('id_debitur')
                    ->from('peminjaman_finlog as pf')
                    ->whereDate('harapan_tanggal_pencairan', '<=', $endDate->toDateString());
            })
            ->select('id_debitur')
            ->get();

        $totalOverdue = 0.0;
        foreach ($debiturs as $debitur) {
            $arByCategory = $this->calculateArByCategory($debitur->id_debitur, $endDate);
            // Sum only overdue categories: DEL, NPL, Write Off
            $totalOverdue += ($arByCategory['del_1_30'] ?? 0)
                + ($arByCategory['del_31_60'] ?? 0)
                + ($arByCategory['del_61_90'] ?? 0)
                + ($arByCategory['npl_91_179'] ?? 0)
                + ($arByCategory['write_off'] ?? 0);
        }
        return $totalOverdue;
    }

    /**
     * Get total outstanding piutang (AR total)
     */
    private function getTotalOutstandingPiutang(Carbon $endDate): float
    {
        // ar_perbulan_finlog is cumulative per month, use latest periode up to endDate
        $latestPeriode = ArPerbulanFinlog::where('periode', '<=', $endDate->toDateString())
            ->selectRaw('MAX(periode) as latest_periode')
            ->first();

        if (!$latestPeriode || !$latestPeriode->latest_periode) {
            return 0.0;
        }

        $result = ArPerbulanFinlog::where('periode', $latestPeriode->latest_periode)
            ->selectRaw('COALESCE(SUM(sisa_ar_total), 0) as total')
            ->first();

        return (float)($result->total ?? 0);
    }

    /**
     * Get disbursement chart data per debitur untuk bulan yang dipilih
     */
    public function getDisbursementData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        // Normalize bulan
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        if ($bulanInt < 1 || $bulanInt > 12) $bulanInt = (int)date('m');
        
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        // Get data per debitur untuk bulan yang dipilih - terpisah POKOK vs BAGI HASIL
        $result = DB::table('peminjaman_finlog as pf')
            ->join('master_debitur_dan_investor as md', 'pf.id_debitur', '=', 'md.id_debitur')
            ->where('pf.status', 'Selesai')
            ->whereDate('pf.harapan_tanggal_pencairan', '>=', $startOfMonth->toDateString())
            ->whereDate('pf.harapan_tanggal_pencairan', '<=', $endOfMonth->toDateString())
            ->selectRaw('md.nama as debitur, COALESCE(SUM(pf.nilai_pinjaman), 0) as pokok, COALESCE(SUM(pf.nilai_bagi_hasil), 0) as bagi_hasil')
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = [];
        $pokokData = [];
        $bagiHasilData = [];
        
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->pokok ?? 0);
            $bagiHasilData[] = (float)($row->bagi_hasil ?? 0);
        }
        
        return [
            'categories' => $categories,
            'pokok' => $pokokData,
            'bagi_hasil' => $bagiHasilData,
        ];
    }

    /**
     * Get pembayaran chart data per debitur untuk bulan yang dipilih
     */
    public function getPembayaranData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        // Normalize bulan
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        if ($bulanInt < 1 || $bulanInt > 12) $bulanInt = (int)date('m');

        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        // Get pembayaran data per debitur dari pengembalian_pinjaman_finlog - POKOK + BAGI HASIL
        $result = DB::table('pengembalian_pinjaman_finlog as pp')
            ->join('peminjaman_finlog as pf', 'pp.id_pinjaman_finlog', '=', 'pf.id_peminjaman_finlog')
            ->join('master_debitur_dan_investor as md', 'pf.id_debitur', '=', 'md.id_debitur')
            ->whereDate('pp.tanggal_pengembalian', '>=', $startOfMonth->toDateString())
            ->whereDate('pp.tanggal_pengembalian', '<=', $endOfMonth->toDateString())
            ->selectRaw('md.nama as debitur, COALESCE(SUM(pp.sisa_pinjaman), 0) as pokok, COALESCE(SUM(pp.sisa_bagi_hasil), 0) as bagi_hasil')
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = [];
        $pokokData = [];
        $bagiHasilData = [];
        
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->pokok ?? 0);
            $bagiHasilData[] = (float)($row->bagi_hasil ?? 0);
        }
        
        return [
            'categories' => $categories,
            'pokok' => $pokokData,
            'bagi_hasil' => $bagiHasilData,
        ];
    }

    /**
     * Get sisa belum terbayar chart data per debitur untuk bulan yang dipilih
     */
    public function getSisaBelumTerbayarData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        // Normalize bulan
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        if ($bulanInt < 1 || $bulanInt > 12) $bulanInt = (int)date('m');
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        // Get only the latest pengembalian per pinjaman up to endOfMonth - POKOK + BAGI HASIL SISA
        $result = DB::table('pengembalian_pinjaman_finlog as pp')
            ->select('md.id_debitur', 'md.nama as debitur')
            ->selectRaw('COALESCE(SUM(pp.sisa_pinjaman), 0) as pokok, COALESCE(SUM(pp.sisa_bagi_hasil), 0) as bagi_hasil')
            ->join('peminjaman_finlog as pf', 'pp.id_pinjaman_finlog', '=', 'pf.id_peminjaman_finlog')
            ->join('master_debitur_dan_investor as md', 'pf.id_debitur', '=', 'md.id_debitur')
            ->where('pf.status', 'Selesai')
            ->whereDate('pp.created_at', '<=', $endOfMonth)
            ->whereRaw('pp.id_pengembalian_pinjaman_finlog IN (SELECT MAX(pp2.id_pengembalian_pinjaman_finlog) FROM pengembalian_pinjaman_finlog pp2 WHERE pp2.id_pinjaman_finlog = pp.id_pinjaman_finlog AND DATE(pp2.created_at) <= ?)', [$endOfMonth->toDateString()])
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = [];
        $pokokData = [];
        $bagiHasilData = [];
        
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->pokok ?? 0);
            $bagiHasilData[] = (float)($row->bagi_hasil ?? 0);
        }
        
        return [
            'categories' => $categories,
            'pokok' => $pokokData,
            'bagi_hasil' => $bagiHasilData,
        ];
    }

    /**
     * Get pembayaran piutang per tahun yang dipilih
     */
    public function getPembayaranPiutangTahunData(?int $tahun = null): array
    {
        $tahun = $tahun ?? date('Y');
        
        // Get pembayaran piutang per debitur dari pengembalian_pinjaman_finlog
        $result = DB::table('pengembalian_pinjaman_finlog as pp')
            ->join('peminjaman_finlog as pf', 'pp.id_pinjaman_finlog', '=', 'pf.id_peminjaman_finlog')
            ->join('master_debitur_dan_investor as md', 'pf.id_debitur', '=', 'md.id_debitur')
            ->whereYear('pp.tanggal_pengembalian', $tahun)
            ->selectRaw('md.nama as debitur, COALESCE(SUM(pp.jumlah_pengembalian), 0) as pokok, COALESCE(SUM(pp.sisa_bagi_hasil), 0) as bagi_hasil')
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = [];
        $pokokData = [];
        $bagiHasilData = [];
        
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->pokok ?? 0);
            $bagiHasilData[] = (float)($row->bagi_hasil ?? 0);
        }
        
        return [
            'categories' => $categories,
            'pokok' => $pokokData,
            'bagi_hasil' => $bagiHasilData,
        ];
    }

    /**
     * Get comparison chart data (AR vs Utang Pengembalian Deposito)
     */
    public function getComparisonData(?string $bulan1 = null, ?string $bulan2 = null, ?int $tahun = null): array
    {
        $bulan1 = $bulan1 ?? date('m');
        $bulan2 = $bulan2 ?? date('m', strtotime('-1 month'));
        $tahun = $tahun ?? date('Y');
        
        // Convert bulan to integer
        $bulan1Int = is_numeric($bulan1) ? (int)$bulan1 : (int)date('m');
        $bulan2Int = is_numeric($bulan2) ? (int)$bulan2 : (int)date('m', strtotime('-1 month'));
        
        // Get month names
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $namaBulan1 = $bulanNama[$bulan1Int] ?? 'Bulan 1';
        $namaBulan2 = $bulanNama[$bulan2Int] ?? 'Bulan 2';
        
        // Get AR for bulan1 and bulan2
        $arBulan1 = $this->getARForMonth($bulan1, $tahun);
        $arBulan2 = $this->getARForMonth($bulan2, $tahun);
        
        // Get Utang Pengembalian untuk bulan1 dan bulan2
        $utangBulan1 = $this->getUtangPengembalianForMonth($bulan1, $tahun);
        $utangBulan2 = $this->getUtangPengembalianForMonth($bulan2, $tahun);
        
        // Calculate selisih (differences)
        $arSelisih = $arBulan1 - $arBulan2;
        $utangSelisih = $utangBulan1 - $utangBulan2;
        
        return [
            'bulan1' => $namaBulan1,
            'bulan2' => $namaBulan2,
            'ar_bulan1' => $arBulan1,
            'ar_bulan2' => $arBulan2,
            'ar_selisih' => $arSelisih,
            'utang_bulan1' => $utangBulan1,
            'utang_bulan2' => $utangBulan2,
            'utang_selisih' => $utangSelisih,
            'categories' => [$namaBulan2, $namaBulan1],
        ];
    }

    /**
     * Get AR total for a specific month dari ArPerbulanFinlog
     */
    private function getARForMonth(string $bulan, int $tahun): float
    {
        // Convert bulan to integer
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        
        // Query ar_perbulan_finlog untuk periode terakhir dalam bulan (sisa_ar_total)
        // Ambil record terakhir dari bulan tersebut, bukan SUM (untuk menghindari GROUP BY error)
        $result = ArPerbulanFinlog::whereYear('periode', $tahun)
            ->whereMonth('periode', $bulanInt)
            ->orderBy('periode', 'desc')
            ->first();
        
        return (float)($result->sisa_ar_total ?? 0);
    }

    /**
     * Get Utang Pengembalian Deposito for a specific month dari PengajuanInvestasiFinlog
     * Hitung total nominal investasi + bagi hasil yang belum dikembalikan
     */
    private function getUtangPengembalianForMonth(string $bulan, int $tahun): float
    {
        // Convert bulan to integer
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        // Get total investasi yang telah dibuat sampai bulan ini
        $investasiResult = PengajuanInvestasiFinlog::whereDate('tanggal_investasi', '<=', $endOfMonth->toDateString())
            ->selectRaw('COALESCE(SUM(nominal_investasi + nominal_bagi_hasil_yang_didapat), 0) as total_investasi')
            ->first();
        
        $totalInvestasi = (float)($investasiResult->total_investasi ?? 0);
        
        // Get total yang sudah dikembalikan sampai bulan ini
        $pengembalianResult = PengembalianInvestasiFinlog::whereDate('tanggal_pengembalian', '<=', $endOfMonth->toDateString())
            ->selectRaw('COALESCE(SUM(total_dibayar), 0) as total_dibayar')
            ->first();
        
        $totalDibayar = (float)($pengembalianResult->total_dibayar ?? 0);
        
        // Sisa = Total investasi - Total dibayar
        $sisa = $totalInvestasi - $totalDibayar;
        
        return max(0, $sisa); // Return 0 jika negative
    }

    /**
     * Get AR table data grouped by status
     */
    public function getArTableData(?string $bulan = null, ?int $tahun = null): array
    {
        // Filter khusus untuk AR Table
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        // Ambil semua debitur yang punya transaksi pada bulan/tahun filter table
        $debiturs = DB::table('master_debitur_dan_investor')
            ->whereIn('id_debitur', function($q) use ($endOfMonth) {
                $q->select('id_debitur')
                    ->from('peminjaman_finlog as pf')
                    ->whereDate('harapan_tanggal_pencairan', '<=', $endOfMonth->toDateString());
            })
            ->select('id_debitur', 'nama')
            ->orderBy('nama')
            ->get();

        $result = [];

        foreach ($debiturs as $debitur) {
            $arByCategory = $this->calculateArByCategory($debitur->id_debitur, $endOfMonth);
            $result[] = array_merge([
                'debitur' => $debitur->nama,
            ], $arByCategory);
        }

        return $result;
    }

    /**
     * Calculate AR by status category for a debitur
     */
    private function calculateArByCategory(string $idDebitur, Carbon $endOfMonth): array
    {
        $categories = [
            'lancar' => 0.0,
            'del_1_30' => 0.0,
            'del_31_60' => 0.0,
            'del_61_90' => 0.0,
            'npl_91_179' => 0.0,
            'write_off' => 0.0,
            'total' => 0.0,
        ];
        
        // Get all pengembalian records for this debitur up to endOfMonth
        $pengembalian = DB::table('pengembalian_pinjaman_finlog as pp')
            ->join('peminjaman_finlog as pf', 'pp.id_pinjaman_finlog', '=', 'pf.id_peminjaman_finlog')
            ->where('pf.id_debitur', $idDebitur)
            ->whereDate('pp.created_at', '<=', $endOfMonth->toDateString())
            ->selectRaw('pp.sisa_pinjaman, pp.status')
            ->get();
        
        foreach ($pengembalian as $record) {
            $sisa = (float)($record->sisa_pinjaman ?? 0);
            
            if ($record->status === 'Lunas') {
                $categories['lancar'] += $sisa;
            } else {
                // Simple categorization based on status
                $categories['del_1_30'] += $sisa;
            }
        }
        
        // Get remaining AR from ar_perbulan_finlog if any
        $arPerbulan = ArPerbulanFinlog::where('id_debitur', $idDebitur)
            ->whereDate('periode', '<=', $endOfMonth->toDateString())
            ->selectRaw('SUM(sisa_ar_total) as total_ar')
            ->first();
        
        if ($arPerbulan && $arPerbulan->total_ar > 0) {
            $categories['del_1_30'] += (float)($arPerbulan->total_ar ?? 0);
        }
        
        $categories['total'] = array_sum([
            $categories['lancar'],
            $categories['del_1_30'],
            $categories['del_31_60'],
            $categories['del_61_90'],
            $categories['npl_91_179'],
            $categories['write_off'],
        ]);
        
        return $categories;
    }

    /**
     * Normalize and validate month input
     */
    public function normalizeMonth(?string $bulan = null): string
    {
        if (!$bulan) {
            return date('m');
        }

        // Convert to integer first to handle both string and int
        $bulanInt = (int)$bulan;
        
        // Validate range
        if ($bulanInt < 1 || $bulanInt > 12) {
            return date('m');
        }
        
        // Return padded format
        return str_pad($bulanInt, 2, '0', STR_PAD_LEFT);
    }
}
