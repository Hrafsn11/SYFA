<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterDebiturDanInvestor;
use App\Models\MasterKol;

class DebiturDanInvestorController extends Controller
{
    public function index()
    {
        $kol = MasterKol::orderBy('id_kol', 'asc')->get();
        return view('livewire.master-data-debitur-investor.index', compact('kol'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kol' => 'required|exists:master_kol,id_kol',
            'nama_debitur' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'nama_ceo' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'no_rek' => 'nullable|string|max:100',
            'flagging' => 'nullable|string|in:ya,tidak'
        ]);

        $debitur = MasterDebiturDanInvestor::create($validated);
        $debitur->load('kol');

        return response()->json([
            'success' => true,
            'message' => 'Debitur berhasil ditambahkan',
            'data' => $debitur->toArray()
        ]);
    }

    public function edit($id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->with('kol')->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $debitur->toArray()
        ]);
    }

    public function update(Request $request, $id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();
        
        $validated = $request->validate([
            'id_kol' => 'required|exists:master_kol,id_kol',
            'nama_debitur' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'nama_ceo' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'no_rek' => 'nullable|string|max:100',
            'flagging' => 'nullable|string|in:ya,tidak'
        ]);

        $debitur->update($validated);
        $debitur->refresh();
        $debitur->load('kol');

        return response()->json([
            'success' => true,
            'message' => 'Debitur berhasil diupdate',
            'data' => $debitur->toArray()
        ]);
    }

    public function destroy($id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();
        $debitur->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Debitur berhasil dihapus'
        ]);
    }
}
