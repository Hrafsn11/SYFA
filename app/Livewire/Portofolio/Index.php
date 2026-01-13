<?php

namespace App\Livewire\Portofolio;

use Livewire\Component;
use App\Attributes\FieldInput;
use App\Http\Requests\PortofolioRequest;
use App\Livewire\Traits\HasModal;
use App\Livewire\Traits\HasValidate;
use App\Http\Traits\HandlesPermissions;
use App\Livewire\Traits\HasUniversalFormAction;

class Index extends Component
{
    use HasUniversalFormAction, HasValidate, HasModal, HandlesPermissions;
    private string $validateClass = PortofolioRequest::class;
    
    #[FieldInput]
    public $nama_sbu, $tahun;

    public function mount() {
        // Use the middleware trait to check permission
        // $this->checkPermission('master_data.view', 'You do not have permission to view this page.');
        
        $this->setUrlSaveData('store_porto', 'portofolio.store', ["callback" => "afterAction"]);
    }

    public function render()
    {
        return view('livewire.portofolio.index')
            ->layout('layouts.app', [
                'showSidebar' => false,
                'showNavbar'  => true
            ]);
    }
}
