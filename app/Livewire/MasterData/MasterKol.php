<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Livewire\Traits\HasValidate;
use App\Http\Requests\MasterKolRequest;
use Illuminate\Foundation\Http\FormRequest;

class MasterKol extends Component
{    
    use HasUniversalFormAction, HasValidate;
    private string $validateClass = MasterKolRequest::class;
    
    public $kol;
    public $persentase_pencairan;
    public $jmlh_hari_keterlambatan;

    public function render()
    {
        return view('livewire.master-data.master-kol')
        ->layout('layouts.app', [
            'title' => 'Master Kol'
        ]); 
    }

    public function setterFormData()
    {
        $this->form_data = [
            'kol' => $this->kol,
            'persentase_pencairan' => $this->persentase_pencairan,
            'jmlh_hari_keterlambatan' => $this->jmlh_hari_keterlambatan,
        ];
    }
}
