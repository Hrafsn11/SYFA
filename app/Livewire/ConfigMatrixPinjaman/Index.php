<?php

namespace App\Livewire\ConfigMatrixPinjaman;

use Livewire\Component;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\ConfigMatrixPinjamanRequest;

class Index extends Component
{
    use HasUniversalFormAction, HasValidate;
    private string $validateClass = ConfigMatrixPinjamanRequest::class;

    #[FieldInput]
    public $nominal, $approve_oleh;

    public function mount()
    {
        $this->setUrlSaveData('store_config', 'config-matrix-pinjaman.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_config', 'config-matrix-pinjaman.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
        $this->setUrlSaveData('delete_config', 'config-matrix-pinjaman.destroy', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    public function render()
    {
        return view('livewire.config-matrix-pinjaman.config-matrix-pinjaman-index');
    }
}
