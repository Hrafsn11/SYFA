<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MasterDebiturDanInvestor;
use App\Models\CellsProject;
use App\Models\Project;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\SFinlog\PeminjamanRequest;
use Illuminate\Support\Facades\Auth;

class PeminjamanCreate extends Component
{    
    use HasUniversalFormAction, HasValidate, WithFileUploads;
    
    private string $validateClass = PeminjamanRequest::class;
    
    #[FieldInput]
    public $id_debitur, $id_cells_project, $nama_project, $durasi_project, $durasi_project_hari;
    
    #[FieldInput]
    public $nilai_pinjaman, $presentase_bagi_hasil, $nilai_bagi_hasil, $total_pinjaman;
    
    #[FieldInput]
    public $harapan_tanggal_pencairan, $top, $rencana_tgl_pengembalian;
    
    #[FieldInput]
    public $dokumen_mitra, $form_new_customer, $dokumen_kerja_sama, $dokumen_npa;
    
    #[FieldInput]
    public $akta_perusahaan, $ktp_owner, $ktp_pic, $surat_izin_usaha, $nib_perusahaan;
    
    #[FieldInput]
    public $catatan;
    
    public $projects = [];
    public $availableProjects = [];
    public $currentDebitur = null;
    public $nama_perusahaan = '';

    public function mount() 
    {
        $this->currentDebitur = MasterDebiturDanInvestor::where('user_id', Auth::id())
                                            ->where('flagging', 'tidak')
                                            ->first();
        
        if ($this->currentDebitur) {
            $this->id_debitur = $this->currentDebitur->id_debitur;
            $this->nama_perusahaan = $this->currentDebitur->nama;
        }
        
        $this->projects = CellsProject::orderBy('nama_cells_bisnis', 'asc')->get();
        
        // Set default values
        $this->durasi_project = 0;
        $this->durasi_project_hari = 0;

        $this->setUrlSaveData('store_peminjaman_finlog', 'sfinlog.peminjaman.store', ["callback" => "afterAction"]);
    }

    public function updatedIdCellsProject($value)
    {
        $this->nama_project = null;
        
        if ($value) {
            $cellsProject = CellsProject::with('projects')->find($value);
            $this->availableProjects = $cellsProject && $cellsProject->projects 
                ? $cellsProject->projects->map(function($project) {
                    return [
                        'id_project' => $project->id_project,
                        'nama_project' => $project->nama_project
                    ];
                })->toArray() 
                : [];
        } else {
            $this->availableProjects = [];
        }
    }

    public function setterFormData()
    {
        $fileFields = ['dokumen_mitra', 'form_new_customer', 'dokumen_kerja_sama', 'dokumen_npa', 
                      'akta_perusahaan', 'ktp_owner', 'ktp_pic', 'surat_izin_usaha', 'nib_perusahaan'];
        
        foreach ($this->getUniversalFieldInputs() as $field) {
            $value = $this->{$field};
            
            if (in_array($field, $fileFields) && $value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $fileName = time() . '_' . $field . '_' . $value->getClientOriginalName();
                $filePath = $value->storeAs('peminjaman_finlog', $fileName, 'public');
                $this->form_data[$field] = $filePath;
            } else {
                $this->form_data[$field] = $value;
            }
        }
    }

    public function render()
    {
        return view('livewire.sfinlog.peminjaman.create')
        ->layout('layouts.app', [
            'title' => 'Buat Peminjaman Dana'
        ]);
    }
}
