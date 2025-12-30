<?php

namespace App\Services;

use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use App\Models\PenyaluranDeposito;
use App\Models\PengajuanInvestasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardPembiayaanSfinanceService
{
    /**
     * Get summary data for dashboard cards
     */
    public function getSummaryData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        $previousMonth = Carbon::create($tahun, $bulanInt, 1)->subMonth();
        $startOfPreviousMonth = $previousMonth->copy()->startOfMonth();
        $endOfPreviousMonth = $previousMonth->copy()->endOfMonth();
        
        // 1. Total Disbursement
        $totalDisbursement = $this->getTotalDisbursement($startOfMonth, $endOfMonth);
        $totalDisbursementPrevious = $this->getTotalDisbursement($startOfPreviousMonth, $endOfPreviousMonth);
        $disbursementStats = $this->calculateStats($totalDisbursementPrevious, $totalDisbursement);
        
        // 2. Total Pembayaran Masuk
        $totalPembayaranMasuk = $this->getTotalPembayaranMasuk($startOfMonth, $endOfMonth);
        $totalPembayaranMasukPrevious = $this->getTotalPembayaranMasuk($startOfPreviousMonth, $endOfPreviousMonth);
        $pembayaranStats = $this->calculateStats($totalPembayaranMasukPrevious, $totalPembayaranMasuk);
        
        // 3. Total Sisa Belum Terbayar
        $totalSisaBelumTerbayar = $this->getTotalSisaBelumTerbayar($endOfMonth);
        $totalSisaBelumTerbayarPrevious = $this->getTotalSisaBelumTerbayar($endOfPreviousMonth);
        $sisaStats = $this->calculateStats($totalSisaBelumTerbayarPrevious, $totalSisaBelumTerbayar);
        
        // 4. Total Outstanding Piutang
        $totalOutstandingPiutang = $this->getTotalOutstandingPiutang($endOfMonth);
        $totalOutstandingPiutangPrevious = $this->getTotalOutstandingPiutang($endOfPreviousMonth);
        $outstandingStats = $this->calculateStats($totalOutstandingPiutangPrevious, $totalOutstandingPiutang);
        
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return [
            // Disbursement
            'total_disbursement' => $totalDisbursement,
            'total_disbursement_percentage' => $disbursementStats['percentage'],
            'total_disbursement_is_increase' => $disbursementStats['is_increase'],
            'total_disbursement_is_new' => $disbursementStats['is_new'],
            
            // Pembayaran
            'total_pembayaran_masuk' => $totalPembayaranMasuk,
            'total_pembayaran_masuk_percentage' => $pembayaranStats['percentage'],
            'total_pembayaran_masuk_is_increase' => $pembayaranStats['is_increase'],
            'total_pembayaran_masuk_is_new' => $pembayaranStats['is_new'],
            
            // Sisa
            'total_sisa_belum_terbayar' => $totalSisaBelumTerbayar,
            'total_sisa_belum_terbayar_percentage' => $sisaStats['percentage'],
            'total_sisa_belum_terbayar_is_increase' => $sisaStats['is_increase'],
            'total_sisa_belum_terbayar_is_new' => $sisaStats['is_new'],
            
            // Outstanding
            'total_outstanding_piutang' => $totalOutstandingPiutang,
            'total_outstanding_piutang_percentage' => $outstandingStats['percentage'],
            'total_outstanding_piutang_is_increase' => $outstandingStats['is_increase'],
            'total_outstanding_piutang_is_new' => $outstandingStats['is_new'],
            
            'previous_month_name' => $bulanNama[$previousMonth->month] ?? '',
        ];
    }

    private function calculateStats(float $previous, float $current): array
    {
        if ($previous == 0) {
            if ($current > 0) {
                return ['percentage' => 100, 'is_increase' => true, 'is_new' => true];
            }
            return ['percentage' => 0, 'is_increase' => false, 'is_new' => false];
        }
        
        $percentage = (($current - $previous) / $previous) * 100;
        return [
            'percentage' => abs($percentage),
            'is_increase' => $percentage >= 0,
            'is_new' => false
        ];
    }

    private function getTotalDisbursement(Carbon $startDate, Carbon $endDate): float
    {
        $result = PengajuanPeminjaman::where('pengajuan_peminjaman.status', 'Dana Sudah Dicairkan')
            ->join('history_status_pengajuan_pinjaman', function($join) use ($startDate, $endDate) {
                $join->on('pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'history_status_pengajuan_pinjaman.id_pengajuan_peminjaman')
                    ->whereIn('history_status_pengajuan_pinjaman.current_step', [3, 4, 6])
                    ->whereNotNull('history_status_pengajuan_pinjaman.tanggal_pencairan')
                    ->whereBetween('history_status_pengajuan_pinjaman.tanggal_pencairan', [$startDate, $endDate]);
            })
            ->selectRaw('COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_pinjaman), 0) + COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_bagi_hasil), 0) as total')
            ->first();
        return (float)($result->total ?? 0);
    }

    private function getTotalPembayaranMasuk(Carbon $startDate, Carbon $endDate): float
    {
        $monthStr = $startDate->format('Y-m');
        $result = DB::table('pengembalian_pinjaman as pp')
            ->leftJoin('report_pengembalian as rp', 'pp.ulid', '=', 'rp.id_pengembalian')
            ->where(function($q) use ($monthStr, $startDate, $endDate) {
                $q->where('pp.bulan_pembayaran', $monthStr)
                  ->orWhereBetween('pp.tanggal_pencairan', [$startDate, $endDate]);
            })
            ->selectRaw('COALESCE(SUM(COALESCE(rp.nilai_total_pengembalian, pp.nominal_invoice)), 0) as total')
            ->first();
        return (float)($result->total ?? 0);
    }

    private function getTotalSisaBelumTerbayar(Carbon $endDate): float
    {
        $latestPerPengajuan = DB::table('pengembalian_pinjaman')
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('MAX(ulid) as latest_ulid, id_pengajuan_peminjaman')
            ->groupBy('id_pengajuan_peminjaman')
            ->pluck('latest_ulid')
            ->toArray();

        if (empty($latestPerPengajuan)) return 0.0;

        $result = DB::table('pengembalian_pinjaman as pp')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->where('pm.status', 'Dana Sudah Dicairkan')
            ->whereIn('pp.ulid', $latestPerPengajuan)
            ->selectRaw('COALESCE(SUM(pp.sisa_bayar_pokok), 0) + COALESCE(SUM(pp.sisa_bagi_hasil), 0) as total')
            ->first();
        return (float)($result->total ?? 0);
    }

    private function getTotalOutstandingPiutang(Carbon $endDate): float
    {
        $latestPeriode = DB::table('ar_perbulan')
            ->where('periode', '<=', $endDate->format('Y-m-d'))
            ->selectRaw('MAX(periode) as latest_periode')
            ->first();

        if (!$latestPeriode || !$latestPeriode->latest_periode) return 0.0;

        $result = DB::table('ar_perbulan')
            ->where('periode', $latestPeriode->latest_periode)
            ->selectRaw('COALESCE(SUM(sisa_ar_total), 0) as total')
            ->first();
        return (float)($result->total ?? 0);
    }

    public function getDisbursementData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        $result = PengajuanPeminjaman::where('pengajuan_peminjaman.status', 'Dana Sudah Dicairkan')
            ->join('history_status_pengajuan_pinjaman', function($join) use ($startOfMonth, $endOfMonth) {
                $join->on('pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'history_status_pengajuan_pinjaman.id_pengajuan_peminjaman')
                    ->whereIn('history_status_pengajuan_pinjaman.current_step', [3, 4, 6])
                    ->whereNotNull('history_status_pengajuan_pinjaman.tanggal_pencairan')
                    ->whereBetween('history_status_pengajuan_pinjaman.tanggal_pencairan', [$startOfMonth, $endOfMonth]);
            })
            ->join('master_debitur_dan_investor as md', 'pengajuan_peminjaman.id_debitur', '=', 'md.id_debitur')
            ->selectRaw('md.nama as debitur, COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_pinjaman), 0) as pokok, COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_bagi_hasil), 0) as bagi_hasil')
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = []; $pokokData = []; $bagiHasilData = [];
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->pokok ?? 0);
            $bagiHasilData[] = (float)($row->bagi_hasil ?? 0);
        }
        return ['categories' => $categories, 'pokok' => $pokokData, 'bagi_hasil' => $bagiHasilData];
    }

    public function getPembayaranData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $monthStr = str_pad($bulanInt, 2, '0', STR_PAD_LEFT);
        $period = "{$tahun}-{$monthStr}";
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        $result = DB::table('pengembalian_pinjaman as pp')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->join('master_debitur_dan_investor as md', 'pm.id_debitur', '=', 'md.id_debitur')
            ->where(function($q) use ($period, $startOfMonth, $endOfMonth) {
                $q->where('pp.bulan_pembayaran', $period)
                  ->orWhereBetween('pp.tanggal_pencairan', [$startOfMonth, $endOfMonth]);
            })
            ->select('md.nama as debitur', DB::raw('SUM(COALESCE(pp.total_pinjaman, 0) - COALESCE(pp.sisa_bayar_pokok, 0)) as total_pokok_dibayar'), DB::raw('SUM(COALESCE(pp.total_bagi_hasil, 0) - COALESCE(pp.sisa_bagi_hasil, 0)) as total_bagi_hasil_dibayar'))
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = []; $pokokData = []; $bagiHasilData = [];
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->total_pokok_dibayar ?? 0);
            $bagiHasilData[] = (float)($row->total_bagi_hasil_dibayar ?? 0);
        }
        return ['categories' => $categories, 'pokok' => $pokokData, 'bagi_hasil' => $bagiHasilData];
    }

    public function getSisaBelumTerbayarData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        $result = DB::table('pengembalian_pinjaman as pp')
            ->select('md.id_debitur', 'md.nama as debitur')
            ->selectRaw('COALESCE(SUM(pp.sisa_bayar_pokok), 0) as pokok')
            ->selectRaw('COALESCE(SUM(pp.sisa_bagi_hasil), 0) as bagi_hasil')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->join('master_debitur_dan_investor as md', 'pm.id_debitur', '=', 'md.id_debitur')
            ->where('pm.status', 'Dana Sudah Dicairkan')
            ->whereDate('pp.created_at', '<=', $endOfMonth)
            ->whereRaw('pp.ulid IN (SELECT MAX(pp2.ulid) FROM pengembalian_pinjaman pp2 WHERE pp2.id_pengajuan_peminjaman = pp.id_pengajuan_peminjaman AND DATE(pp2.created_at) <= ?)', [$endOfMonth->toDateString()])
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = []; $pokokData = []; $bagiHasilData = [];
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->pokok ?? 0);
            $bagiHasilData[] = (float)($row->bagi_hasil ?? 0);
        }
        return ['categories' => $categories, 'pokok' => $pokokData, 'bagi_hasil' => $bagiHasilData];
    }

    public function getPembayaranPiutangTahunData(?int $tahun = null): array
    {
        $tahun = $tahun ?? date('Y');
        $result = DB::table('report_pengembalian as rp')
            ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->join('master_debitur_dan_investor as md', 'pm.id_debitur', '=', 'md.id_debitur')
            ->whereYear('rp.created_at', $tahun)
            ->select('md.nama as debitur', DB::raw('SUM(COALESCE(pp.total_pinjaman, 0) - COALESCE(pp.sisa_bayar_pokok, 0)) as total_pokok_dibayar'), DB::raw('SUM(COALESCE(pp.total_bagi_hasil, 0) - COALESCE(pp.sisa_bagi_hasil, 0)) as total_bagi_hasil_dibayar'))
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = []; $pokokData = []; $bagiHasilData = [];
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->total_pokok_dibayar ?? 0);
            $bagiHasilData[] = (float)($row->total_bagi_hasil_dibayar ?? 0);
        }
        return ['categories' => $categories, 'pokok' => $pokokData, 'bagi_hasil' => $bagiHasilData];
    }

    /**
     * Get comparison chart data (AR vs Utang Pengembalian Deposito)
     * UPDATE: Menambahkan ar_selisih dan utang_selisih untuk dashboard
     */
    public function getComparisonData(?string $bulan1 = null, ?string $bulan2 = null, ?int $tahun = null): array
    {
        $bulan1 = $bulan1 ?? date('m');
        $bulan2 = $bulan2 ?? date('m', strtotime('-1 month'));
        $tahun = $tahun ?? date('Y');
        
        $bulan1Int = is_numeric($bulan1) ? (int)$bulan1 : (int)date('m');
        $bulan2Int = is_numeric($bulan2) ? (int)$bulan2 : (int)date('m', strtotime('-1 month'));
        
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $namaBulan1 = $bulanNama[$bulan1Int] ?? 'Bulan 1';
        $namaBulan2 = $bulanNama[$bulan2Int] ?? 'Bulan 2';
        
        $arBulan1 = $this->getARForMonth($bulan1, $tahun);
        $arBulan2 = $this->getARForMonth($bulan2, $tahun);
        
        $utangBulan1 = $this->getUtangPengembalianDepositoForMonth($bulan1, $tahun);
        $utangBulan2 = $this->getUtangPengembalianDepositoForMonth($bulan2, $tahun);
        
        // Return dengan format yang dibutuhkan Blade Sfinlog
        return [
            'bulan1' => $namaBulan1,
            'bulan2' => $namaBulan2,
            'ar_bulan1' => $arBulan1,
            'ar_bulan2' => $arBulan2,
            'ar_selisih' => $arBulan1 - $arBulan2, // Logic selisih ditambahkan
            'utang_bulan1' => $utangBulan1,
            'utang_bulan2' => $utangBulan2,
            'utang_selisih' => $utangBulan1 - $utangBulan2, // Logic selisih ditambahkan
            'categories' => [$namaBulan2, $namaBulan1]
        ];
    }

    private function getARForMonth(string $bulan, int $tahun): float
    {
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        $result = DB::table('ar_perbulan')
            ->whereBetween('periode', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('periode', 'desc')
            ->select('sisa_ar_total')
            ->first();
        
        return (float)($result->sisa_ar_total ?? 0);
    }

    private function getUtangPengembalianDepositoForMonth(string $bulan, int $tahun): float
    {
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        $results = DB::table('pengajuan_investasi as pi')
            ->whereNotNull('pi.nomor_kontrak')
            ->where('pi.nomor_kontrak', '!=', '')
            ->whereDate('pi.tanggal_investasi', '<=', $endOfMonth->format('Y-m-d'))
            ->select(DB::raw('pi.jumlah_investasi - COALESCE(pi.total_kembali_dari_penyaluran, 0) as sisa'))
            ->get();
        
        return (float)($results->sum('sisa') ?? 0);
    }

    public function getArTableData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        
        $debiturs = DB::table('report_pengembalian as rp')
            ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->join('master_debitur_dan_investor as md', 'pm.id_debitur', '=', 'md.id_debitur')
            ->where('pm.status', 'Dana Sudah Dicairkan')
            ->whereMonth('rp.created_at', $bulanInt)
            ->whereYear('rp.created_at', $tahun)
            ->select('md.id_debitur', 'md.nama as nama_debitur')
            ->distinct()
            ->get();

        $result = [];
        foreach ($debiturs as $debitur) {
            $reports = DB::table('report_pengembalian as rp')
                ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
                ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
                ->where('pm.id_debitur', $debitur->id_debitur)
                ->where('pm.status', 'Dana Sudah Dicairkan')
                ->whereMonth('rp.created_at', $bulanInt)
                ->whereYear('rp.created_at', $tahun)
                ->select('rp.hari_keterlambatan', 'rp.nilai_total_pengembalian')
                ->get();

            $del_1_30 = 0; $del_31_60 = 0; $del_61_90 = 0; $npl_91_179 = 0; $write_off = 0;

            foreach ($reports as $report) {
                preg_match('/(\d+)/', $report->hari_keterlambatan, $matches);
                $daysLate = isset($matches[1]) ? (int)$matches[1] : 0;
                $nilai = (float)($report->nilai_total_pengembalian ?? 0);
                if ($daysLate <= 30) $del_1_30 += $nilai;
                elseif ($daysLate <= 60) $del_31_60 += $nilai;
                elseif ($daysLate <= 90) $del_61_90 += $nilai;
                elseif ($daysLate <= 179) $npl_91_179 += $nilai;
                else $write_off += $nilai;
            }

            $result[] = [
                'debitur' => $debitur->nama_debitur,
                'del_1_30' => $del_1_30,
                'del_31_60' => $del_31_60,
                'del_61_90' => $del_61_90,
                'npl_91_179' => $npl_91_179,
                'write_off' => $write_off,
            ];
        }
        return $result;
    }
}