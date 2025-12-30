<?php

namespace App\Livewire;

use App\Exports\DebiturPiutangExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class DebiturPiutangIndex extends Component
{
    public $searchTerm = '';

    protected $listeners = ['search-changed' => 'updateSearch'];

    public function updateSearch($value)
    {
        $this->searchTerm = $value;
    }

    public function export()
    {
        $filename = 'Debitur_Piutang_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new DebiturPiutangExport($this->searchTerm),
            $filename
        );
    }

    public function render()
    {
        return view('livewire.debitur-piutang.index')
            ->layout('layouts.app', ['title' => 'AR Debitur Piutang']);
    }
}
