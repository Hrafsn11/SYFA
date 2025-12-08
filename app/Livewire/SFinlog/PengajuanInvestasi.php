<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;

class PengajuanInvestasi extends Component
{
    public function render()
    {
        return view('livewire.sfinlog.pengajuan-investasi.index')
            ->layout('layouts.app', [
                'title' => 'Pengajuan Investasi - SFinlog'
            ]);
    }
}
