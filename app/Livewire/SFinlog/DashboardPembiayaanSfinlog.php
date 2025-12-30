<?php

namespace App\Livewire\Sfinlog;

use Livewire\Component;
use App\Services\PembiayaanSFinlogService;
use Carbon\Carbon;

class DashboardPembiayaanSfinlog extends Component
{
    // Filter properties - ISOLATED per chart to prevent state collision (Sama seperti Sfinance)
    public $bulanDisbursement;
    public $bulanPembayaran;
    public $bulanSisa;
    public $tahun;              // Global year filter
    public $tahunPiutang;       // Separate year filter for piutang chart
    public $bulan1;             // untuk comparison chart
    public $bulan2;             // untuk comparison chart
    public $bulanTable;         // Filter khusus untuk AR Table
    public $tahunTable;         // Filter khusus untuk AR Table

    protected $service;

    public function boot(PembiayaanSFinlogService $service)
    {
        $this->service = $service;
    }

    public function mount()
    {
        // Initialize all filters with current month/year using Carbon
        $now = Carbon::now();
        $currentMonth = $now->format('m');
        $currentYear = $now->format('Y');
        $previousMonth = $now->copy()->subMonth()->format('m');

        // Logic inisialisasi sama seperti Sfinance
        $this->tahun = ($this->tahun === 'now' || empty($this->tahun)) ? $currentYear : $this->tahun;
        $this->tahunPiutang = ($this->tahunPiutang === 'now' || empty($this->tahunPiutang)) ? $currentYear : $this->tahunPiutang;
        $this->bulanDisbursement = ($this->bulanDisbursement === 'now' || empty($this->bulanDisbursement)) ? $currentMonth : $this->bulanDisbursement;
        $this->bulanPembayaran = ($this->bulanPembayaran === 'now' || empty($this->bulanPembayaran)) ? $currentMonth : $this->bulanPembayaran;
        $this->bulanSisa = ($this->bulanSisa === 'now' || empty($this->bulanSisa)) ? $currentMonth : $this->bulanSisa;
        
        // Comparison defaults
        $this->bulan1 = ($this->bulan1 === 'now' || empty($this->bulan1)) ? $currentMonth : $this->bulan1;
        // Default bulan2 adalah bulan lalu
        $this->bulan2 = ($this->bulan2 === 'now' || empty($this->bulan2)) ? $previousMonth : $this->bulan2;
        
        $this->bulanTable = ($this->bulanTable === 'now' || empty($this->bulanTable)) ? $currentMonth : $this->bulanTable;
        $this->tahunTable = ($this->tahunTable === 'now' || empty($this->tahunTable)) ? $currentYear : $this->tahunTable;
    }

    // Update methods untuk trigger re-render CHART SPESIFIK (Sama seperti Sfinance)
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
        // Chart sisa biasanya re-render otomatis via wire:key, tapi bisa dispatch jika perlu
        $this->dispatch('updateChartSisa'); 
    }

    public function updatedTahunPiutang()
    {
        $this->dispatch('updateChartPiutang');
    }

    // Add listeners untuk update otomatis ketika property berubah
    protected $listeners = ['updated' => '$refresh'];

    public function render()
    {
        // Get real data from service using ISOLATED filter values

        // 1. Summary Cards (Menggunakan method service Sfinlog tapi pattern Sfinance)
        $summaryData = $this->service->getSummaryDisbursement(
            $this->bulanDisbursement, 
            $this->tahun
        );

        $summaryDataPembayaran = $this->service->getSummaryPembayaran(
            $this->bulanPembayaran, 
            $this->tahun
        );

        $summaryDataSisa = $this->service->getSummarySisa(
            $this->bulanSisa, 
            $this->tahun
        );

        $summaryDataOutstanding = $this->service->getSummaryOutstanding(
            $this->tahun // Global year only
        );

        // 2. Chart Data
        
        // Ambil data comparison raw dari service Sfinlog
        $comparisonRaw = $this->service->getComparisonData(
            $this->bulan1, 
            $this->bulan2, 
            $this->tahun
        );

        // Format ulang data comparison agar sesuai dengan struktur JS Sfinance
        // Sfinlog Service return keys: ar_bulan1, ar_bulan2, etc.
        // Frontend Sfinance expect array series.
        $comparisonFormatted = [
            'categories' => $comparisonRaw['categories'] ?? [],
            'ar' => [
                $comparisonRaw['ar_bulan2'] ?? 0, // Data kiri (bulan lama/bulan 2)
                $comparisonRaw['ar_bulan1'] ?? 0  // Data kanan (bulan baru/bulan 1)
            ],
            'utang_pengembalian_deposito' => [
                $comparisonRaw['utang_bulan2'] ?? 0,
                $comparisonRaw['utang_bulan1'] ?? 0
            ],
            // Data tambahan untuk display text selisih di view (Fitur unik Sfinlog)
            'ar_selisih' => $comparisonRaw['ar_selisih'] ?? 0,
            'utang_selisih' => $comparisonRaw['utang_selisih'] ?? 0,
        ];

        $chartData = [
            'disbursement' => $this->service->getDisbursementData(
                $this->bulanDisbursement, 
                $this->tahun
            ),
            'pembayaran' => $this->service->getPembayaranData(
                $this->bulanPembayaran, 
                $this->tahun
            ),
            'sisa_belum_terbayar' => $this->service->getSisaBelumTerbayarData(
                $this->bulanSisa, 
                $this->tahun
            ),
            'pembayaran_piutang_tahun' => $this->service->getPembayaranPiutangTahunData(
                $this->tahunPiutang
            ),
            'comparison' => $comparisonFormatted,
        ];

        // Check for empty states
        $hasDataDisbursement = !empty($chartData['disbursement']['categories']);
        $hasDataPembayaran = !empty($chartData['pembayaran']['categories']);
        $hasDataSisa = !empty($chartData['sisa_belum_terbayar']['categories']);
        $hasDataPiutang = !empty($chartData['pembayaran_piutang_tahun']['categories']);

        $arTableData = $this->service->getArTableData(
            $this->bulanTable, 
            $this->tahunTable
        );

        return view('livewire.sfinlog.dashboard-pembiayaan-sfinlog', [
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
            'title' => 'Dashboard Pembiayaan Sfinlog'
        ]);
    }
}