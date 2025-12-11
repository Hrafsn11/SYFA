<?php

namespace App\Livewire;

use App\Models\MasterDebiturDanInvestor;
use App\Services\KolHistoryService;
use Livewire\Component;

class KolHistoryIndex extends Component
{
    public $debiturId;
    public $debitur;
    public $tahun;
    public $kolHistory = [];

    protected $kolHistoryService;

    public function boot(KolHistoryService $kolHistoryService)
    {
        $this->kolHistoryService = $kolHistoryService;
    }

    public function mount($id)
    {
        $this->debiturId = $id;
        $this->debitur = MasterDebiturDanInvestor::where('id_debitur', $id)
            ->with('kol')
            ->firstOrFail();
        
        // Default to current year
        $this->tahun = request()->get('tahun', date('Y'));
        
        $this->loadKolHistory();
    }

    public function updatedTahun()
    {
        $this->loadKolHistory();
    }

    public function loadKolHistory()
    {
        if (!$this->tahun) {
            $this->kolHistory = [];
            return;
        }

        $this->kolHistory = $this->kolHistoryService->getKolHistoryByYear(
            $this->debiturId,
            $this->tahun
        );
    }

    public function render()
    {
        return view('livewire.kol-history.index', [
            'debitur' => $this->debitur,
            'kolHistory' => $this->kolHistory,
            'tahun' => $this->tahun,
        ])->layout('layouts.app', [
            'title' => 'History KOL - ' . $this->debitur->nama
        ]);
    }
}

