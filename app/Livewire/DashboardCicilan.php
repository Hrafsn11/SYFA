<?php

namespace App\Livewire;

use App\Services\DashboardCicilanService;
use Livewire\Component;

class DashboardCicilan extends Component
{
    // Filter per bulan-tahun untuk chart Tren Pengajuan
    public $bulanTahunTren;

    // Filter per bulan-tahun untuk chart Pokok & Margin
    public $bulanTahunPokokMargin;

    protected DashboardCicilanService $service;

    public function boot(DashboardCicilanService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        $this->bulanTahunTren        = now()->format('Y-m');
        $this->bulanTahunPokokMargin  = now()->format('Y-m');
    }

    // -------------------------------------------------------
    // Watcher → trigger chart re-render via data-holder attrs
    // -------------------------------------------------------
    public function updatedBulanTahunTren(): void {}
    public function updatedBulanTahunPokokMargin(): void {}

    // -------------------------------------------------------
    // Data helpers
    // -------------------------------------------------------
    public function getYearOptions(): array
    {
        return $this->service->getYearOptions();
    }

    /**
     * 12 opsi bulan terakhir untuk filter tren.
     */
    public function getMonthYearOptions(): array
    {
        $options = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $options[$m->format('Y-m')] = $m->format('M Y');
        }
        return $options;
    }

    public function render()
    {
        $summaryData      = $this->service->getSummaryData();
        $monthYearOptions = $this->getMonthYearOptions();

        $chartData = [
            'tren_pengajuan'        => $this->service->getTrenBulanData($this->bulanTahunTren),
            'jenis_restrukturisasi' => $this->service->getJenisRestrukturisasiData(),
            'pokok_margin'          => $this->service->getPokokMarginBulanData($this->bulanTahunPokokMargin),
            'angsuran_per_debitur'  => $this->service->getAngsuranPerDebiturData(),
        ];

        $debiturMonitoringData = $this->service->getDebiturMonitoringData();

        return view('livewire.dashboard.cicilan', compact(
            'summaryData',
            'chartData',
            'monthYearOptions',
            'debiturMonitoringData'
        ));
    }
}
