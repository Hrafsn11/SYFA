<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\MasterKol;

class MasterKolController extends Controller
{
    public function index()
    {
        $data = MasterKol::orderBy('id_kol', 'asc')->get();
        return view('livewire.master-data-kol.index', compact('data'));
    }

    public function create()
    {
        return view('master.kol.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kol' => 'required|integer',
            'persentase_pencairan' => 'nullable|numeric|min:0|max:100',
            'jmlh_hari_keterlambatan' => 'nullable|integer',
        ]);

    $kol = MasterKol::create($data);

    return response()->json(['success' => true, 'data' => $kol->toArray()]);
    }

    public function edit($id)
    {
    $kol = MasterKol::findOrFail($id);
    return response()->json(['data' => $kol->toArray()]);
    }

    public function update(Request $request, $id)
    {
        $kol = MasterKol::findOrFail($id);
        $data = $request->validate([
            'kol' => 'required|integer',
            'persentase_pencairan' => 'nullable|numeric|min:0|max:100',
            'jmlh_hari_keterlambatan' => 'nullable|integer',
        ]);

    $kol->update($data);

    $kol->refresh();

    return response()->json(['success' => true, 'data' => $kol->toArray()]);
    }

    public function destroy($id)
    {
        $kol = MasterKol::findOrFail($id);
        $kol->delete();
        return response()->json(['success' => true]);
    }
}
