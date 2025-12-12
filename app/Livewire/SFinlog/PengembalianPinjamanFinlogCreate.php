<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;

class PengembalianPinjamanFinlogCreate extends Component
{
    public function render()
    {
        return view('livewire.sfinlog.pengembalian-pinjaman.create')
            ->layout('layouts.app', [
                'title' => 'Create Pengembalian Pinjaman - SFinlog'
            ]);
    }
}
