<?php

namespace App\Livewire\PenyesuaianCicilan;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.penyesuaian-cicilan.index')
            ->layout('layouts.app');
    }
}
