<?php

namespace App\Livewire;

use Livewire\Component;

class LaporanInvestasiSFinance extends Component
{
    public $year;
    public $globalSearch = '';
    public $filterStatus = '';

    protected $queryString = [
        'year'         => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount()
    {
        $this->year = $this->year ?: '';
    }

    public function updatedGlobalSearch($value)
    {
        $this->dispatch('globalSearchChanged', $value);
    }

    public function updatedYear($value)
    {
        $this->dispatch('yearChanged', $value);
    }

    public function updatedFilterStatus($value)
    {
        $this->dispatch('statusFilterChanged', $value);
    }

    public function clearFilters()
    {
        $this->globalSearch  = '';
        $this->year          = '';
        $this->filterStatus  = '';
        $this->dispatch('globalSearchChanged', '');
        $this->dispatch('yearChanged', '');
        $this->dispatch('statusFilterChanged', '');
    }

    public function render()
    {
        return view('livewire.laporan-investasi-sfinance.index')
            ->layout('layouts.app', ['title' => 'Laporan Investasi SFinance']);
    }
}
