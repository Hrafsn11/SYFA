<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;

class PengembalianPinjamanFinlog extends Component
{
    public function render()
    {
        return view('livewire.sfinlog.pengembalian-pinjaman.index')
            ->layout('layouts.app', [
                'title' => 'Pengembalian Pinjaman - SFinlog'
            ]);
    }
}
