<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterSumberPendanaanEksternal;
use Illuminate\Http\Request;

class MasterSumberPendanaanEksternalController extends Controller
{
    public function index()
    {
        return view('livewire.master-sumber-pendanaan-eksternal.index');
    }

    public function create() {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'persentase_bagi_hasil' => 'nullable|integer|min:0|max:100',
        ]);

        $item = MasterSumberPendanaanEksternal::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Sumber Pendanaan berhasil ditambahkan',
            'data' => $item->toArray()
        ]);
    }

    public function edit($id)
    {
        $item = MasterSumberPendanaanEksternal::where('id_instansi', $id)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $item->toArray()
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = MasterSumberPendanaanEksternal::where('id_instansi', $id)->firstOrFail();
        $data = $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'persentase_bagi_hasil' => 'nullable|integer|min:0|max:100',
        ]);
        $item->update($data);
        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Sumber Pendanaan berhasil diupdate',
            'data' => $item->toArray()
        ]);
    }

    public function destroy($id)
    {
        $item = MasterSumberPendanaanEksternal::where('id_instansi', $id)->firstOrFail();
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sumber Pendanaan berhasil dihapus'
        ]);
    }
}
