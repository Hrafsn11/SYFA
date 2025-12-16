<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasi;

class KertasKerjaInvestorSFinlogController extends Controller
{
    /**
     * Display kertas kerja investor SFinlog report
     */
    public function index()
    {
        return view('livewire.sfinlog.kertas-kerja-investor-sfinlog.index');
    }
}