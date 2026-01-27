<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use App\Models\PengajuanInvestasiFinlog;

class PengajuanInvestasiDetail extends Component
{
    public $id;
    public $pengajuan;
    public $projects = [];

    public function mount($id)
    {
        $this->id = $id;
        $this->loadData();
    }

    public function loadData()
    {
        $this->pengajuan = PengajuanInvestasiFinlog::with([
            'investor',
            'project',
            'histories' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'histories.submitBy',
            'histories.approvedBy',
            'histories.rejectedBy'
        ])->findOrFail($this->id);

        // Generate preview nomor kontrak jika belum ada dan current_step >= 4 (sudah disetujui CEO)
        if (empty($this->pengajuan->nomor_kontrak) && $this->pengajuan->current_step >= 4) {
            // Generate preview tanpa save ke database
            if ($this->pengajuan->investor && !empty($this->pengajuan->investor->kode_perusahaan)) {
                $this->pengajuan->preview_nomor_kontrak = \App\Services\ContractNumberService::generateInvestasi(
                    $this->pengajuan->investor->kode_perusahaan,
                    'Finlog',
                    $this->pengajuan->tanggal_investasi
                );
            } else {
                // Investor belum punya kode perusahaan
                $this->pengajuan->kode_perusahaan_missing = true;
            }
        }

        // Load projects for edit modal
        $this->projects = \App\Models\CellsProject::orderBy('nama_cells_bisnis')
            ->get();
    }

    public function refreshData()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.sfinlog.pengajuan-investasi.detail')
            ->layout('layouts.app', [
                'title' => 'Detail Pengajuan Investasi - SFinlog'
            ]);
    }
}
