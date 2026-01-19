<?php

namespace App\Livewire;

use App\Services\DashboardPembiayaanSfinanceService;
use Livewire\Component;
use Carbon\Carbon;

class DashboardPembiayaanSfinance extends Component
{
    private const DEFAULT_YEAR_RANGE_PAST = 5;
    private const DEFAULT_YEAR_RANGE_FUTURE = 1;

    public $bulanDisbursement;
    public $bulanPembayaran;
    public $bulanSisa;
    public $tahun;
    public $tahunPiutang;

    public $bulan1;
    public $bulan2;

    public $bulanTable;
    public $tahunTable;

    protected DashboardPembiayaanSfinanceService $service;

    public function boot(DashboardPembiayaanSfinanceService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        $now = Carbon::now();
        $currentMonth = $now->format('m');
        $currentYear = $now->format('Y');
        $previousMonth = $now->copy()->subMonth()->format('m');

        $this->tahun = $currentYear;
        $this->tahunPiutang = $currentYear;
        $this->tahunTable = $currentYear;

        $this->bulanDisbursement = $currentMonth;
        $this->bulanPembayaran = $currentMonth;
        $this->bulanSisa = $currentMonth;
        $this->bulanTable = $currentMonth;

        $this->bulan1 = $currentMonth;
        $this->bulan2 = $previousMonth;
    }

    public function updatedBulanDisbursement(): void
    {
        $this->dispatch('updateChartDisbursement');
    }

    public function updatedBulanPembayaran(): void
    {
        $this->dispatch('updateChartPembayaran');
    }

    public function updatedBulanSisa(): void
    {
        $this->dispatch('updateChartSisa');
    }

    public function updatedTahunPiutang(): void
    {
        $this->dispatch('updateChartPiutang');
    }

    public function updatedBulan1(): void
    {
        $this->dispatch('updateChartComparison');
    }

    public function updatedBulan2(): void
    {
        $this->dispatch('updateChartComparison');
    }

    public function updatedBulanTable(): void {}

    public function updatedTahunTable(): void {}

    private function getSummaryData(): array
    {
        $now = Carbon::now();
        return $this->service->getSummaryData($now->format('m'), $now->format('Y'));
    }

    private function getChartData(): array
    {
        return [
            'disbursement' => $this->service->getDisbursementData($this->bulanDisbursement, $this->tahun),
            'pembayaran' => $this->service->getPembayaranData($this->bulanPembayaran, $this->tahun),
            'sisa_belum_terbayar' => $this->service->getSisaBelumTerbayarData($this->bulanSisa, $this->tahun),
            'pembayaran_piutang_tahun' => $this->service->getPembayaranPiutangTahunData($this->tahunPiutang),
            'comparison' => $this->service->getComparisonData($this->bulan1, $this->bulan2, $this->tahun),
        ];
    }

    private function checkDataAvailability(array $chartData): array
    {
        return [
            'disbursement' => !empty($chartData['disbursement']['categories']),
            'pembayaran' => !empty($chartData['pembayaran']['categories']),
            'sisa' => !empty($chartData['sisa_belum_terbayar']['categories']),
            'piutang' => !empty($chartData['pembayaran_piutang_tahun']['categories']),
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

    public function getYearOptions(): array
    {
        $currentYear = (int) date('Y');
        $years = [];

        for ($year = $currentYear - self::DEFAULT_YEAR_RANGE_PAST; $year <= $currentYear + self::DEFAULT_YEAR_RANGE_FUTURE; $year++) {
            $years[$year] = (string) $year;
        }

        return $years;
    }

    public function render()
    {
        $summaryData = $this->getSummaryData();
        $chartData = $this->getChartData();
        $hasData = $this->checkDataAvailability($chartData);
        $arTableData = $this->service->getArTableData($this->bulanTable, $this->tahunTable);

        return view('livewire.dashboard-pembiayaan-sfinance', [
            'summaryData' => $summaryData,
            'chartData' => $chartData,
            'hasDataDisbursement' => $hasData['disbursement'],
            'hasDataPembayaran' => $hasData['pembayaran'],
            'hasDataSisa' => $hasData['sisa'],
            'hasDataPiutang' => $hasData['piutang'],
            'arTableData' => $arTableData,
            'monthOptions' => $this->getMonthOptions(),
            'yearOptions' => $this->getYearOptions(),
        ])->layout('layouts.app', [
            'title' => 'Dashboard Pembiayaan SFinance'
        ]);
    }
}
