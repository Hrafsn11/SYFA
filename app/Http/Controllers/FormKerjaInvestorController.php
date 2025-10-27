<?php

namespace App\Http\Controllers;

use App\Models\FormKerjaInvestor;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormKerjaInvestorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $investor = null;
        if (Auth::check()) {
            $investor = MasterDebiturDanInvestor::where('user_id', Auth::id())
                ->where('flagging', 'ya')
                ->first();
        }
        
        return view('livewire.form-kerja-investor.index', compact('investor'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(FormKerjaInvestor $formKerjaInvestor)
    {
        //
        return view('livewire.form-kerja-investor.detail');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FormKerjaInvestor $formKerjaInvestor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FormKerjaInvestor $formKerjaInvestor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormKerjaInvestor $formKerjaInvestor)
    {
        //
    }
}
