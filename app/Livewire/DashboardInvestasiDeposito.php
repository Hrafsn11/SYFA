<?php

namespace App\Livewire;

use App\Services\DashboardInvestasiDepositoService;
use Livewire\Component;
use Carbon\Carbon;

class DashboardInvestasiDeposito extends Component
{
    public $bulanDepositoPokok;
    public $bulanCoF;
    public $bulanPengembalian;
    public $bulanSisaDeposito;

    protected DashboardInvestasiDepositoService $service;

    public function boot(DashboardInvestasiDepositoService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        $currentMonth = Carbon::now()->format('m');
        $this->bulanDepositoPokok = $currentMonth;
        $this->bulanCoF = $currentMonth;
        $this->bulanPengembalian = $currentMonth;
        $this->bulanSisaDeposito = $currentMonth;
    }

    public function updatedBulanDepositoPokok(): void {}

    public function updatedBulanCoF(): void {}

    public function updatedBulanPengembalian(): void {}

    public function updatedBulanSisaDeposito(): void {}

    private function getSummaryData(): array
    {
        return $this->service->getSummaryData();
    }

    private function getChartData(): array
    {
        return [
            'deposito_pokok' => $this->service->getChartDepositoPokok($this->bulanDepositoPokok),
            'cof' => $this->service->getChartCoF($this->bulanCoF),
            'pengembalian' => $this->service->getChartPengembalian($this->bulanPengembalian),
            'sisa_deposito' => $this->service->getChartSisaDeposito($this->bulanSisaDeposito),
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
