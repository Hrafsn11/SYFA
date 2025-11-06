<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Livewire\Traits\HasValidate;
use App\Http\Requests\MasterKolRequest;
use Illuminate\Foundation\Http\FormRequest;

class MasterKol extends Component
{    
    private string $validateClass = MasterKolRequest::class;

    use HasUniversalFormAction, HasValidate;

    public function render()
    {
        return view('livewire.master-data.master-kol')
        ->layout('layouts.app', [
            'title' => 'Master Kol'
        ]);
    }
}
