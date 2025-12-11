<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArPerbulanController extends Controller
{
    /**
     * Display AR Perbulan index for SFinlog
     */
    public function index()
    {
        // TODO: Implementasi logika AR Perbulan khusus SFinlog
        return view('livewire.sfinlog.ar-perbulan.index');
    }

    /**
     * Update AR for SFinlog
     */
    public function updateAR(Request $request)
    {
        // TODO: Implementasi logika update AR khusus SFinlog
        return response()->json([
            'success' => true,
            'message' => 'AR Perbulan berhasil diupdate untuk SFinlog'
        ]);
    }
}

