<?php

namespace App\Services;

use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use App\Models\ArPerbulanFinlog;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\PengembalianInvestasiFinlog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembiayaanSFinlogService
{
    protected ?string $debiturId = null;
    protected bool $isRestricted = false;

    public function __construct()
    {
        $this->initializeRestriction();
    }

    private function initializeRestriction(): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->isRestricted = true;
            return;
        }

        if ($user->hasRole('super-admin')) {
            $this->isRestricted = false;
            return;
        }

        $hasUnrestrictedRole = $user->roles()->where('restriction', 1)->exists();

        if ($hasUnrestrictedRole) {
            $this->isRestricted = false;
            return;
        }

        $this->isRestricted = true;
        $debiturInvestor = $user->debitur;
        $this->debiturId = $debiturInvestor ? $debiturInvestor->id_debitur : null;
    }

    public function isUserRestricted(): bool
    {
        return $this->isRestricted;
    }

    public function getDebiturId(): ?string
    {
        return $this->debiturId;
    }

    public function getSummaryData(): array
    {
        $now = Carbon::now();
        $currentMonth = (int)$now->format('m');
        $currentYear = (int)$now->format('Y');

        $startOfMonth = Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::create($currentYear, $currentMonth, 1)->endOfMonth();

        $previousMonth = Carbon::create($currentYear, $currentMonth, 1)->subMonth();
        $startOfPreviousMonth = $previousMonth->copy()->startOfMonth();
        $endOfPreviousMonth = $previousMonth->copy()->endOfMonth();

        $totalDisbursement = $this->getTotalDisbursement($startOfMonth, $endOfMonth);
        $totalDisbursementPrevious = $this->getTotalDisbursement($startOfPreviousMonth, $endOfPreviousMonth);
        $disbursementStats = $this->calculateStats($totalDisbursementPrevious, $totalDisbursement);

        $totalPembayaranMasuk = $this->getTotalPembayaranMasuk($startOfMonth, $endOfMonth);
        $totalPembayaranMasukPrevious = $this->getTotalPembayaranMasuk($startOfPreviousMonth, $endOfPreviousMonth);
        $pembayaranStats = $this->calculateStats($totalPembayaranMasukPrevious, $totalPembayaranMasuk);

        $totalSisaBelumTerbayar = $this->getTotalSisaBelumTerbayar($endOfMonth);
        $totalSisaBelumTerbayarPrevious = $this->getTotalSisaBelumTerbayar($endOfPreviousMonth);
        $sisaStats = $this->calculateStats($totalSisaBelumTerbayarPrevious, $totalSisaBelumTerbayar);

        $totalOutstandingPiutang = $this->getTotalOutstandingPiutang($endOfMonth);
        $totalOutstandingPiutangPrevious = $this->getTotalOutstandingPiutang($endOfPreviousMonth);
        $outstandingStats = $this->calculateStats($totalOutstandingPiutangPrevious, $totalOutstandingPiutang);

        $bulanNama = [
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

        return [
            'total_disbursement' => $totalDisbursement,
            'total_disbursement_percentage' => $disbursementStats['percentage'],
            'total_disbursement_is_increase' => $disbursementStats['is_increase'],
            'total_disbursement_is_new' => $disbursementStats['is_new'],

            'total_pembayaran_masuk' => $totalPembayaranMasuk,
            'total_pembayaran_masuk_percentage' => $pembayaranStats['percentage'],
            'total_pembayaran_masuk_is_increase' => $pembayaranStats['is_increase'],
            'total_pembayaran_masuk_is_new' => $pembayaranStats['is_new'],

            'total_sisa_belum_terbayar' => $totalSisaBelumTerbayar,
            'total_sisa_belum_terbayar_percentage' => $sisaStats['percentage'],
            'total_sisa_belum_terbayar_is_increase' => $sisaStats['is_increase'],
            'total_sisa_belum_terbayar_is_new' => $sisaStats['is_new'],

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
        $query = PeminjamanFinlog::where('status', 'Selesai')
            ->whereDate('harapan_tanggal_pencairan', '>=', $startDate->toDateString())
            ->whereDate('harapan_tanggal_pencairan', '<=', $endDate->toDateString());

        if ($this->isRestricted && $this->debiturId) {
            $query->where('id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return 0.0;
        }

        $result = $query->selectRaw('COALESCE(SUM(total_pinjaman), 0) as total')->first();
        return (float)($result->total ?? 0);
    }

    private function getTotalPembayaranMasuk(Carbon $startDate, Carbon $endDate): float
    {
        $query = DB::table('pengembalian_pinjaman_finlog as pp')
            ->join('peminjaman_finlog as pf', 'pp.id_pinjaman_finlog', '=', 'pf.id_peminjaman_finlog')
            ->whereDate('pp.tanggal_pengembalian', '>=', $startDate->toDateString())
            ->whereDate('pp.tanggal_pengembalian', '<=', $endDate->toDateString());

        if ($this->isRestricted && $this->debiturId) {
            $query->where('pf.id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return 0.0;
        }

        $result = $query->selectRaw('COALESCE(SUM(pp.jumlah_pengembalian), 0) as total')->first();
        return (float)($result->total ?? 0);
    }

    private function getTotalSisaBelumTerbayar(Carbon $endDate): float
    {
        $query = DB::table('master_debitur_dan_investor')
            ->whereIn('id_debitur', function ($q) use ($endDate) {
                $q->select('id_debitur')
                    ->from('peminjaman_finlog as pf')
                    ->whereDate('harapan_tanggal_pencairan', '<=', $endDate->toDateString());
            });

        if ($this->isRestricted && $this->debiturId) {
            $query->where('id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return 0.0;
        }

        $debiturs = $query->select('id_debitur')->get();

        $totalOverdue = 0.0;
        foreach ($debiturs as $debitur) {
            $arByCategory = $this->calculateArByCategory($debitur->id_debitur, $endDate);
            $totalOverdue += ($arByCategory['del_1_30'] ?? 0)
                + ($arByCategory['del_31_60'] ?? 0)
                + ($arByCategory['del_61_90'] ?? 0)
                + ($arByCategory['npl_91_179'] ?? 0)
                + ($arByCategory['write_off'] ?? 0);
        }
        return $totalOverdue;
    }

    private function getTotalOutstandingPiutang(Carbon $endDate): float
    {
        $latestPeriode = ArPerbulanFinlog::where('periode', '<=', $endDate->toDateString())
            ->selectRaw('MAX(periode) as latest_periode')
            ->first();

        if (!$latestPeriode || !$latestPeriode->latest_periode) {
            return 0.0;
        }

        $query = ArPerbulanFinlog::where('periode', $latestPeriode->latest_periode);

        if ($this->isRestricted && $this->debiturId) {
            $query->where('id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return 0.0;
        }

        $result = $query->selectRaw('COALESCE(SUM(sisa_ar_total), 0) as total')->first();
        return (float)($result->total ?? 0);
    }

    public function getDisbursementData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');

        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        $query = DB::table('peminjaman_finlog as pf')
            ->join('master_debitur_dan_investor as md', 'pf.id_debitur', '=', 'md.id_debitur')
            ->where('pf.status', 'Selesai')
            ->whereDate('pf.harapan_tanggal_pencairan', '>=', $startOfMonth->toDateString())
            ->whereDate('pf.harapan_tanggal_pencairan', '<=', $endOfMonth->toDateString());

        if ($this->isRestricted && $this->debiturId) {
            $query->where('pf.id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return ['categories' => [], 'pokok' => [], 'bagi_hasil' => []];
        }

        $result = $query->selectRaw('md.nama as debitur, COALESCE(SUM(pf.nilai_pinjaman), 0) as pokok, COALESCE(SUM(pf.nilai_bagi_hasil), 0) as bagi_hasil')
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

        return ['categories' => $categories, 'pokok' => $pokokData, 'bagi_hasil' => $bagiHasilData];
    }

    public function getPembayaranData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');

        $startOfMonth = Carbon::create($tahun, $bulanInt, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        $query = DB::table('pengembalian_pinjaman_finlog as pp')
            ->join('peminjaman_finlog as pf', 'pp.id_pinjaman_finlog', '=', 'pf.id_peminjaman_finlog')
            ->join('master_debitur_dan_investor as md', 'pf.id_debitur', '=', 'md.id_debitur')
            ->whereDate('pp.tanggal_pengembalian', '>=', $startOfMonth->toDateString())
            ->whereDate('pp.tanggal_pengembalian', '<=', $endOfMonth->toDateString());

        if ($this->isRestricted && $this->debiturId) {
            $query->where('pf.id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return ['categories' => [], 'pokok' => [], 'bagi_hasil' => []];
        }

        $result = $query->selectRaw('md.nama as debitur, COALESCE(SUM(pp.sisa_pinjaman), 0) as pokok, COALESCE(SUM(pp.sisa_bagi_hasil), 0) as bagi_hasil')
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

        return ['categories' => $categories, 'pokok' => $pokokData, 'bagi_hasil' => $bagiHasilData];
    }

    public function getSisaBelumTerbayarData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        $query = DB::table('pengembalian_pinjaman_finlog as pp')
            ->select('md.id_debitur', 'md.nama as debitur')
            ->selectRaw('COALESCE(SUM(pp.sisa_pinjaman), 0) as pokok, COALESCE(SUM(pp.sisa_bagi_hasil), 0) as bagi_hasil')
            ->join('peminjaman_finlog as pf', 'pp.id_pinjaman_finlog', '=', 'pf.id_peminjaman_finlog')
            ->join('master_debitur_dan_investor as md', 'pf.id_debitur', '=', 'md.id_debitur')
            ->where('pf.status', 'Selesai')
            ->whereDate('pp.created_at', '<=', $endOfMonth)
            ->whereRaw('pp.id_pengembalian_pinjaman_finlog IN (SELECT MAX(pp2.id_pengembalian_pinjaman_finlog) FROM pengembalian_pinjaman_finlog pp2 WHERE pp2.id_pinjaman_finlog = pp.id_pinjaman_finlog AND DATE(pp2.created_at) <= ?)', [$endOfMonth->toDateString()]);

        if ($this->isRestricted && $this->debiturId) {
            $query->where('pf.id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return ['categories' => [], 'pokok' => [], 'bagi_hasil' => []];
        }

        $result = $query->groupBy('md.id_debitur', 'md.nama')
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

        return ['categories' => $categories, 'pokok' => $pokokData, 'bagi_hasil' => $bagiHasilData];
    }

    public function getPembayaranPiutangTahunData(?int $tahun = null): array
    {
        $tahun = $tahun ?? date('Y');

        $query = DB::table('pengembalian_pinjaman_finlog as pp')
            ->join('peminjaman_finlog as pf', 'pp.id_pinjaman_finlog', '=', 'pf.id_peminjaman_finlog')
            ->join('master_debitur_dan_investor as md', 'pf.id_debitur', '=', 'md.id_debitur')
            ->whereYear('pp.tanggal_pengembalian', $tahun);

        if ($this->isRestricted && $this->debiturId) {
            $query->where('pf.id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return ['categories' => [], 'pokok' => [], 'bagi_hasil' => []];
        }

        $result = $query->selectRaw('md.nama as debitur, COALESCE(SUM(pp.jumlah_pengembalian), 0) as pokok, COALESCE(SUM(pp.sisa_bagi_hasil), 0) as bagi_hasil')
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

        return ['categories' => $categories, 'pokok' => $pokokData, 'bagi_hasil' => $bagiHasilData];
    }

    public function getComparisonData(?string $bulan1 = null, ?string $bulan2 = null, ?int $tahun = null): array
    {
        $bulan1 = $bulan1 ?? date('m');
        $bulan2 = $bulan2 ?? date('m', strtotime('-1 month'));
        $tahun = $tahun ?? date('Y');

        $bulan1Int = is_numeric($bulan1) ? (int)$bulan1 : (int)date('m');
        $bulan2Int = is_numeric($bulan2) ? (int)$bulan2 : (int)date('m', strtotime('-1 month'));

        $bulanNama = [
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

        $namaBulan1 = $bulanNama[$bulan1Int] ?? 'Bulan 1';
        $namaBulan2 = $bulanNama[$bulan2Int] ?? 'Bulan 2';

        $arBulan1 = $this->getARForMonth($bulan1, $tahun);
        $arBulan2 = $this->getARForMonth($bulan2, $tahun);

        $utangBulan1 = $this->getUtangPengembalianForMonth($bulan1, $tahun);
        $utangBulan2 = $this->getUtangPengembalianForMonth($bulan2, $tahun);

        return [
            'bulan1' => $namaBulan1,
            'bulan2' => $namaBulan2,
            'ar_bulan1' => $arBulan1,
            'ar_bulan2' => $arBulan2,
            'ar_selisih' => $arBulan1 - $arBulan2,
            'utang_bulan1' => $utangBulan1,
            'utang_bulan2' => $utangBulan2,
            'utang_selisih' => $utangBulan1 - $utangBulan2,
            'categories' => [$namaBulan2, $namaBulan1],
        ];
    }

    private function getARForMonth(string $bulan, int $tahun): float
    {
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');

        $query = ArPerbulanFinlog::whereYear('periode', $tahun)
            ->whereMonth('periode', $bulanInt);

        if ($this->isRestricted && $this->debiturId) {
            $query->where('id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return 0.0;
        }

        $result = $query->orderBy('periode', 'desc')->first();
        return (float)($result->sisa_ar_total ?? 0);
    }

    private function getUtangPengembalianForMonth(string $bulan, int $tahun): float
    {
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        if ($this->isRestricted) {
            return 0.0;
        }

        $investasiResult = PengajuanInvestasiFinlog::whereDate('tanggal_investasi', '<=', $endOfMonth->toDateString())
            ->selectRaw('COALESCE(SUM(nominal_investasi + nominal_bagi_hasil_yang_didapat), 0) as total_investasi')
            ->first();

        $totalInvestasi = (float)($investasiResult->total_investasi ?? 0);

        $pengembalianResult = PengembalianInvestasiFinlog::whereDate('tanggal_pengembalian', '<=', $endOfMonth->toDateString())
            ->selectRaw('COALESCE(SUM(total_dibayar), 0) as total_dibayar')
            ->first();

        $totalDibayar = (float)($pengembalianResult->total_dibayar ?? 0);

        return max(0, $totalInvestasi - $totalDibayar);
    }

    public function getArTableData(?string $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        $bulanInt = is_numeric($bulan) ? (int)$bulan : (int)date('m');
        $endOfMonth = Carbon::create($tahun, $bulanInt, 1)->endOfMonth();

        $debitursQuery = DB::table('master_debitur_dan_investor')
            ->whereIn('id_debitur', function ($q) use ($endOfMonth) {
                $q->select('id_debitur')
                    ->from('peminjaman_finlog as pf')
                    ->whereDate('harapan_tanggal_pencairan', '<=', $endOfMonth->toDateString());
            });

        if ($this->isRestricted && $this->debiturId) {
            $debitursQuery->where('id_debitur', $this->debiturId);
        } elseif ($this->isRestricted && !$this->debiturId) {
            return [];
        }

        $debiturs = $debitursQuery->select('id_debitur', 'nama')
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
                $categories['del_1_30'] += $sisa;
            }
        }

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
}
