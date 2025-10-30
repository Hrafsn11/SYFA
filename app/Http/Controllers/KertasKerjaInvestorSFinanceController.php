<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KertasKerjaInvestorSFinanceController extends Controller
{
    public function index()
    {
        return view('livewire.kertas-kerja-investor-sfinance.index');
    }
}
