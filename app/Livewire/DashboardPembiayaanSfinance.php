<?php

namespace App\Livewire;

use App\Services\DashboardPembiayaanSfinanceService;
use Livewire\Component;
use Carbon\Carbon;

class DashboardPembiayaanSfinance extends Component
{
    // Filter properties - ISOLATED per chart
    public $bulanDisbursement;
    public $bulanPembayaran;
    public $bulanSisa;
    public $tahun;              // Global year
    public $tahunPiutang;       // Separate year filter for piutang chart
    public $bulan1;             // Comparison chart
    public $bulan2;             // Comparison chart
    public $bulanTable;         // AR Table filter
    public $tahunTable;         // AR Table filter

    protected $service;

    public function boot(DashboardPembiayaanSfinanceService $service)
    {
        $this->service = $service;
    }

    public function mount()
    {
        $now = Carbon::now();
        $currentMonth = $now->format('m');
        $currentYear = $now->format('Y');
        $previousMonth = $now->copy()->subMonth()->format('m');

        // Initialize with logic handling for 'now' or empty
        $this->tahun = ($this->tahun === 'now' || empty($this->tahun)) ? $currentYear : $this->tahun;
        $this->tahunPiutang = ($this->tahunPiutang === 'now' || empty($this->tahunPiutang)) ? $currentYear : $this->tahunPiutang;
        
        $this->bulanDisbursement = ($this->bulanDisbursement === 'now' || empty($this->bulanDisbursement)) ? $currentMonth : $this->bulanDisbursement;
        $this->bulanPembayaran = ($this->bulanPembayaran === 'now' || empty($this->bulanPembayaran)) ? $currentMonth : $this->bulanPembayaran;
        $this->bulanSisa = ($this->bulanSisa === 'now' || empty($this->bulanSisa)) ? $currentMonth : $this->bulanSisa;
        
        $this->bulan1 = ($this->bulan1 === 'now' || empty($this->bulan1)) ? $currentMonth : $this->bulan1;
        $this->bulan2 = ($this->bulan2 === 'now' || empty($this->bulan2)) ? $previousMonth : $this->bulan2;
        
        $this->bulanTable = ($this->bulanTable === 'now' || empty($this->bulanTable)) ? $currentMonth : $this->bulanTable;
        $this->tahunTable = ($this->tahunTable === 'now' || empty($this->tahunTable)) ? $currentYear : $this->tahunTable;
    }

    // Update methods to trigger specific chart re-renders
    public function updatedBulanDisbursement()
    {
        $this->dispatch('updateChartDisbursement');
    }

    public function updatedBulanPembayaran()
    {
        $this->dispatch('updateChartPembayaran');
    }

    public function updatedBulanSisa()
    {
        $this->dispatch('updateChartSisa');
    }

    public function updatedTahunPiutang()
    {
        $this->dispatch('updateChartPiutang');
    }

    // Comparison & Table updates are handled by wire:key auto-refresh logic usually, 
    // but dispatching event doesn't hurt for consistency.

    protected $listeners = ['updated' => '$refresh'];

    public function render()
    {
        // 1. Summary Data
        $summaryData = $this->service->getSummaryData($this->bulanDisbursement, $this->tahun);
        $summaryDataPembayaran = $this->service->getSummaryData($this->bulanPembayaran, $this->tahun);
        $summaryDataSisa = $this->service->getSummaryData($this->bulanSisa, $this->tahun);
        $summaryDataOutstanding = $this->service->getSummaryData(null, null); // Global

        // 2. Chart Data
        $chartData = [
            'disbursement' => $this->service->getDisbursementData($this->bulanDisbursement, $this->tahun),
            'pembayaran' => $this->service->getPembayaranData($this->bulanPembayaran, $this->tahun),
            'sisa_belum_terbayar' => $this->service->getSisaBelumTerbayarData($this->bulanSisa, $this->tahun),
            'pembayaran_piutang_tahun' => $this->service->getPembayaranPiutangTahunData($this->tahunPiutang),
            'comparison' => $this->service->getComparisonData($this->bulan1, $this->bulan2, $this->tahun),
        ];

        // 3. Table Data
        $arTableData = $this->service->getArTableData($this->bulanTable, $this->tahunTable);

        // Check availability
        $hasDataDisbursement = !empty($chartData['disbursement']['categories']);
        $hasDataPembayaran = !empty($chartData['pembayaran']['categories']);
        $hasDataSisa = !empty($chartData['sisa_belum_terbayar']['categories']);
        $hasDataPiutang = !empty($chartData['pembayaran_piutang_tahun']['categories']);

        return view('livewire.dashboard-pembiayaan-sfinance', [
            'summaryData' => $summaryData,
            'summaryDataPembayaran' => $summaryDataPembayaran,
            'summaryDataSisa' => $summaryDataSisa,
            'summaryDataOutstanding' => $summaryDataOutstanding,
            'chartData' => $chartData,
            'arTableData' => $arTableData,
            'hasDataDisbursement' => $hasDataDisbursement,
            'hasDataPembayaran' => $hasDataPembayaran,
            'hasDataSisa' => $hasDataSisa,
            'hasDataPiutang' => $hasDataPiutang,
        ])->layout('layouts.app', [
            'title' => 'Dashboard Pembiayaan Sfinance'
        ]);
    }
}