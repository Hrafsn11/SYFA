<?php

namespace App\Http\Controllers\Master;

use App\Helpers\Response;
use App\Models\MasterKol;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterKolRequest;

class MasterKolController extends Controller
{
    // public function index()
    // {
    //     return view('livewire.master-data-kol.index');
    // }

    // public function create()
    // {
        
    // }

    public function store(MasterKolRequest $request)
    {
        MasterKol::create($request->validated());
        return Response::success(null, 'KOL berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kol = MasterKol::where('id_kol', $id)->firstOrFail();
        return Response::success($kol, 'KOL berhasil diambil');
    }

    public function update(MasterKolRequest $request, $id)
    {
        $kol = MasterKol::where('id_kol', $id)->firstOrFail();
        $kol->update($request->validated());
        return Response::success(null, 'KOL berhasil diupdate');
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
