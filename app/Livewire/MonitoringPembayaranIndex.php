<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\ArPerformanceService;

class MonitoringPembayaranIndex extends Component
{
    // Filters
    public $tahun;
    public $bulan;

    // Inject Service
    protected $arPerformanceService;

    public function boot(ArPerformanceService $arPerformanceService)
    {
        $this->arPerformanceService = $arPerformanceService;
    }

    public function mount()
    {
        $this->tahun = date('Y');
        $this->bulan = null; // Default: semua bulan
    }

    public function render()
    {
        // Always fetch fresh data (no cache)
        $arData = $this->arPerformanceService->getArPerformanceData($this->tahun, $this->bulan, false);

        return view('livewire.monitoring-pembayaran.index', [
            'arData' => $arData,
        ])->layout('layouts.app', [
            'title' => 'Monitoring Pembayaran'
        ]);
    }

    public function updatedTahun()
    {
        // Auto-refresh ketika tahun berubah
        $this->dispatch('filter-changed');
    }

    public function updatedBulan()
    {
        // Auto-refresh ketika bulan berubah
        $this->dispatch('filter-changed');
    }

    // #[On('refresh-data')]
    // public function refreshData()
    // {
    //     // Clear cache and refresh data
    //     $this->arPerformanceService->clearCache($this->tahun);
        
    //     // Dispatch event for success notification
    //     $this->dispatch('show-alert', [
    //         'type' => 'success',
    //         'message' => 'Data berhasil di-refresh!'
    //     ]);
    // }
}
