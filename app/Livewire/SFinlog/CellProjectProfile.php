<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\CellsProject;

#[Layout('layouts.app')]
#[Title('Cell Project Profile')]
class CellProjectProfile extends Component
{
    public function render()
    {
        $cellsProjects = CellsProject::with('projects')->get();
        
        return view('livewire.sfinlog.cell-project-profile.index', [
            'cellsProjects' => $cellsProjects
        ]);
    }
}