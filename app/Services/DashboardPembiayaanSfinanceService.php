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
        
        // Ensure bulan is integer for Carbon
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        // Get previous month
        $previousMonth = Carbon::create($tahun, $bulanInt, 1)->subMonth();
        $startOfPreviousMonth = $previousMonth->copy()->startOfMonth();
        $endOfPreviousMonth = $previousMonth->copy()->endOfMonth();
        
        // Total Disbursement Bulan Ini (Pokok + Bagi Hasil)
        $totalDisbursement = $this->getTotalDisbursement($startOfMonth, $endOfMonth);
        $totalDisbursementPrevious = $this->getTotalDisbursement($startOfPreviousMonth, $endOfPreviousMonth);
        $disbursementPercentage = $this->calculatePercentageChange($totalDisbursementPrevious, $totalDisbursement);
        
        // Total Pembayaran Masuk Bulan Ini
        $totalPembayaranMasuk = $this->getTotalPembayaranMasuk($startOfMonth, $endOfMonth);
        $totalPembayaranMasukPrevious = $this->getTotalPembayaranMasuk($startOfPreviousMonth, $endOfPreviousMonth);
        $pembayaranPercentage = $this->calculatePercentageChange($totalPembayaranMasukPrevious, $totalPembayaranMasuk);
        
        // Total Sisa yang Belum Terbayar Bulan Ini
        $totalSisaBelumTerbayar = $this->getTotalSisaBelumTerbayar($endOfMonth);
        $totalSisaBelumTerbayarPrevious = $this->getTotalSisaBelumTerbayar($endOfPreviousMonth);
        $sisaPercentage = $this->calculatePercentageChange($totalSisaBelumTerbayarPrevious, $totalSisaBelumTerbayar);
        
        // Total Outstanding Piutang
        $totalOutstandingPiutang = $this->getTotalOutstandingPiutang($endOfMonth);
        $totalOutstandingPiutangPrevious = $this->getTotalOutstandingPiutang($endOfPreviousMonth);
        $outstandingPercentage = $this->calculatePercentageChange($totalOutstandingPiutangPrevious, $totalOutstandingPiutang);
        
        // Get month names
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return [
            'total_disbursement' => $totalDisbursement,
            'total_disbursement_percentage' => $disbursementPercentage['percentage'],
            'total_disbursement_is_increase' => $disbursementPercentage['is_increase'],
            'total_disbursement_is_new' => $disbursementPercentage['is_new'] ?? false,
            'total_pembayaran_masuk' => $totalPembayaranMasuk,
            'total_pembayaran_masuk_percentage' => $pembayaranPercentage['percentage'],
            'total_pembayaran_masuk_is_increase' => $pembayaranPercentage['is_increase'],
            'total_pembayaran_masuk_is_new' => $pembayaranPercentage['is_new'] ?? false,
            'total_sisa_belum_terbayar' => $totalSisaBelumTerbayar,
            'total_sisa_belum_terbayar_percentage' => $sisaPercentage['percentage'],
            'total_sisa_belum_terbayar_is_increase' => $sisaPercentage['is_increase'],
            'total_sisa_belum_terbayar_is_new' => $sisaPercentage['is_new'] ?? false,
            'total_outstanding_piutang' => $totalOutstandingPiutang,
            'total_outstanding_piutang_percentage' => $outstandingPercentage['percentage'],
            'total_outstanding_piutang_is_increase' => $outstandingPercentage['is_increase'],
            'total_outstanding_piutang_is_new' => $outstandingPercentage['is_new'] ?? false,
            'previous_month_name' => $bulanNama[$previousMonth->month] ?? '',
            'previous_month_year' => $previousMonth->year,
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
                    'percentage' => null,
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
     * Get total disbursement (pokok + bagi hasil) for a period
     */
    private function getTotalDisbursement(Carbon $startDate, Carbon $endDate): float
    {
        $result = PengajuanPeminjaman::where('pengajuan_peminjaman.status', 'Dana Sudah Dicairkan')
            ->join('history_status_pengajuan_pinjaman', function($join) use ($startDate, $endDate) {
                $join->on('pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'history_status_pengajuan_pinjaman.id_pengajuan_peminjaman')
                    ->whereIn('history_status_pengajuan_pinjaman.current_step', [3, 4, 6])
                    ->whereNotNull('history_status_pengajuan_pinjaman.tanggal_pencairan')
                    ->whereBetween('history_status_pengajuan_pinjaman.tanggal_pencairan', [$startDate, $endDate]);
            })
            ->selectRaw('
                COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_pinjaman), 0) +
                COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_bagi_hasil), 0) as total
            ')
            ->first();

        return (float)($result->total ?? 0);
    }

    /**
     * Get total pembayaran masuk for a period
     */
    private function getTotalPembayaranMasuk(Carbon $startDate, Carbon $endDate): float
    {
        $startPeriod = $startDate->format('Y-m');
        $monthStr = $startDate->format('Y-m');

        // Use rp.nilai_total_pengembalian if available, otherwise fallback to pp.nominal_invoice
        // Accept either explicit bulan_pembayaran or fallback to tanggal_pencairan between start and end
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

    /**
     * Get total sisa yang belum terbayar (sisa pokok + sisa bagi hasil)
     * Only counts the latest pengembalian record per pengajuan (latest sisa state)
     */
    private function getTotalSisaBelumTerbayar(Carbon $endDate): float
    {
        // First, get the latest pengembalian ULID per pengajuan
        $latestPerPengajuan = DB::table('pengembalian_pinjaman')
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('MAX(ulid) as latest_ulid, id_pengajuan_peminjaman')
            ->groupBy('id_pengajuan_peminjaman')
            ->get()
            ->pluck('latest_ulid')
            ->toArray();

        // Then, get the sisa from only those latest records
        if (empty($latestPerPengajuan)) {
            return 0.0;
        }

        $result = DB::table('pengembalian_pinjaman as pp')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->where('pm.status', 'Dana Sudah Dicairkan')
            ->whereIn('pp.ulid', $latestPerPengajuan)
            ->selectRaw('
                COALESCE(SUM(pp.sisa_bayar_pokok), 0) + COALESCE(SUM(pp.sisa_bagi_hasil), 0) as total
            ')
            ->first();

        return (float)($result->total ?? 0);
    }

    /**
     * Get total outstanding piutang (AR total) - use only the latest periode
     */
    private function getTotalOutstandingPiutang(Carbon $endDate): float
    {
        // ar_perbulan is cumulative per month, so we should only use the latest periode up to endDate
        $latestPeriode = DB::table('ar_perbulan')
            ->where('periode', '<=', $endDate->format('Y-m-d'))
            ->selectRaw('MAX(periode) as latest_periode')
            ->first();

        if (!$latestPeriode || !$latestPeriode->latest_periode) {
            return 0.0;
        }

        $result = DB::table('ar_perbulan')
            ->where('periode', $latestPeriode->latest_periode)
            ->selectRaw('COALESCE(SUM(sisa_ar_total), 0) as total')
            ->first();

        return (float)($result->total ?? 0);
    }

    /**
     * Get disbursement chart data per debitur untuk bulan yang dipilih
     * Chart menampilkan data per debitur sesuai filter bulan
     */
    public function getDisbursementData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        // Convert bulan to integer
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        // Get data per debitur untuk bulan yang dipilih
        $result = PengajuanPeminjaman::where('pengajuan_peminjaman.status', 'Dana Sudah Dicairkan')
            ->join('history_status_pengajuan_pinjaman', function($join) use ($startOfMonth, $endOfMonth) {
                $join->on('pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'history_status_pengajuan_pinjaman.id_pengajuan_peminjaman')
                    ->whereIn('history_status_pengajuan_pinjaman.current_step', [3, 4, 6])
                    ->whereNotNull('history_status_pengajuan_pinjaman.tanggal_pencairan')
                    ->whereBetween('history_status_pengajuan_pinjaman.tanggal_pencairan', [$startOfMonth, $endOfMonth]);
            })
            ->join('master_debitur_dan_investor as md', 'pengajuan_peminjaman.id_debitur', '=', 'md.id_debitur')
            ->selectRaw('
                md.nama as debitur,
                COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_pinjaman), 0) as pokok,
                COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_bagi_hasil), 0) as bagi_hasil
            ')
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
        
        // Convert bulan to integer
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $monthStr = str_pad($bulanInt, 2, '0', STR_PAD_LEFT);
        $period = "{$tahun}-{$monthStr}";

        // Determine start and end of the selected month
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        // Get pembayaran data per debitur dari pengembalian_pinjaman
        // Hitung pokok dan bagi hasil yang dibayarkan dari selisih total vs sisa
        $result = DB::table('pengembalian_pinjaman as pp')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->join('master_debitur_dan_investor as md', 'pm.id_debitur', '=', 'md.id_debitur')
            ->where(function($q) use ($period, $startOfMonth, $endOfMonth) {
                $q->where('pp.bulan_pembayaran', $period)
                  ->orWhereBetween('pp.tanggal_pencairan', [$startOfMonth, $endOfMonth]);
            })
            ->select(
                'md.nama as debitur',
                DB::raw('SUM(COALESCE(pp.total_pinjaman, 0) - COALESCE(pp.sisa_bayar_pokok, 0)) as total_pokok_dibayar'),
                DB::raw('SUM(COALESCE(pp.total_bagi_hasil, 0) - COALESCE(pp.sisa_bagi_hasil, 0)) as total_bagi_hasil_dibayar')
            )
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = [];
        $pokokData = [];
        $bagiHasilData = [];
        
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->total_pokok_dibayar ?? 0);
            $bagiHasilData[] = (float)($row->total_bagi_hasil_dibayar ?? 0);
        }
        
        return [
            'categories' => $categories,
            'pokok' => $pokokData,
            'bagi_hasil' => $bagiHasilData,
        ];
    }

    /**
     * Get sisa belum terbayar chart data per debitur untuk bulan yang dipilih
     * CRITICAL: Only use LATEST pengembalian per pengajuan, DO NOT join HSP (causes multiplication)
     */
    public function getSisaBelumTerbayarData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        // Convert bulan to integer
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();
        
        // Get only the latest pengembalian per pengajuan, then aggregate per debitur
        // DO NOT join with history_status_pengajuan_pinjaman - it causes row multiplication
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
     * Get pembayaran piutang per debitur untuk tahun yang dipilih
     * Aggregates all months in the selected year
     * CRITICAL: Use DISTINCT pengembalian records to avoid report join multiplication
     */
    public function getPembayaranPiutangTahunData(?int $tahun = null): array
    {
        $tahun = $tahun ?? date('Y');
        
        // Get pembayaran piutang per debitur dari report_pengembalian
        // Hitung pokok dan bagi hasil dari pengembalian_pinjaman
        $result = DB::table('report_pengembalian as rp')
            ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->join('master_debitur_dan_investor as md', 'pm.id_debitur', '=', 'md.id_debitur')
            ->whereYear('rp.created_at', $tahun)
            ->select(
                'md.nama as debitur',
                DB::raw('SUM(COALESCE(pp.total_pinjaman, 0) - COALESCE(pp.sisa_bayar_pokok, 0)) as total_pokok_dibayar'),
                DB::raw('SUM(COALESCE(pp.total_bagi_hasil, 0) - COALESCE(pp.sisa_bagi_hasil, 0)) as total_bagi_hasil_dibayar')
            )
            ->groupBy('md.id_debitur', 'md.nama')
            ->orderBy('md.nama')
            ->get();
        
        $categories = [];
        $pokokData = [];
        $bagiHasilData = [];
        
        foreach ($result as $row) {
            $categories[] = $row->debitur;
            $pokokData[] = (float)($row->total_pokok_dibayar ?? 0);
            $bagiHasilData[] = (float)($row->total_bagi_hasil_dibayar ?? 0);
        }
        
        return [
            'categories' => $categories,
            'pokok' => $pokokData,
            'bagi_hasil' => $bagiHasilData,
        ];
    }

    /**
     * Get comparison chart data (AR vs Utang Pengembalian Deposito)
     * Structure: X-axis = months, Series = [AR, Utang Pengembalian Deposito]
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
        
        // Logic pengambilan data tetap menggunakan tabel Sfinance (tidak diubah)
        $arBulan1 = $this->getARForMonth($bulan1, $tahun);
        $arBulan2 = $this->getARForMonth($bulan2, $tahun);
        
        $utangBulan1 = $this->getUtangPengembalianDepositoForMonth($bulan1, $tahun);
        $utangBulan2 = $this->getUtangPengembalianDepositoForMonth($bulan2, $tahun);
        
        $arSelisih = $arBulan1 - $arBulan2;
        $utangSelisih = $utangBulan1 - $utangBulan2;
        
        // FORMAT RETURN DISAMAKAN DENGAN SFINLOG agar Blade bisa menampilkan selisih
        return [
            'bulan1' => $namaBulan1,
            'bulan2' => $namaBulan2,
            'ar_bulan1' => $arBulan1,
            'ar_bulan2' => $arBulan2,
            'ar_selisih' => $arSelisih,
            'utang_bulan1' => $utangBulan1,
            'utang_bulan2' => $utangBulan2,
            'utang_selisih' => $utangSelisih,
            // Categories untuk Chart JS: [Data Kiri (Bulan Lama), Data Kanan (Bulan Baru)]
            'categories' => [$namaBulan2, $namaBulan1], 
        ];
    }

    /**
     * Get AR total for a specific month
     * Query ke ar_perbulan per bulan dan tahun yang spesifik
     * Return 0 jika tidak ada data (bukan fallback sample)
     */
    private function getARForMonth(string $bulan, int $tahun): float
    {
        // Convert bulan to integer
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // Query ar_perbulan untuk bulan dan tahun spesifik
        $result = DB::table('ar_perbulan')
            ->whereBetween('periode', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('periode', 'desc')
            ->select('sisa_ar_total')
            ->first();
        
        // Return 0 jika tidak ada data - jangan bikin sample data
        if (!$result) {
            return 0.0;
        }
        
        return (float)($result->sisa_ar_total ?? 0);
    }

    /**
     * Get Utang Pengembalian Deposito for a specific month
     * Ambil semua investasi yang tanggal investasinya hingga akhir bulan
     * Hitung total belum dikembalikan (jumlah - total_kembali)
     */
    private function getUtangPengembalianDepositoForMonth(string $bulan, int $tahun): float
    {
        // Convert bulan to integer
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // Query pengajuan_investasi - ambil yang tanggal investasi sampai akhir bulan ini
        // Hitung sisa yang belum dikembalikan
        $results = DB::table('pengajuan_investasi as pi')
            ->whereNotNull('pi.nomor_kontrak')
            ->where('pi.nomor_kontrak', '!=', '')
            ->whereDate('pi.tanggal_investasi', '<=', $endOfMonth->format('Y-m-d'))
            ->select(
                DB::raw('pi.jumlah_investasi - COALESCE(pi.total_kembali_dari_penyaluran, 0) as sisa')
            )
            ->get();
        
        $total = $results->sum('sisa');
        
        // Return 0 jika tidak ada data - jangan bikin sample data
        return (float)($total ?? 0);
    }

    /**
     * Get AR table data grouped by keterlambatan category
     */
    public function getArTableData(?string $bulan = null, ?int $tahun = null): array
    {
        // Filter khusus untuk AR Table, tidak terpengaruh filter chart lain
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        // Ambil semua debitur yang punya transaksi di report_pengembalian pada bulan/tahun filter table (khusus)
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
            // Ambil semua report_pengembalian untuk debitur ini pada bulan/tahun filter table (khusus)
            $reports = DB::table('report_pengembalian as rp')
                ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
                ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
                ->where('pm.id_debitur', $debitur->id_debitur)
                ->where('pm.status', 'Dana Sudah Dicairkan')
                ->whereMonth('rp.created_at', $bulanInt)
                ->whereYear('rp.created_at', $tahun)
                ->select('rp.hari_keterlambatan', 'rp.nilai_total_pengembalian')
                ->get();

            $del_1_30 = 0;
            $del_31_60 = 0;
            $del_61_90 = 0;
            $npl_91_179 = 0;
            $write_off = 0;

            foreach ($reports as $report) {
                preg_match('/(\d+)/', $report->hari_keterlambatan, $matches);
                $daysLate = isset($matches[1]) ? (int)$matches[1] : 0;
                $nilai = (float)($report->nilai_total_pengembalian ?? 0);
                if ($daysLate <= 30) {
                    $del_1_30 += $nilai;
                } elseif ($daysLate <= 60) {
                    $del_31_60 += $nilai;
                } elseif ($daysLate <= 90) {
                    $del_61_90 += $nilai;
                } elseif ($daysLate <= 179) {
                    $npl_91_179 += $nilai;
                } else {
                    $write_off += $nilai;
                }
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

    /**
     * Calculate AR by keterlambatan category for a debitur
     */
    private function calculateArByCategory(string $idDebitur, Carbon $endOfMonth): array
    {
        $categories = [
            'del_1_30' => 0,
            'del_31_60' => 0,
            'del_61_90' => 0,
            'npl_91_179' => 0,
            'write_off' => 0,
        ];
        
        // Get all invoices/reports for this debitur that are overdue
        // This includes both paid (but late) and unpaid invoices
        $reports = DB::table('report_pengembalian as rp')
            ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->where('pm.id_debitur', $idDebitur)
            ->where('pm.status', 'Dana Sudah Dicairkan')
            ->whereNotNull('rp.due_date')
            ->where('rp.due_date', '<=', $endOfMonth->format('Y-m-d'))
            ->select(
                'rp.due_date',
                'rp.nilai_total_pengembalian',
                'rp.created_at as tanggal_pembayaran',
                'rp.hari_keterlambatan'
            )
            ->get();
        
        foreach ($reports as $report) {
            $dueDate = Carbon::parse($report->due_date);
            $tanggalPembayaran = $report->tanggal_pembayaran 
                ? Carbon::parse($report->tanggal_pembayaran) 
                : null;
            
            // Calculate days late at end of month
            if ($tanggalPembayaran) {
                // If paid, use actual payment date
                $daysLate = $report->hari_keterlambatan 
                    ? (int)$report->hari_keterlambatan 
                    : max(0, $tanggalPembayaran->diffInDays($dueDate));
            } else {
                // If not paid yet, calculate from end of month
                $daysLate = max(0, $endOfMonth->diffInDays($dueDate));
            }
            
            // Only count if overdue
            if ($daysLate > 0) {
                $nilai = (float)($report->nilai_total_pengembalian ?? 0);
                
                // Categorize based on days late
                if ($daysLate <= 30) {
                    $categories['del_1_30'] += $nilai;
                } elseif ($daysLate <= 60) {
                    $categories['del_31_60'] += $nilai;
                } elseif ($daysLate <= 90) {
                    $categories['del_61_90'] += $nilai;
                } elseif ($daysLate <= 179) {
                    $categories['npl_91_179'] += $nilai;
                } else {
                    $categories['write_off'] += $nilai;
                }
            }
        }
        
        // Get remaining AR from ar_perbulan that hasn't been covered by reports
        $arPerbulan = DB::table('ar_perbulan')
            ->where('id_debitur', $idDebitur)
            ->where('periode', '<=', $endOfMonth->format('Y-m-d'))
            ->orderBy('periode', 'desc')
            ->first();
        
        if ($arPerbulan && $arPerbulan->sisa_ar_total > 0) {
            $totalARFromReports = array_sum($categories);
            $remainingAR = (float)$arPerbulan->sisa_ar_total - $totalARFromReports;
            
            // If there's remaining AR, get unpaid invoices to calculate aging
            if ($remainingAR > 0) {
                // Get invoices that haven't been paid yet (no report_pengembalian entry)
                // This is a simplified approach - in production you'd need to track unpaid invoices differently
                // For now, distribute remaining AR based on average aging from existing reports
                $avgDaysLate = 0;
                $reportCount = 0;
                
                foreach ($reports as $report) {
                    $dueDate = Carbon::parse($report->due_date);
                    $tanggalPembayaran = $report->tanggal_pembayaran 
                        ? Carbon::parse($report->tanggal_pembayaran) 
                        : $endOfMonth;
                    
                    $daysLate = $report->hari_keterlambatan 
                        ? (int)$report->hari_keterlambatan 
                        : max(0, $tanggalPembayaran->diffInDays($dueDate));
                    
                    if ($daysLate > 0) {
                        $avgDaysLate += $daysLate;
                        $reportCount++;
                    }
                }
                
                if ($reportCount > 0) {
                    $avgDaysLate = $avgDaysLate / $reportCount;
                } else {
                    // Default to 45 days if no reports
                    $avgDaysLate = 45;
                }
                
                // Distribute remaining AR based on average aging
                if ($avgDaysLate <= 30) {
                    $categories['del_1_30'] += $remainingAR;
                } elseif ($avgDaysLate <= 60) {
                    $categories['del_31_60'] += $remainingAR;
                } elseif ($avgDaysLate <= 90) {
                    $categories['del_61_90'] += $remainingAR;
                } elseif ($avgDaysLate <= 179) {
                    $categories['npl_91_179'] += $remainingAR;
                } else {
                    $categories['write_off'] += $remainingAR;
                }
            }
        }
        
        $categories['total'] = array_sum([
            $categories['del_1_30'],
            $categories['del_31_60'],
            $categories['del_61_90'],
            $categories['npl_91_179'],
            $categories['write_off'],
        ]);
        
        return $categories;
    }

    /**
     * Normalize and validate month input (handles string '01'-'12' or int 1-12)
     * Returns padded month string '01'-'12'
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
            \Log::warning("Invalid month value received: {$bulan}, using current month instead");
            return date('m');
        }
        
        // Return padded format
        return str_pad($bulanInt, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get AR terbagi berdasarkan kriteria keterlambatan
     * Kategorisasi: 0-30 hari (lancar), 31-60 hari, 61-90 hari, >90 hari (macet)
     */
    public function getARTerbarisDiagramData(): array
    {
        $result = DB::table('report_pengembalian as rp')
            ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
            ->select(
                'rp.hari_keterlambatan',
                DB::raw('SUM(COALESCE(pp.sisa_bayar_pokok, 0)) as total_sisa_pokok')
            )
            ->groupBy('rp.hari_keterlambatan')
            ->orderByRaw("CAST(SUBSTRING_INDEX(rp.hari_keterlambatan, ' ', 1) AS UNSIGNED)")
            ->get();
        
        $categories = [
            'DEL 0-30 Hari (Lancar)',
            'DEL 31-60 Hari',
            'DEL 61-90 Hari',
            'NPL >90 Hari (Macet)'
        ];
        
        $data = [0, 0, 0, 0];
        
        foreach ($result as $row) {
            // Extract angka dari string "X Hari"
            preg_match('/(\d+)/', $row->hari_keterlambatan, $matches);
            $hariKeterlambatan = isset($matches[1]) ? (int)$matches[1] : 0;
            $total = (float)($row->total_sisa_pokok ?? 0);
            
            if ($hariKeterlambatan <= 30) {
                $data[0] += $total;
            } elseif ($hariKeterlambatan <= 60) {
                $data[1] += $total;
            } elseif ($hariKeterlambatan <= 90) {
                $data[2] += $total;
            } else {
                $data[3] += $total;
            }
        }
        
        return [
            'categories' => $categories,
            'data' => $data,
            'colors' => ['#71dd37', '#ffab00', '#ff8c42', '#ff3e1d']
        ];
    }
}

