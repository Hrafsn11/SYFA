<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Livewire\Traits\HasSaveData;

class MasterKol extends Component
{    
    use HasSaveData;

    public function render()
    {
        return view('livewire.master-data.master-kol')
        ->layout('layouts.app', [
            'title' => 'Master Kol'
        ]);
    }
}
