<?php

namespace App\Livewire;

use App\Services\DashboardInvestasiDepositoService;
use Livewire\Component;
use Carbon\Carbon;

class DashboardInvestasiDeposito extends Component
{
    public $bulanInvestasiPokok;
    public $bulanCoF;
    public $bulanPengembalian;
    public $bulanSisaInvestasi;
    public $bulanTahunTren;

    protected DashboardInvestasiDepositoService $service;

    public function boot(DashboardInvestasiDepositoService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        $currentMonth = Carbon::now()->format('m');
        $this->bulanInvestasiPokok = $currentMonth;
        $this->bulanCoF = $currentMonth;
        $this->bulanPengembalian = $currentMonth;
        $this->bulanSisaInvestasi = $currentMonth;
        $this->bulanTahunTren = null;
    }

    public function updatedBulanInvestasiPokok(): void {}

    public function updatedBulanCoF(): void {}

    public function updatedBulanPengembalian(): void {}

    public function updatedBulanSisaInvestasi(): void {}

    public function updatedBulanTahunTren(): void {}

    public function getMonthYearOptions(): array
    {
        $options = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $options[$m->format('Y-m')] = $m->translatedFormat('F Y');
        }
        return $options;
    }

    private function getSummaryData(): array
    {
        return $this->service->getSummaryData();
    }

    private function getChartData(): array
    {
        return [
            'investasi_pokok' => $this->service->getChartInvestasiPokok($this->bulanInvestasiPokok),
            'cof' => $this->service->getChartCoF($this->bulanCoF),
            'pengembalian' => $this->service->getChartPengembalian($this->bulanPengembalian),
            'sisa_investasi' => $this->service->getChartSisaInvestasi($this->bulanSisaInvestasi),
        ];
    }

    public function render()
    {
        $summaryData = $this->getSummaryData();
        $chartData = $this->getChartData();

        return view('livewire.dashboard.investasi', [
            'summaryData' => $summaryData,
            'chartData' => $chartData,
            'monthOptions' => $this->service->getMonthOptions(),
            'monthYearOptions' => $this->getMonthYearOptions(),
            'trenInvestasi' => $this->service->getTrenInvestasiData($this->bulanTahunTren),
            'upcomingMaturities' => $this->service->getUpcomingMaturingInvestments(),
            'upcomingDistributions' => $this->service->getUpcomingMaturingDistributions(),
            'topInvestors' => $this->service->getTopInvestorsList(),
            'jenisInvestasiMix' => $this->service->getJenisInvestasiMix(),
            'isRestricted' => $this->service->isUserRestricted(),
        ])->layout('layouts.app', [
            'title' => 'Dashboard Investasi SFinance'
        ]);
    }
}
