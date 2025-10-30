<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyaluranDanaInvestasiController extends Controller
{
    public function index()
    {
        return view('livewire.penyaluran-dana-investasi.index');
    }
}
