<?php

namespace App\Livewire\MasterData;

use Livewire\Component;

class DebiturDanInvestor extends Component
{
    public function render()
    {
        return view('livewire.master-data.debitur-dan-investor')
        ->layout('layouts.app', [
            'title' => 'Master Debitur dan Investor'
        ]);
    }
}
