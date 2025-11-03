<?php

namespace App\Livewire\MasterData;

use Livewire\Component;

class MasterKaryawanSki extends Component
{
    public function render()
    {
        return view('livewire.master-data.master-karyawan-ski')
        ->layout('layouts.app', [
            'title' => 'Master Karyawan SKI'
        ]);
    }
}
