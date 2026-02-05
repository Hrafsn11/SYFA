<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\ArPerformanceFinlogService;

class ArPerformanceIndex extends Component
{
    // Filters
    public $tahun;
    public $bulan;

    // Inject Service
    protected $arPerformanceService;

    public function boot(ArPerformanceFinlogService $arPerformanceService)
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
        $arData = $this->arPerformanceService->getArPerformanceData(
            $this->tahun, 
            $this->bulan ?: null, 
            false
        );

        return view('livewire.sfinlog.ar-performance.index', [
            'arData' => $arData,
        ])->layout('layouts.app', [
            'title' => 'AR Performance - SFinlog'
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
}
