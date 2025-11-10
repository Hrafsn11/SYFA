<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\MasterSumberPendanaanEksternalRequest;

class SumberPendanaanEksternal extends Component
{
    use HasUniversalFormAction, HasValidate;
    private string $validateClass = MasterSumberPendanaanEksternalRequest::class;

    public $nama_instansi;
    public $persentase_bagi_hasil;

    public function mount() {
        $this->setUrlSaveData('store_pendanaan', 'master-data.sumber-pendanaan-eksternal.store', ["callback" => "afterAction"]);
    }

    public function render()
    {
        return view('livewire.master-data.sumber-pendanaan-eksternal')
        ->layout('layouts.app', [
            'title' => 'Master Sumber Pendanaan Eksternal'
        ]);
    }

    public function setterFormData()
    {
        $this->form_data = [
            'nama_instansi' => $this->nama_instansi,
            'persentase_bagi_hasil' => $this->persentase_bagi_hasil
        ];
    }
}
