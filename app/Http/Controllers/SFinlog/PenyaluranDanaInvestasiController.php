<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenyaluranDanaInvestasiController extends Controller
{
    /**
     * Display penyaluran dana investasi index for SFinlog
     */
    public function index()
    {
        // TODO: Implementasi logika index khusus SFinlog
        return view('livewire.sfinlog.penyaluran-dana-investasi.index');
    }
}

