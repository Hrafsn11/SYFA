<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use App\Models\PengajuanInvestasiFinlog;

class PengajuanInvestasiDetail extends Component
{
    public $id;
    public $pengajuan;

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
            'histories' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'histories.submitBy',
            'histories.approvedBy',
            'histories.rejectedBy'
        ])->findOrFail($this->id);
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
