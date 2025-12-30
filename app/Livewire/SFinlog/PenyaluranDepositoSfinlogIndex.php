<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\MasterDebiturDanInvestor;
use App\Models\CellsProject;
use App\Models\Project;
use Livewire\WithFileUploads;
use App\Attributes\FieldInput;
use App\Attributes\ParameterIDRoute;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\SFinlog\PenyaluranDepositoSfinlogRequest;

class PenyaluranDepositoSfinlogIndex extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads;
    
    private string $validateClass = PenyaluranDepositoSfinlogRequest::class;

    #[ParameterIDRoute]
    public $id;
    
    #[FieldInput]
    public $id_pengajuan_investasi_finlog, $id_cells_project, $id_project, $nominal_yang_disalurkan, $tanggal_pengiriman_dana, $tanggal_pengembalian, $bukti_pengembalian;

    public $availableProjects = [];

    public function mount()
    {
        $this->setUrlSaveData('store_penyaluran_deposito_sfinlog', 'sfinlog.penyaluran-deposito-sfinlog.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_penyaluran_deposito_sfinlog', 'sfinlog.penyaluran-deposito-sfinlog.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    /**
     * Get pengajuan investasi finlog yang sudah memiliki nomor kontrak
     */
    public function getPengajuanInvestasiFinlogProperty()
    {
        return PengajuanInvestasiFinlog::query()
            ->with(['project.projects', 'investor'])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->orderBy('created_at', 'desc')
            ->get();
    }


    /**
     * Get cells project dari master data
     */
    public function getCellsProjectProperty()
    {
        return CellsProject::orderBy('nama_cells_bisnis', 'asc')->get();
    }

    /**
     * Handle ketika id_pengajuan_investasi_finlog berubah
     */
    public function updatedIdPengajuanInvestasiFinlog($value)
    {
        if ($value) {
            $pengajuan = PengajuanInvestasiFinlog::with('project.projects')->find($value);
            if ($pengajuan && $pengajuan->project) {
                $this->id_cells_project = $pengajuan->id_cells_project;
                
                // Load projects untuk cell bisnis ini
                $cellsProject = CellsProject::with('projects')->find($pengajuan->id_cells_project);
                if ($cellsProject && $cellsProject->projects && $cellsProject->projects->isNotEmpty()) {
                    $this->availableProjects = $cellsProject->projects->map(function($project) {
                        return [
                            'id_project' => $project->id_project,
                            'nama_project' => $project->nama_project
                        ];
                    })->toArray();
                    
                    // Set project pertama dari pengajuan investasi jika ada
                    if ($pengajuan->project->projects->isNotEmpty()) {
                        $this->id_project = $pengajuan->project->projects->first()->id_project;
                    }
                } else {
                    $this->availableProjects = [];
                    $this->id_project = null;
                }
            }
        } else {
            $this->id_cells_project = null;
            $this->id_project = null;
            $this->availableProjects = [];
        }
    }

    /**
     * Handle ketika id_cells_project berubah
     */
    public function updatedIdCellsProject($value)
    {
        // Jangan reset id_project jika sudah ada value (untuk edit mode)
        if (!$this->id) {
            $this->id_project = null;
        }
        
        if ($value) {
            $cellsProject = CellsProject::with('projects')->find($value);
            if ($cellsProject && $cellsProject->projects && $cellsProject->projects->isNotEmpty()) {
                $this->availableProjects = $cellsProject->projects->map(function($project) {
                    return [
                        'id_project' => $project->id_project,
                        'nama_project' => $project->nama_project
                    ];
                })->toArray();
            } else {
                $this->availableProjects = [];
            }
        } else {
            $this->availableProjects = [];
        }
        
        // Dispatch event untuk update dropdown project di frontend
        $this->dispatch('updateProjects', ['projects' => $this->availableProjects]);
    }

    public function render()
    {
        return view('livewire.sfinlog.penyaluran-deposito-sfinlog.index', [
            'pengajuanInvestasiFinlog' => $this->pengajuanInvestasiFinlog,
            'cellsProject' => $this->cellsProject,
            'availableProjects' => $this->availableProjects ?? [],
        ])
        ->layout('layouts.app', [
            'title' => 'Penyaluran Deposito - SFinlog'
        ]);
    }
}

