<?php

namespace App\Livewire\MasterData;

use Livewire\Component;

class SumberPendanaanEksternal extends Component
{
    public function render()
    {
        return view('livewire.master-data.sumber-pendanaan-eksternal')
        ->layout('layouts.app', [
            'title' => 'Master Sumber Pendanaan Eksternal'
        ]);
    }
}
