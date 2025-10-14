<?php

namespace App\Livewire\SumberPendanaanEksternal;

use Livewire\Component;

class Index extends Component
{
    public $data;

    public function mount()
    {
        $this->data = [
            ['nama_instansi' => 'Bank ABC', 'presentase_bagi_hasil' => '5%'],
            ['nama_instansi' => 'Lembaga XYZ', 'presentase_bagi_hasil' => '7%'],
            ['nama_instansi' => 'Investor 123', 'presentase_bagi_hasil' => '6%'],
        ];
    }

    public function render()
    {
        return view('livewire.sumber-pendanaan-eksternal.index')
        ->layout('layouts.app');
    }
}
