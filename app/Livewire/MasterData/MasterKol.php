<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasModal;
use App\Livewire\Traits\HasValidate;
use App\Http\Requests\MasterKolRequest;
use App\Http\Traits\HandlesPermissions;
use App\Livewire\Traits\HasUniversalFormAction;

class MasterKol extends Component
{    
    use HasUniversalFormAction, HasValidate, HasModal, HandlesPermissions;
    private string $validateClass = MasterKolRequest::class;
    
    #[FieldInput]
    public $kol, $persentase_pencairan, $jmlh_hari_keterlambatan;

    public function mount() {
        // Use the middleware trait to check permission
        $this->checkPermission('master_data.view', 'You do not have permission to view this page.');
        
        $this->setUrlSaveData('store_master_kol', 'master-data.kol.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_master_kol', 'master-data.kol.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
        $this->setUrlSaveData('delete_master_kol', 'master-data.kol.destroy', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    public function render()
    {
        return view('livewire.master-data.master-kol')
        ->layout('layouts.app', [
            'title' => 'Master Kol'
        ]);
    }
}
