<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;

class Peminjaman extends Component
{
    public function render()
    {
        return view('livewire.sfinlog.peminjaman.index')
        ->layout('layouts.app', [
            'title' => 'Peminjaman Dana'
        ]);
    }
}
