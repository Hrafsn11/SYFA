<?php

namespace App\Livewire;

use Livewire\Component;

class KertasKerjaInvestorSFinance extends Component
{
    public $year;
    public $perPage = 10;
    public $search = '';

    protected $queryString = [
        'year' => ['except' => ''],
    ];

    public function mount()
    {
        $this->year = $this->year ?: date('Y');
    }

    public function applyFilter()
    {
        // Dispatch event to all child tables
        $this->dispatch('yearChanged', $this->year);
    }

    public function render()
    {
        return view('livewire.kertas-kerja-investor-sfinance.index')
            ->layout('layouts.app', ['title' => 'Kertas Kerja Investor SFinance']);
    }
}
