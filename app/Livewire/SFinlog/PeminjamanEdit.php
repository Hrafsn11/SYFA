<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MasterDebiturDanInvestor;
use App\Models\CellsProject;
use App\Models\PeminjamanFinlog;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\SFinlog\PeminjamanRequest;
use Illuminate\Support\Facades\Auth;

class PeminjamanEdit extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads;

    private string $validateClass = PeminjamanRequest::class;

    public $peminjaman;
    public $id_peminjaman;

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

    // Existing file paths (untuk preview)
    public $existing_dokumen_mitra;
    public $existing_form_new_customer;
    public $existing_dokumen_kerja_sama;
    public $existing_dokumen_npa;
    public $existing_akta_perusahaan;
    public $existing_ktp_owner;
    public $existing_ktp_pic;
    public $existing_surat_izin_usaha;
    public $existing_nib_perusahaan;

    public function mount($id)
    {
        $this->id_peminjaman = $id;
        $this->peminjaman = PeminjamanFinlog::with('debitur')->findOrFail($id);

        // Validasi: hanya bisa edit jika status = Draft
        if ($this->peminjaman->status !== 'Draft') {
            session()->flash('error', 'Peminjaman tidak dapat diedit karena sudah tidak dalam status Draft.');
            return redirect()->route('sfinlog.peminjaman.detail', ['id' => $id]);
        }

        // Load data dari peminjaman
        $this->loadPeminjamanData();

        // Load projects
        $this->projects = CellsProject::orderBy('nama_cells_bisnis', 'asc')->get();

        // Load available projects berdasarkan cells project yang dipilih
        if ($this->id_cells_project) {
            $this->updatedIdCellsProject($this->id_cells_project);
        }

        $this->setUrlSaveData('update_peminjaman_finlog', 'sfinlog.peminjaman.update', ["callback" => "afterAction", "id" => $id]);
    }

    private function loadPeminjamanData()
    {
        // Data debitur
        $this->currentDebitur = $this->peminjaman->debitur;
        $this->id_debitur = $this->peminjaman->id_debitur;
        $this->nama_perusahaan = $this->currentDebitur->nama ?? '';

        // Data project
        $this->id_cells_project = $this->peminjaman->id_cells_project;
        $this->nama_project = $this->peminjaman->nama_project;
        $this->durasi_project = $this->peminjaman->durasi_project ?? 0;
        $this->durasi_project_hari = $this->peminjaman->durasi_project_hari ?? 0;

        // Data pinjaman
        $this->nilai_pinjaman = $this->peminjaman->nilai_pinjaman;
        $this->presentase_bagi_hasil = $this->peminjaman->presentase_bagi_hasil;
        $this->nilai_bagi_hasil = $this->peminjaman->nilai_bagi_hasil;
        $this->total_pinjaman = $this->peminjaman->total_pinjaman;

        // Data tanggal
        $this->harapan_tanggal_pencairan = $this->peminjaman->harapan_tanggal_pencairan?->format('Y-m-d');
        $this->top = $this->peminjaman->top;
        $this->rencana_tgl_pengembalian = $this->peminjaman->rencana_tgl_pengembalian?->format('Y-m-d');

        // Catatan
        $this->catatan = $this->peminjaman->catatan;

        // Existing files (untuk preview)
        $this->existing_dokumen_mitra = $this->peminjaman->dokumen_mitra;
        $this->existing_form_new_customer = $this->peminjaman->form_new_customer;
        $this->existing_dokumen_kerja_sama = $this->peminjaman->dokumen_kerja_sama;
        $this->existing_dokumen_npa = $this->peminjaman->dokumen_npa;
        $this->existing_akta_perusahaan = $this->peminjaman->akta_perusahaan;
        $this->existing_ktp_owner = $this->peminjaman->ktp_owner;
        $this->existing_ktp_pic = $this->peminjaman->ktp_pic;
        $this->existing_surat_izin_usaha = $this->peminjaman->surat_izin_usaha;
        $this->existing_nib_perusahaan = $this->peminjaman->nib_perusahaan;
    }

    public function updatedIdCellsProject($value)
    {
        $this->nama_project = null;

        if ($value) {
            $cellsProject = CellsProject::with('projects')->find($value);
            $this->availableProjects = $cellsProject && $cellsProject->projects
                ? $cellsProject->projects->map(function ($project) {
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
        $fileFields = [
            'dokumen_mitra',
            'form_new_customer',
            'dokumen_kerja_sama',
            'dokumen_npa',
            'akta_perusahaan',
            'ktp_owner',
            'ktp_pic',
            'surat_izin_usaha',
            'nib_perusahaan'
        ];

        foreach ($this->getUniversalFieldInputs() as $field) {
            $value = $this->{$field};

            if (in_array($field, $fileFields)) {
                if ($value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    // File baru diupload
                    $fileName = time() . '_' . $field . '_' . $value->getClientOriginalName();
                    $filePath = $value->storeAs('peminjaman_finlog', $fileName, 'public');
                    $this->form_data[$field] = $filePath;
                } else {
                    // Gunakan file existing jika tidak ada upload baru
                    $existingField = 'existing_' . $field;
                    if (property_exists($this, $existingField) && $this->{$existingField}) {
                        $this->form_data[$field] = $this->{$existingField};
                    }
                }
            } else {
                $this->form_data[$field] = $value;
            }
        }
    }

    public function render()
    {
        return view('livewire.sfinlog.peminjaman.edit')
            ->layout('layouts.app', [
                'title' => 'Edit Peminjaman Dana'
            ]);
    }
}
