<?php

namespace App\Livewire;

use Livewire\Component;

class ArPerformanceTable extends Component
{
    public $arData;
    public $tahun;

    public function mount($arData, $tahun)
    {
        $this->arData = $arData;
        $this->tahun = $tahun;
    }

    public function render()
    {
        return view('livewire.ar-performance.table');
    }

    // Helper untuk format rupiah
    public function formatRupiah($amount)
    {
        return $amount > 0 ? 'Rp ' . number_format($amount, 0, ',', '.') : '-';
    }
}
