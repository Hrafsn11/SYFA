<?php

namespace App\Livewire;

use App\Exports\ArPerbulanExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ArPerbulan extends Component
{
    public $selectedMonth = '';

    protected $listeners = ['filterByMonth'];

    public function filterByMonth($month)
    {
        $this->selectedMonth = $month;
    }

    public function exportToExcel()
    {
        $fileName = 'AR_Perbulan_' . ($this->selectedMonth ?: 'All') . '_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return Excel::download(
            new ArPerbulanExport($this->selectedMonth),
            $fileName
        );
    }

    public function render()
    {
        return view('livewire.ar-perbulan.index');
    }
}
