<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;

class ReportPengembalian extends Component
{
    public function render()
    {
        return view('livewire.sfinlog.report-pengembalian.index')
        ->layout('layouts.app', [
            'title' => 'Report Pengembalian'
        ]);
    }
}
