<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasModal;
use App\Livewire\Traits\HasValidate;
use App\Http\Traits\HandlesPermissions;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\MasterSumberPendanaanEksternalRequest;

class SumberPendanaanEksternal extends Component
{
    use HasUniversalFormAction, HasValidate, HasModal, HandlesPermissions;
    private string $validateClass = MasterSumberPendanaanEksternalRequest::class;

    #[FieldInput]
    public $nama_instansi, $persentase_bunga;

    public function mount() {

        $this->checkPermission('master_data.view', 'You do not have permission to view this page.');

        $this->setUrlSaveData('store_pendanaan', 'master-data.sumber-pendanaan-eksternal.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_pendanaan', 'master-data.sumber-pendanaan-eksternal.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
        $this->setUrlSaveData('delete_pendanaan', 'master-data.sumber-pendanaan-eksternal.destroy', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    public function render()
    {
        return view('livewire.master-data.sumber-pendanaan-eksternal')
        ->layout('layouts.app', [
            'title' => 'Master Sumber Pendanaan Eksternal'
        ]);
    }
}
