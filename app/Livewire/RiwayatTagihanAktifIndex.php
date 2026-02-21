<?php

namespace App\Livewire;

use App\Exports\DebiturPiutangExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class RiwayatTagihanAktifIndex extends Component
{
    public $searchTerm = '';

    protected $listeners = ['search-changed' => 'updateSearch'];

    public function updateSearch($value)
    {
        $this->searchTerm = $value;
    }

    public function export()
    {
        $filename = 'Riwayat_Tagihan_Aktif_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new DebiturPiutangExport($this->searchTerm),
            $filename
        );
    }

    public function render()
    {
        return view('livewire.riwayat-tagihan-aktif.index')
            ->layout('layouts.app', ['title' => 'Riwayat Tagihan Aktif']);
    }
}
