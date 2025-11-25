<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyaluranDepositoController extends Controller
{
    public function index()
    {
        return view('livewire.penyaluran-deposito.index');
    }
}

