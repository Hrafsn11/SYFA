<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ArPerformanceService;

class ArPerformanceIndex extends Component
{
    // Filters
    public $tahun;

    // Inject Service
    protected $arPerformanceService;

    public function boot(ArPerformanceService $arPerformanceService)
    {
        $this->arPerformanceService = $arPerformanceService;
    }

    public function mount()
    {
        $this->tahun = date('Y');
    }

    public function render()
    {
        $arData = $this->arPerformanceService->getArPerformanceData($this->tahun);

        return view('livewire.ar-performance.index', [
            'arData' => $arData,
        ])->layout('layouts.app', [
            'title' => 'AR Performance'
        ]);
    }

    public function updatedTahun()
    {
        // Auto-refresh ketika tahun berubah
        $this->dispatch('filter-changed');
    }

    public function refreshData()
    {
        // Clear cache
        $this->arPerformanceService->clearCache($this->tahun);
        
        // Dispatch event for success notification
        $this->dispatch('show-alert', [
            'type' => 'success',
            'message' => 'Data berhasil di-refresh!'
        ]);
    }
}
