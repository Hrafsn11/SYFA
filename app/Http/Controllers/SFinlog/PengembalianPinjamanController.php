<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengembalianPinjamanController extends Controller
{
    // /**
    //  * Show the form for creating a new pengembalian pinjaman for SFinlog
    //  */
    // public function create()
    // {
    //     // TODO: Implementasi logika create khusus SFinlog
    //     return view('livewire.sfinlog.pengembalian-pinjaman.create', [
    //         'pengajuanPeminjaman' => collect([]),
    //         'namaPerusahaan' => '',
    //     ]);
    // }

    /**
     * Store a newly created pengembalian pinjaman for SFinlog
     */
    public function store(Request $request)
    {
        // TODO: Implementasi logika store khusus SFinlog
        return redirect()->route('sfinlog.pengembalian.index');
    }
}

