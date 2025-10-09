<?php

namespace App\Livewire;

use Livewire\Component;

class ConfigMatrixScore extends Component
{
    public function render()
    {
        return view('livewire.config-matrix-score.index')
        ->layout('layouts.app');
    }
}
