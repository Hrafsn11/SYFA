<?php

namespace App\Services;

use App\Models\PengajuanInvestasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardInvestasiDepositoService
{
    protected ?string $investorId = null;
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
        $this->investorId = $debiturInvestor ? $debiturInvestor->id_debitur : null;
    }

    public function isUserRestricted(): bool
    {
        return $this->isRestricted;
    }

    public function getInvestorId(): ?string
    {
        return $this->investorId;
    }

    private function applyRestriction($query, string $investorColumn = 'id_debitur_dan_investor')
    {
        if ($this->isRestricted && $this->investorId) {
            $query->where($investorColumn, $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            $query->whereRaw('1 = 0');
        }
        return $query;
    }

    public function getSummaryData(): array
    {
        $now = Carbon::now();
        $currentMonth = (int)$now->format('m');
        $currentYear = (int)$now->format('Y');

        $previousMonth = $currentMonth - 1;
        $previousYear = $currentYear;
        if ($previousMonth < 1) {
            $previousMonth = 12;
            $previousYear = $currentYear - 1;
        }

        $namaBulan = [
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

        // 1. Total Deposito Pokok
        $totalDepositoPokok = $this->getTotalDepositoPokok($currentYear, $currentMonth);
        $totalDepositoPokokPrev = $this->getTotalDepositoPokok($previousYear, $previousMonth);
        $depositoPokokStats = $this->calculateStats($totalDepositoPokokPrev, $totalDepositoPokok);

        // 2. Total CoF
        $totalCoF = $this->getTotalCoF($currentYear, $currentMonth);
        $totalCoFPrev = $this->getTotalCoF($previousYear, $previousMonth);
        $cofStats = $this->calculateStats($totalCoFPrev, $totalCoF);

        // 3. Total Pengembalian
        $totalPengembalian = $this->getTotalPengembalian($currentYear, $currentMonth);
        $totalPengembalianPrev = $this->getTotalPengembalian($previousYear, $previousMonth);
        $pengembalianStats = $this->calculateStats($totalPengembalianPrev, $totalPengembalian);

        // 4. Total Outstanding
        $totalOutstanding = $this->getTotalOutstanding($currentYear, $currentMonth);
        $totalOutstandingPrev = $this->getTotalOutstanding($previousYear, $previousMonth);
        $outstandingStats = $this->calculateStats($totalOutstandingPrev, $totalOutstanding);

        return [
            'total_deposito_pokok' => $totalDepositoPokok,
            'total_deposito_pokok_percentage' => $depositoPokokStats['percentage'],
            'total_deposito_pokok_is_increase' => $depositoPokokStats['is_increase'],
            'total_deposito_pokok_is_new' => $depositoPokokStats['is_new'],

            'total_cof' => $totalCoF,
            'total_cof_percentage' => $cofStats['percentage'],
            'total_cof_is_increase' => $cofStats['is_increase'],
            'total_cof_is_new' => $cofStats['is_new'],

            'total_pengembalian' => $totalPengembalian,
            'total_pengembalian_percentage' => $pengembalianStats['percentage'],
            'total_pengembalian_is_increase' => $pengembalianStats['is_increase'],
            'total_pengembalian_is_new' => $pengembalianStats['is_new'],

            'total_outstanding' => $totalOutstanding,
            'total_outstanding_percentage' => $outstandingStats['percentage'],
            'total_outstanding_is_increase' => $outstandingStats['is_increase'],
            'total_outstanding_is_new' => $outstandingStats['is_new'],

            'previous_month_name' => $namaBulan[$previousMonth] ?? '',
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

    private function getTotalDepositoPokok(int $year, int $month): float
    {
        $query = DB::table('pengajuan_investasi')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month);

        $this->applyRestriction($query);

        return (float)$query->sum('jumlah_investasi');
    }

    private function getTotalCoF(int $year, int $month): float
    {
        $query = DB::table('pengajuan_investasi')
            ->select('jumlah_investasi', 'bunga_pertahun')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month);

        $this->applyRestriction($query);

        $data = $query->get();
        $totalCof = 0;

        foreach ($data as $item) {
            $jumlahInvestasi = (float)$item->jumlah_investasi;
            $bungaPertahun = (float)$item->bunga_pertahun;
            $bungaPerBulan = $bungaPertahun / 12;
            $cofBulan = ($jumlahInvestasi * $bungaPerBulan) / 100;
            $totalCof += $cofBulan;
        }

        return $totalCof;
    }

    private function getTotalPengembalian(int $year, int $month): float
    {
        $query = DB::table('pengembalian_investasi as pi')
            ->join('pengajuan_investasi as pj', 'pi.id_pengajuan_investasi', '=', 'pj.id_pengajuan_investasi')
            ->whereYear('pi.tanggal_pengembalian', $year)
            ->whereMonth('pi.tanggal_pengembalian', $month);

        if ($this->isRestricted && $this->investorId) {
            $query->where('pj.id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return 0.0;
        }

        return (float)$query->selectRaw('COALESCE(SUM(pi.dana_pokok_dibayar + pi.bunga_dibayar), 0) as total')
            ->value('total');
    }

    private function getTotalOutstanding(int $year, int $month): float
    {
        $query = DB::table('pengajuan_investasi')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month);

        $this->applyRestriction($query);

        return (float)$query->selectRaw('COALESCE(SUM(sisa_pokok + sisa_bunga), 0) as total')
            ->value('total');
    }

    public function getChartDepositoPokok(?string $bulan = null): array
    {
        $currentYear = date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengajuan_investasi')
            ->select('nama_investor', DB::raw('SUM(jumlah_investasi) as total_pokok'))
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth)
            ->groupBy('nama_investor')
            ->orderBy('nama_investor');

        $this->applyRestriction($query);

        $data = $query->get();

        if ($data->isEmpty()) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []]]];
        }

        $categories = [];
        $pokokData = [];

        foreach ($data as $item) {
            $categories[] = $item->nama_investor;
            $pokokData[] = (float)$item->total_pokok;
        }

        return [
            'categories' => $categories,
            'series' => [['name' => 'Pokok', 'data' => $pokokData]]
        ];
    }

    public function getChartCoF(?string $bulan = null): array
    {
        $currentYear = date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengajuan_investasi')
            ->select('nama_investor', 'jumlah_investasi', 'bunga_pertahun')
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth);

        $this->applyRestriction($query);

        $data = $query->get();

        if ($data->isEmpty()) {
            return ['categories' => [], 'series' => [['name' => 'CoF', 'data' => []]]];
        }

        $cofPerInvestor = [];

        foreach ($data as $item) {
            $namaInvestor = $item->nama_investor;
            $jumlahInvestasi = (float)$item->jumlah_investasi;
            $bungaPertahun = (float)$item->bunga_pertahun;
            $bungaPerBulan = $bungaPertahun / 12;
            $cofBulan = ($jumlahInvestasi * $bungaPerBulan) / 100;

            if (!isset($cofPerInvestor[$namaInvestor])) {
                $cofPerInvestor[$namaInvestor] = 0;
            }
            $cofPerInvestor[$namaInvestor] += $cofBulan;
        }

        ksort($cofPerInvestor);

        $categories = array_keys($cofPerInvestor);
        $cofData = array_values($cofPerInvestor);

        return [
            'categories' => $categories,
            'series' => [['name' => 'CoF', 'data' => $cofData]]
        ];
    }

    public function getChartPengembalian(?string $bulan = null): array
    {
        $currentYear = date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengembalian_investasi as pi')
            ->join('pengajuan_investasi as pj', 'pi.id_pengajuan_investasi', '=', 'pj.id_pengajuan_investasi')
            ->select(
                'pj.nama_investor',
                DB::raw('SUM(pi.dana_pokok_dibayar) as total_pokok'),
                DB::raw('SUM(pi.bunga_dibayar) as total_bunga')
            )
            ->whereYear('pi.tanggal_pengembalian', $currentYear)
            ->whereMonth('pi.tanggal_pengembalian', $selectedMonth)
            ->groupBy('pj.nama_investor')
            ->orderBy('pj.nama_investor');

        if ($this->isRestricted && $this->investorId) {
            $query->where('pj.id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return [
                'categories' => [],
                'series' => [
                    ['name' => 'Pokok', 'data' => []],
                    ['name' => 'Bunga', 'data' => []]
                ]
            ];
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return [
                'categories' => [],
                'series' => [
                    ['name' => 'Pokok', 'data' => []],
                    ['name' => 'Bunga', 'data' => []]
                ]
            ];
        }

        $categories = [];
        $pokokData = [];
        $bungaData = [];

        foreach ($data as $item) {
            $categories[] = $item->nama_investor;
            $pokokData[] = (float)$item->total_pokok;
            $bungaData[] = (float)$item->total_bunga;
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Pokok', 'data' => $pokokData],
                ['name' => 'Bunga', 'data' => $bungaData]
            ]
        ];
    }

    public function getChartSisaDeposito(?string $bulan = null): array
    {
        $currentYear = date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengajuan_investasi')
            ->select(
                'nama_investor',
                DB::raw('SUM(sisa_pokok) as total_sisa_pokok'),
                DB::raw('SUM(sisa_bunga) as total_sisa_bunga')
            )
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth)
            ->groupBy('nama_investor')
            ->orderBy('nama_investor');

        $this->applyRestriction($query);

        $data = $query->get();

        if ($data->isEmpty()) {
            return [
                'categories' => [],
                'series' => [
                    ['name' => 'Pokok', 'data' => []],
                    ['name' => 'Bunga', 'data' => []]
                ]
            ];
        }

        $categories = [];
        $pokokData = [];
        $bungaData = [];

        foreach ($data as $item) {
            $categories[] = $item->nama_investor;
            $pokokData[] = (float)$item->total_sisa_pokok;
            $bungaData[] = (float)$item->total_sisa_bunga;
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Pokok', 'data' => $pokokData],
                ['name' => 'Bunga', 'data' => $bungaData]
            ]
        ];
    }

    public function getMonthOptions(): array
    {
        return [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    }
}
