<?php

namespace App\Livewire;

use Livewire\Component;

class Peminjaman extends Component
{
    public function render()
    {
        return view('livewire.peminjaman')
            ->layout('layouts.app');
    }
}
