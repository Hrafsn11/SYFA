<?php

namespace App\Http\Controllers;

use App\Models\PengembalianPinjaman;
use Illuminate\Http\Request;

class PengembalianPinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('livewire.pengembalian-pinjaman.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('livewire.pengembalian-pinjaman.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PengembalianPinjaman $pengembalianPinjaman)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PengembalianPinjaman $pengembalianPinjaman)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PengembalianPinjaman $pengembalianPinjaman)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PengembalianPinjaman $pengembalianPinjaman)
    {
        //
    }
}
