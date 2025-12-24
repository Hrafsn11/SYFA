<?php

namespace App\Livewire\ProgramRestrukturisasi;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.program-restrukturisasi.index')
            ->layout('layouts.app');
    }
}
