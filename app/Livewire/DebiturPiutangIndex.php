<?php

namespace App\Livewire;

use Livewire\Component;

class DebiturPiutangIndex extends Component
{
    public function render()
    {
        return view('livewire.debitur-piutang.index')
            ->layout('layouts.app', ['title' => 'AR Debitur Piutang']);
    }
}
