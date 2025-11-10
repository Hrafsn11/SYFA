<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasValidate;
use App\Http\Requests\MasterKolRequest;
use Illuminate\Foundation\Http\FormRequest;
use App\Livewire\Traits\HasUniversalFormAction;

class MasterKol extends Component
{    
    use HasUniversalFormAction, HasValidate;
    private string $validateClass = MasterKolRequest::class;
    
    #[FieldInput]
    public $kol, $persentase_pencairan, $jmlh_hari_keterlambatan;

    public function mount() {
        $this->setUrlSaveData('store_master_kol', 'master-data.kol.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_master_kol', 'master-data.kol.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

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
