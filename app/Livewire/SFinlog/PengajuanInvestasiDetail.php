<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;

class PengajuanInvestasiDetail extends Component
{
    public $id;

    public function mount($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        return view('livewire.sfinlog.pengajuan-investasi.detail')
            ->layout('layouts.app', [
                'title' => 'Pengajuan Investasi - SFinlog'
            ]);
    }
}
