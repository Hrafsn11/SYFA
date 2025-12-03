<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Services\DebiturPiutangService;

class DebiturPiutangIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public ?string $selectedPeriod = null;
    public ?string $selectedIdPengembalian = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function boot(DebiturPiutangService $debiturPiutangService): void
    {
        $this->debiturPiutangService = $debiturPiutangService;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    #[On('refresh-data')]
    public function refreshData(): void
    {
        $this->debiturPiutangService->clearCache();
        
        $this->dispatch('show-alert', [
            'type' => 'success',
            'message' => 'Data berhasil di-refresh!'
        ]);
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function setPeriod($period): void
    {
        $this->selectedPeriod = $period;
    }

    public function selectPengembalian($idPengembalian): void
    {
        $this->selectedIdPengembalian = $idPengembalian;
    }

    public function render()
    {
        // Fetch main data with fresh query (no cache)
        $debiturPiutangData = $this->debiturPiutangService->getDebiturPiutangData(
            $this->perPage,
            $this->search,
            false // Always fetch fresh data
        );

        // Auto-select first item if none selected
        if (!$this->selectedIdPengembalian && $debiturPiutangData->count() > 0) {
            $firstItem = $debiturPiutangData->first();
            $this->selectedIdPengembalian = $firstItem->id_pengembalian;
        }

        // Initialize default values
        $historiPembayaran = collect();
        $summaryData = [
            'subtotal_sisa' => 0,
            'pokok' => 0,
            'sisa_bagi_hasil' => 0,
            'telat_hari' => 0,
        ];

        // Fetch related data only if ID exists
        if ($this->selectedIdPengembalian) {
            $historiPembayaran = $this->debiturPiutangService->getHistoriPembayaran(
                $this->selectedIdPengembalian,
                $this->selectedPeriod
            );
            
            $summaryData = $this->debiturPiutangService->getSummaryData(
                $this->selectedIdPengembalian,
                $this->selectedPeriod
            );
        }

        return view('livewire.debitur-piutang.index', [
            'debiturPiutangData' => $debiturPiutangData,
            'historiPembayaran' => $historiPembayaran,
            'summaryData' => $summaryData,
        ])->layout('layouts.app', [
            'title' => 'AR Debitur Piutang'
        ]);
    }
}
