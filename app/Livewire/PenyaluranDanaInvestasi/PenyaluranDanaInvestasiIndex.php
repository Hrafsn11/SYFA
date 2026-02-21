<?php

namespace App\Livewire\PenyaluranDanaInvestasi;

use Livewire\Component;

class PenyaluranDanaInvestasiIndex extends Component
{
    public function render()
    {
        return view('livewire.penyaluran-dana-investasi.penyaluran-dana-investasi-index')
            ->layout('layouts.app', ['title' => 'Penyaluran Dana Investasi']);
    }
}
