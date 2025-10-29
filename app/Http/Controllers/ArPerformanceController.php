<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArPerformanceController extends Controller
{
    public function index()
    {
        return view('livewire.ar-performance.index');
    }
}
