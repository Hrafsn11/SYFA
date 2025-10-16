<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\MasterSumberPendanaanEksternal;

class MasterSumberPendanaanEksternalController extends Controller
{
    public function index()
    {
        $data = MasterSumberPendanaanEksternal::orderBy('id_instansi','asc')->get();
        return view('livewire.master-sumber-pendanaan-eksternal.index', compact('data'));
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'persentase_bagi_hasil' => 'nullable|integer',
        ]);

        $item = MasterSumberPendanaanEksternal::create($data);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function edit($id)
    {
        $item = MasterSumberPendanaanEksternal::findOrFail($id);
        return response()->json(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = MasterSumberPendanaanEksternal::findOrFail($id);
        $data = $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'persentase_bagi_hasil' => 'nullable|integer',
        ]);
        $item->update($data);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        $item = MasterSumberPendanaanEksternal::findOrFail($id);
        $item->delete();
        return response()->json(['success' => true]);
    }
}
