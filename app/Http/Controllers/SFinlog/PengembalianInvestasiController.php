<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengembalianInvestasiController extends Controller
{
    /**
     * Store a newly created pengembalian investasi for SFinlog
     */
    public function index()
    {
        return view('livewire.sfinlog.pengembalian-investasi-sfinlog.index');
    }
}