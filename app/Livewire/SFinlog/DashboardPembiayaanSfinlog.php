<?php

namespace App\Livewire\Sfinlog;

use Livewire\Component;
use App\Services\PembiayaanSFinlogService;
use Carbon\Carbon;

class DashboardPembiayaanSfinlog extends Component
{
    public $bulanDisbursement;
    public $bulanPembayaran;
    public $bulanSisa;
    public $tahunPiutang;
    public $bulan1;
    public $bulan2;
    public $bulanTable;
    public $tahunTable;

    protected PembiayaanSFinlogService $service;

    public function boot(PembiayaanSFinlogService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        $now = Carbon::now();
        $currentMonth = $now->format('m');
        $currentYear = $now->format('Y');
        $previousMonth = $now->copy()->subMonth()->format('m');

        $this->bulanDisbursement = $currentMonth;
        $this->bulanPembayaran = $currentMonth;
        $this->bulanSisa = $currentMonth;
        $this->tahunPiutang = $currentYear;
        $this->bulan1 = $currentMonth;
        $this->bulan2 = $previousMonth;
        $this->bulanTable = $currentMonth;
        $this->tahunTable = $currentYear;
    }

    public function updatedBulanDisbursement(): void {}
    public function updatedBulanPembayaran(): void {}
    public function updatedBulanSisa(): void {}
    public function updatedTahunPiutang(): void {}
    public function updatedBulan1(): void {}
    public function updatedBulan2(): void {}
    public function updatedBulanTable(): void {}
    public function updatedTahunTable(): void {}

    private function getSummaryData(): array
    {
        return $this->service->getSummaryData();
    }

    private function getChartData(): array
    {
        $currentYear = (int)date('Y');

        $comparisonRaw = $this->service->getComparisonData(
            $this->bulan1,
            $this->bulan2,
            $currentYear
        );

        return [
            'disbursement' => $this->service->getDisbursementData($this->bulanDisbursement, $currentYear),
            'pembayaran' => $this->service->getPembayaranData($this->bulanPembayaran, $currentYear),
            'sisa_belum_terbayar' => $this->service->getSisaBelumTerbayarData($this->bulanSisa, $currentYear),
            'pembayaran_piutang_tahun' => $this->service->getPembayaranPiutangTahunData((int)$this->tahunPiutang),
            'comparison' => [
                'categories' => $comparisonRaw['categories'] ?? [],
                'ar' => [
                    $comparisonRaw['ar_bulan2'] ?? 0,
                    $comparisonRaw['ar_bulan1'] ?? 0
                ],
                'utang_pengembalian_deposito' => [
                    $comparisonRaw['utang_bulan2'] ?? 0,
                    $comparisonRaw['utang_bulan1'] ?? 0
                ],
                'ar_selisih' => $comparisonRaw['ar_selisih'] ?? 0,
                'utang_selisih' => $comparisonRaw['utang_selisih'] ?? 0,
            ],
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

    public function render()
    {
        $summaryData = $this->getSummaryData();
        $chartData = $this->getChartData();
        $hasData = $this->checkDataAvailability($chartData);
        $arTableData = $this->service->getArTableData($this->bulanTable, (int)$this->tahunTable);

        return view('livewire.sfinlog.dashboard-pembiayaan-sfinlog', [
            'summaryData' => $summaryData,
            'chartData' => $chartData,
            'arTableData' => $arTableData,
            'hasData' => $hasData,
        ])->layout('layouts.app', [
            'title' => 'Dashboard Pembiayaan Sfinlog'
        ]);
    }
}
