<?php

namespace App\Livewire\PengembalianPinjaman;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.pengembalian-pinjaman.index')
            ->layout('layouts.app', [
                'title' => 'Pengembalian Pinjaman'
            ]);
    }
}
