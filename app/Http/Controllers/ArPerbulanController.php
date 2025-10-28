<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArPerbulanController extends Controller
{
    public function index()
    {
        return view('livewire.ar-perbulan.index');
    }
}
