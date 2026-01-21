<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;

class KertasKerjaInvestorSFinlog extends Component
{
    public $year;

    protected $queryString = [
        'year' => ['except' => ''],
    ];

    public function mount()
    {
        $this->year = $this->year ?: date('Y');
    }

    public function render()
    {
        return view('livewire.sfinlog.kertas-kerja-investor-sfinlog.index')
            ->layout('layouts.app', ['title' => 'Kertas Kerja Investor SFinlog']);
    }
}
