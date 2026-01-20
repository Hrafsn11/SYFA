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
use App\Livewire\Traits\HasModal;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HandleComponentEvent;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\SFinlog\PenyaluranDepositoSfinlogRequest;

class PenyaluranDepositoSfinlogIndex extends Component
{
    use HasUniversalFormAction, HasValidate, HasModal, HandleComponentEvent, WithFileUploads;

    private string $validateClass = PenyaluranDepositoSfinlogRequest::class;

    #[ParameterIDRoute]
    public $id;

    #[FieldInput]
    public $id_pengajuan_investasi_finlog, $id_cells_project, $id_project, $nominal_yang_disalurkan, $tanggal_pengiriman_dana, $tanggal_pengembalian;

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
     * Load projects berdasarkan cell bisnis
     */
    private function loadProjectsByCellBisnis($cellBisnisId)
    {
        if (!$cellBisnisId) {
            $this->availableProjects = [];
            return;
        }

        $cellsProject = CellsProject::with('projects')->find($cellBisnisId);

        if ($cellsProject && $cellsProject->projects && $cellsProject->projects->isNotEmpty()) {
            $this->availableProjects = $cellsProject->projects->map(fn($p) => [
                'id_project' => $p->id_project,
                'nama_project' => $p->nama_project
            ])->toArray();
        } else {
            $this->availableProjects = [];
        }
    }

    /**
     * Handle ketika id_pengajuan_investasi_finlog berubah
     */
    public function updatedIdPengajuanInvestasiFinlog($value)
    {
        if (!$value) {
            $this->id_cells_project = null;
            $this->id_project = null;
            $this->availableProjects = [];
            return;
        }

        $pengajuan = PengajuanInvestasiFinlog::with('project.projects')->find($value);

        if (!$pengajuan || !$pengajuan->project) {
            return;
        }

        $this->id_cells_project = $pengajuan->id_cells_project;
        $this->loadProjectsByCellBisnis($pengajuan->id_cells_project);

        // Set project pertama dari pengajuan investasi jika ada
        if ($pengajuan->project->projects->isNotEmpty()) {
            $this->id_project = $pengajuan->project->projects->first()->id_project;
        }

        $this->dispatch('updateProjects', projects: $this->availableProjects);
    }

    /**
     * Handle ketika id_cells_project berubah
     */
    public function updatedIdCellsProject($value)
    {
        // Jangan reset id_project jika dalam edit mode
        if (!$this->id) {
            $this->id_project = null;
        }

        $this->loadProjectsByCellBisnis($value);

        // Dispatch event with projects data
        $this->dispatch('updateProjects', projects: $this->availableProjects);
    }

    /**
     * Update nominal yang dikembalikan dari project
     */
    public function updateNominalPengembalian($id, $nominal)
    {
        try {
            $penyaluran = \App\Models\PenyaluranDepositoSfinlog::findOrFail($id);

            // Validasi: nominal dikembalikan tidak boleh lebih besar dari nominal disalurkan
            if ($nominal > $penyaluran->nominal_yang_disalurkan) {
                $this->dispatch('showAlert', [
                    'type' => 'error',
                    'message' => 'Nominal yang dikembalikan tidak boleh lebih besar dari nominal yang disalurkan!'
                ]);
                return;
            }

            // Validasi: nominal tidak boleh negatif
            if ($nominal < 0) {
                $this->dispatch('showAlert', [
                    'type' => 'error',
                    'message' => 'Nominal yang dikembalikan tidak boleh negatif!'
                ]);
                return;
            }

            // Update nominal
            $penyaluran->update([
                'nominal_yang_dikembalikan' => $nominal
            ]);

            // Refresh table
            $this->dispatch('refreshPenyaluranDepositoSfinlogTable');

            $this->dispatch('showAlert', [
                'type' => 'success',
                'message' => 'Nominal pengembalian berhasil disimpan!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating nominal pengembalian: ' . $e->getMessage());

            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data!'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.sfinlog.penyaluran-deposito-sfinlog.index', [
            'pengajuanInvestasiFinlog' => $this->pengajuanInvestasiFinlog,
            'cellsProject' => $this->cellsProject,
            'availableProjects' => $this->availableProjects ?? [],
        ])
            ->layout('layouts.app', [
                'title' => 'Aset Investasi - SFinlog'
            ]);
    }
}

