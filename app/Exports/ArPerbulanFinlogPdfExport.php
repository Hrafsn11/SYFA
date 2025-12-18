<?php

namespace App\Exports;

use App\Models\ArPerbulanFinlog;

class ArPerbulanFinlogPdfExport
{
    protected $selectedMonth = '';

    public function __construct($selectedMonth = '')
    {
        $this->selectedMonth = $selectedMonth;
    }

    public function getData()
    {
        $query = ArPerbulanFinlog::query();

        if ($this->selectedMonth) {
            $query->where('bulan', $this->selectedMonth);
        }

        return $query->orderBy('bulan', 'desc')
            ->orderBy('nama_perusahaan', 'asc')
            ->get();
    }

    public function getFileName()
    {
        $month = $this->selectedMonth ?: 'All';
        return 'AR_Perbulan_Finlog_' . $month . '_' . now()->format('Y-m-d_His');
    }
}
