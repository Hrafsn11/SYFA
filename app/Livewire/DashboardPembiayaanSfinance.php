<?php

namespace App\Livewire;

use App\Services\DashboardPembiayaanSfinanceService;
use Livewire\Component;

class DashboardPembiayaanSfinance extends Component
{
    // Filter properties - ISOLATED per chart to prevent state collision
    public $bulanDisbursement;  // Filter for disbursement chart
    public $bulanPembayaran;    // Filter for pembayaran chart
    public $bulanSisa;          // Filter for sisa belum terbayar chart
    public $bulanPiutang;       // Filter for piutang chart (deprecated, kept for backward compat)
    public $tahun;              // Global year filter (affects all charts except piutang)
    public $tahunPiutang;       // Separate year filter for piutang chart
    public $bulan1;             // untuk comparison chart
    public $bulan2;             // untuk comparison chart
    public $bulanTable;         // Filter khusus untuk AR Table
    public $tahunTable;         // Filter khusus untuk AR Table

    protected $service;

    public function boot(DashboardPembiayaanSfinanceService $service)
    {
        $this->service = $service;
    }

    public function mount()
    {
        // Initialize all filters with current month/year using Carbon
        $now = \Carbon\Carbon::now();
        $currentMonth = $now->format('m');
        $currentYear = $now->format('Y');

        // Allow string 'now' to be passed in (for test/dev)
        $this->tahun = ($this->tahun === 'now' || empty($this->tahun)) ? $currentYear : $this->tahun;
        $this->tahunPiutang = ($this->tahunPiutang === 'now' || empty($this->tahunPiutang)) ? $currentYear : $this->tahunPiutang;
        $this->bulanDisbursement = ($this->bulanDisbursement === 'now' || empty($this->bulanDisbursement)) ? $currentMonth : $this->bulanDisbursement;
        $this->bulanPembayaran = ($this->bulanPembayaran === 'now' || empty($this->bulanPembayaran)) ? $currentMonth : $this->bulanPembayaran;
        $this->bulanSisa = ($this->bulanSisa === 'now' || empty($this->bulanSisa)) ? $currentMonth : $this->bulanSisa;
        $this->bulanPiutang = ($this->bulanPiutang === 'now' || empty($this->bulanPiutang)) ? $currentMonth : $this->bulanPiutang;
        $this->bulan1 = ($this->bulan1 === 'now' || empty($this->bulan1)) ? $currentMonth : $this->bulan1;
        $this->bulan2 = ($this->bulan2 === 'now' || empty($this->bulan2)) ? $now->copy()->subMonth()->format('m') : $this->bulan2;
        $this->bulanTable = ($this->bulanTable === 'now' || empty($this->bulanTable)) ? $currentMonth : $this->bulanTable;
        $this->tahunTable = ($this->tahunTable === 'now' || empty($this->tahunTable)) ? $currentYear : $this->tahunTable;
    }

    // Update methods untuk trigger re-render ketika filter berubah
    public function updatedBulanDisbursement()
    {
        // Trigger re-render - only this chart affected
        $this->dispatch('updateChartDisbursement');
    }

    public function updatedBulanPembayaran()
    {
        // Trigger re-render - only this chart affected
        $this->dispatch('updateChartPembayaran');
    }

    public function updatedBulanSisa()
    {
        // Trigger re-render - only this chart affected
    }

    public function updatedBulanPiutang()
    {
        // Trigger re-render - only this chart affected
    }

    public function updatedTahun()
    {
        // Trigger re-render - affects all charts that use tahun
    }

    public function updatedTahunPiutang()
    {
        // Trigger re-render - only piutang chart affected
    }

    public function updatedBulan1()
    {
        // Trigger re-render
    }

    public function updatedBulan2()
    {
        // Trigger re-render
    }

    // Add listeners untuk update otomatis ketika property berubah
    protected $listeners = ['updated' => '$refresh'];

    public function render()
    {
        // Get real data from service using ISOLATED filter values

        // Isolasi summary card sesuai filter chart terkait
        $summaryData = $this->service->getSummaryData($this->bulanDisbursement, $this->tahun);
        $summaryDataPembayaran = $this->service->getSummaryData($this->bulanPembayaran, $this->tahun);
        $summaryDataSisa = $this->service->getSummaryData($this->bulanSisa, $this->tahun);
        $summaryDataOutstanding = $this->service->getSummaryData(null, null); // always global

        $chartData = [
            'disbursement' => $this->service->getDisbursementData($this->bulanDisbursement, $this->tahun),
            'pembayaran' => $this->service->getPembayaranData($this->bulanPembayaran, $this->tahun),
            'sisa_belum_terbayar' => $this->service->getSisaBelumTerbayarData($this->bulanSisa, $this->tahun),
            'pembayaran_piutang_tahun' => $this->service->getPembayaranPiutangTahunData($this->tahunPiutang),
            'comparison' => $this->service->getComparisonData($this->bulan1, $this->bulan2, $this->tahun),
        ];

        // Check for empty states in chart data
        $hasDataDisbursement = !empty($chartData['disbursement']['categories']);
        $hasDataPembayaran = !empty($chartData['pembayaran']['categories']);
        $hasDataSisa = !empty($chartData['sisa_belum_terbayar']['categories']);
        $hasDataPiutang = !empty($chartData['pembayaran_piutang_tahun']['categories']);

        $arTableData = $this->service->getArTableData($this->bulanTable, $this->tahunTable);

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

