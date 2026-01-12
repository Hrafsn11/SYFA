<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Attributes\FieldInput;
use App\Attributes\ParameterIDRoute;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\CellsProjectRequest;
use App\Http\Traits\HandlesPermissions;

class MasterCellsProject extends Component
{
    use HasUniversalFormAction, HasValidate, HandlesPermissions, \Livewire\WithFileUploads;

    private string $validateClass = CellsProjectRequest::class;

    #[ParameterIDRoute]
    public $id_cells_project;

    #[FieldInput]
    public $nama_cells_bisnis, $nama_pic, $alamat, $deskripsi_bidang, $tanda_tangan_pic;

    #[FieldInput]
    public $projects = [];

    public function updatedTandaTanganPic()
    {
        $this->validate([
            'tanda_tangan_pic' => 'image|max:2048',
        ]);
    }

    public function setterFormData()
    {
        foreach ($this->getUniversalFieldInputs() as $field) {
            $this->form_data[$field] = $this->{$field};
        }

        // 2. Handle File Upload
        if ($this->tanda_tangan_pic instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $fileName = time() . '_' . uniqid() . '_' . $this->tanda_tangan_pic->getClientOriginalName();
            $path = $this->tanda_tangan_pic->storeAs('tanda_tangan_pic', $fileName, 'public');
            $this->form_data['tanda_tangan_pic'] = $path;
        }
    }

    public function mount()
    {
        $this->checkPermission('master_data.view', 'You do not have permission to view this page.');

        $this->setUrlSaveData('store_cells_project', 'master-data.cells-project.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_cells_project', 'master-data.cells-project.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    public function render()
    {
        return view('livewire.master-data.master-cells-project')
            ->layout('layouts.app', [
                'title' => 'List Cells Project SFinlog'
            ]);
    }
}
