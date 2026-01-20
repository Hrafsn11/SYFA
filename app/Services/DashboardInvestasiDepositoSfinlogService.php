<?php

namespace App\Services;

use App\Models\PengajuanInvestasiFinlog;
use App\Models\PengembalianInvestasiFinlog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardInvestasiDepositoSfinlogService
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

    public function getSummaryData(): array
    {
        $now = Carbon::now();
        $currentMonth = (int)$now->format('m');
        $currentYear = (int)$now->format('Y');

        $previousMonth = Carbon::create($currentYear, $currentMonth, 1)->subMonth();
        $prevMonth = (int)$previousMonth->format('m');
        $prevYear = (int)$previousMonth->format('Y');

        $totalDepositoPokok = $this->getTotalDepositoPokok($currentYear, $currentMonth);
        $totalDepositoPokokPrev = $this->getTotalDepositoPokok($prevYear, $prevMonth);
        $depositoStats = $this->calculateStats($totalDepositoPokokPrev, $totalDepositoPokok);

        $totalCof = $this->getTotalCoF($currentYear, $currentMonth);
        $totalCofPrev = $this->getTotalCoF($prevYear, $prevMonth);
        $cofStats = $this->calculateStats($totalCofPrev, $totalCof);

        $totalPengembalian = $this->getTotalPengembalian($currentYear, $currentMonth);
        $totalPengembalianPrev = $this->getTotalPengembalian($prevYear, $prevMonth);
        $pengembalianStats = $this->calculateStats($totalPengembalianPrev, $totalPengembalian);

        $totalOutstanding = $this->getTotalOutstandingDeposito($currentYear, $currentMonth);
        $totalOutstandingPrev = $this->getTotalOutstandingDeposito($prevYear, $prevMonth);
        $outstandingStats = $this->calculateStats($totalOutstandingPrev, $totalOutstanding);

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
            'total_deposito_pokok' => $totalDepositoPokok,
            'total_deposito_pokok_percentage' => $depositoStats['percentage'],
            'total_deposito_pokok_is_increase' => $depositoStats['is_increase'],
            'total_deposito_pokok_is_new' => $depositoStats['is_new'],

            'total_cof' => $totalCof,
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

            'previous_month_name' => $bulanNama[$prevMonth] ?? '',
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
        $query = DB::table('pengajuan_investasi_finlog')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month);

        if ($this->isRestricted && $this->investorId) {
            $query->where('id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return 0.0;
        }

        return (float)$query->sum('nominal_investasi');
    }

    private function getTotalCoF(int $year, int $month): float
    {
        $query = DB::table('pengajuan_investasi_finlog')
            ->select('nominal_investasi', 'persentase_bagi_hasil')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month);

        if ($this->isRestricted && $this->investorId) {
            $query->where('id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return 0.0;
        }

        $dataInvestasi = $query->get();

        $totalCof = 0;
        foreach ($dataInvestasi as $item) {
            $bagiHasilNominalPa = ($item->persentase_bagi_hasil * $item->nominal_investasi) / 100;
            $cofBulan = $bagiHasilNominalPa / 12;
            $totalCof += $cofBulan;
        }

        return $totalCof;
    }

    private function getTotalPengembalian(int $year, int $month): float
    {
        $query = DB::table('pengembalian_investasi_finlog as pi')
            ->join('pengajuan_investasi_finlog as pif', 'pi.id_pengajuan_investasi_finlog', '=', 'pif.id_pengajuan_investasi_finlog')
            ->whereYear('pi.tanggal_pengembalian', $year)
            ->whereMonth('pi.tanggal_pengembalian', $month);

        if ($this->isRestricted && $this->investorId) {
            $query->where('pif.id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return 0.0;
        }

        $result = $query->selectRaw('COALESCE(SUM(pi.dana_pokok_dibayar + pi.bagi_hasil_dibayar), 0) as total')
            ->first();

        return (float)($result->total ?? 0);
    }

    private function getTotalOutstandingDeposito(int $year, int $month): float
    {
        $investasiQuery = DB::table('pengajuan_investasi_finlog')
            ->select('id_pengajuan_investasi_finlog', 'nominal_investasi', 'persentase_bagi_hasil', 'lama_investasi')
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month);

        if ($this->isRestricted && $this->investorId) {
            $investasiQuery->where('id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return 0.0;
        }

        $investasi = $investasiQuery->get();

        if ($investasi->isEmpty()) {
            return 0.0;
        }

        $totalPengembalian = DB::table('pengembalian_investasi_finlog')
            ->select('id_pengajuan_investasi_finlog', DB::raw('SUM(dana_pokok_dibayar) as total_pokok'), DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil'))
            ->groupBy('id_pengajuan_investasi_finlog')
            ->get()
            ->keyBy('id_pengajuan_investasi_finlog');

        $totalOutstanding = 0;
        foreach ($investasi as $inv) {
            $total = $totalPengembalian->get($inv->id_pengajuan_investasi_finlog);

            $bagiHasilNominalPa = ($inv->persentase_bagi_hasil * $inv->nominal_investasi) / 100;
            $cofBulan = $bagiHasilNominalPa / 12;
            $bagiHasilPerNominal = $inv->lama_investasi * $cofBulan;

            $sisaPokok = max(0, $inv->nominal_investasi - ($total->total_pokok ?? 0));
            $sisaBagiHasil = max(0, $bagiHasilPerNominal - ($total->total_bagi_hasil ?? 0));

            $totalOutstanding += $sisaPokok + $sisaBagiHasil;
        }

        return $totalOutstanding;
    }

    public function getChartDepositoPokok(?string $bulan = null): array
    {
        $currentYear = (int)date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengajuan_investasi_finlog')
            ->select('nama_investor', DB::raw('SUM(nominal_investasi) as total_pokok'))
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth);

        if ($this->isRestricted && $this->investorId) {
            $query->where('id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []]]];
        }

        $result = $query->groupBy('nama_investor')->orderBy('nama_investor')->get();

        if ($result->isEmpty()) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []]]];
        }

        $categories = [];
        $data = [];
        foreach ($result as $item) {
            $categories[] = $item->nama_investor;
            $data[] = (float)$item->total_pokok;
        }

        return ['categories' => $categories, 'series' => [['name' => 'Pokok', 'data' => $data]]];
    }

    public function getChartCoF(?string $bulan = null): array
    {
        $currentYear = (int)date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengajuan_investasi_finlog')
            ->select('nama_investor', 'nominal_investasi', 'persentase_bagi_hasil')
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth);

        if ($this->isRestricted && $this->investorId) {
            $query->where('id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []]]];
        }

        $dataInvestasi = $query->get();

        if ($dataInvestasi->isEmpty()) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []]]];
        }

        $cofPerPerusahaan = [];
        foreach ($dataInvestasi as $item) {
            $bagiHasilNominalPa = ($item->persentase_bagi_hasil * $item->nominal_investasi) / 100;
            $cofBulan = $bagiHasilNominalPa / 12;

            if (!isset($cofPerPerusahaan[$item->nama_investor])) {
                $cofPerPerusahaan[$item->nama_investor] = 0;
            }
            $cofPerPerusahaan[$item->nama_investor] += $cofBulan;
        }

        ksort($cofPerPerusahaan);

        $categories = [];
        $data = [];
        foreach ($cofPerPerusahaan as $nama => $totalCof) {
            $categories[] = $nama;
            $data[] = $totalCof;
        }

        return ['categories' => $categories, 'series' => [['name' => 'Pokok', 'data' => $data]]];
    }

    public function getChartPengembalian(?string $bulan = null): array
    {
        $currentYear = (int)date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengembalian_investasi_finlog as pi')
            ->join('pengajuan_investasi_finlog as pif', 'pi.id_pengajuan_investasi_finlog', '=', 'pif.id_pengajuan_investasi_finlog')
            ->select('pif.nama_investor', DB::raw('SUM(pi.dana_pokok_dibayar) as total_pokok'), DB::raw('SUM(pi.bagi_hasil_dibayar) as total_bagi_hasil'))
            ->whereYear('pi.tanggal_pengembalian', $currentYear)
            ->whereMonth('pi.tanggal_pengembalian', $selectedMonth);

        if ($this->isRestricted && $this->investorId) {
            $query->where('pif.id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []], ['name' => 'Bagi Hasil', 'data' => []]]];
        }

        $result = $query->groupBy('pif.nama_investor')->orderBy('pif.nama_investor')->get();

        if ($result->isEmpty()) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []], ['name' => 'Bagi Hasil', 'data' => []]]];
        }

        $categories = [];
        $dataPokok = [];
        $dataBagiHasil = [];
        foreach ($result as $item) {
            $categories[] = $item->nama_investor;
            $dataPokok[] = (float)$item->total_pokok;
            $dataBagiHasil[] = (float)$item->total_bagi_hasil;
        }

        return [
            'categories' => $categories,
            'series' => [['name' => 'Pokok', 'data' => $dataPokok], ['name' => 'Bagi Hasil', 'data' => $dataBagiHasil]]
        ];
    }

    public function getChartSisaDeposito(?string $bulan = null): array
    {
        $currentYear = (int)date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $investasiQuery = DB::table('pengajuan_investasi_finlog')
            ->select('id_pengajuan_investasi_finlog', 'nama_investor', 'nominal_investasi', 'persentase_bagi_hasil', 'lama_investasi')
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth);

        if ($this->isRestricted && $this->investorId) {
            $investasiQuery->where('id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []], ['name' => 'Bagi Hasil', 'data' => []]]];
        }

        $investasi = $investasiQuery->get();

        if ($investasi->isEmpty()) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []], ['name' => 'Bagi Hasil', 'data' => []]]];
        }

        $totalPengembalian = DB::table('pengembalian_investasi_finlog')
            ->select('id_pengajuan_investasi_finlog', DB::raw('SUM(dana_pokok_dibayar) as total_pokok'), DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil'))
            ->groupBy('id_pengajuan_investasi_finlog')
            ->get()
            ->keyBy('id_pengajuan_investasi_finlog');

        $companyData = [];
        foreach ($investasi as $inv) {
            $total = $totalPengembalian->get($inv->id_pengajuan_investasi_finlog);

            $bagiHasilNominalPa = ($inv->persentase_bagi_hasil * $inv->nominal_investasi) / 100;
            $cofBulan = $bagiHasilNominalPa / 12;
            $bagiHasilPerNominal = $inv->lama_investasi * $cofBulan;

            $sisaPokok = max(0, $inv->nominal_investasi - ($total->total_pokok ?? 0));
            $sisaBagiHasil = max(0, $bagiHasilPerNominal - ($total->total_bagi_hasil ?? 0));

            if ($sisaPokok > 0 || $sisaBagiHasil > 0) {
                if (!isset($companyData[$inv->nama_investor])) {
                    $companyData[$inv->nama_investor] = ['sisa_pokok' => 0, 'sisa_bagi_hasil' => 0];
                }
                $companyData[$inv->nama_investor]['sisa_pokok'] += $sisaPokok;
                $companyData[$inv->nama_investor]['sisa_bagi_hasil'] += $sisaBagiHasil;
            }
        }

        $categories = [];
        $dataSisaPokok = [];
        $dataSisaBagiHasil = [];
        foreach ($companyData as $nama => $data) {
            $categories[] = $nama;
            $dataSisaPokok[] = (float)$data['sisa_pokok'];
            $dataSisaBagiHasil[] = (float)$data['sisa_bagi_hasil'];
        }

        return [
            'categories' => $categories,
            'series' => [['name' => 'Pokok', 'data' => $dataSisaPokok], ['name' => 'Bagi Hasil', 'data' => $dataSisaBagiHasil]]
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
