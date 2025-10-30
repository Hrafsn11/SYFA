<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RencanaPenagihanDepositoController extends Controller
{
    // Untuk SKI (Admin/Staff)
    public function ski()
    {
        return view('livewire.rencana-penagihan-deposito.ski.index');
    }

    // Untuk Penerima Dana (Debitur/Investor)
    public function penerimaDana()
    {
        return view('livewire.rencana-penagihan-deposito.penerima-dana.index');
    }
}
