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
    }

    public function updatedBulanInvestasiPokok(): void {}

    public function updatedBulanCoF(): void {}

    public function updatedBulanPengembalian(): void {}

    public function updatedBulanSisaInvestasi(): void {}

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

        return view('livewire.dashboard-investasi-deposito', [
            'summaryData' => $summaryData,
            'chartData' => $chartData,
            'monthOptions' => $this->service->getMonthOptions(),
        ])->layout('layouts.app', [
            'title' => 'Dashboard Investasi SFinance'
        ]);
    }
}
