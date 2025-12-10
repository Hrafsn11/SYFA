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
    use HasUniversalFormAction, HasValidate, HandlesPermissions;

    private string $validateClass = CellsProjectRequest::class;

    #[ParameterIDRoute]
    public $id_cells_project;

    #[FieldInput]
    public $nama_project;

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
                'title' => 'Master Cells Project'
            ]);
    }
}
