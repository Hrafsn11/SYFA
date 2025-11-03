<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterKol;

class MasterKolController extends Controller
{
    // public function index()
    // {
    //     return view('livewire.master-data-kol.index');
    // }

    // public function create()
    // {
        
    // }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kol' => 'required|integer',
            'persentase_pencairan' => 'nullable|numeric|min:0|max:100',
            'jmlh_hari_keterlambatan' => 'nullable|integer',
        ]);

        $kol = MasterKol::create($data);

        return response()->json([
            'success' => true,
            'message' => 'KOL berhasil ditambahkan',
            'data' => $kol->toArray()
        ]);
    }

    public function edit($id)
    {
        $kol = MasterKol::where('id_kol', $id)->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $kol->toArray()
        ]);
    }

    public function update(Request $request, $id)
    {
        $kol = MasterKol::where('id_kol', $id)->firstOrFail();
        $data = $request->validate([
            'kol' => 'required|integer',
            'persentase_pencairan' => 'nullable|numeric|min:0|max:100',
            'jmlh_hari_keterlambatan' => 'nullable|integer',
        ]);

        $kol->update($data);
        $kol->refresh();

        return response()->json([
            'success' => true,
            'message' => 'KOL berhasil diupdate',
            'data' => $kol->toArray()
        ]);
    }

    public function destroy($id)
    {
        $kol = MasterKol::where('id_kol', $id)->firstOrFail();
        $kol->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'KOL berhasil dihapus'
        ]);
    }
}
