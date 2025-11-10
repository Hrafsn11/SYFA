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
        try {
            MasterKol::create($request->validated());
            return Response::success(null, 'KOL berhasil ditambahkan');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function edit($id)
    {
        $kol = MasterKol::where('id_kol', $id)->firstOrFail();
        return Response::success($kol, 'KOL berhasil diambil');
    }

    public function update(MasterKolRequest $request, $id)
    {
        try {
            $kol = MasterKol::where('id_kol', $id)->firstOrFail();
            $kol->update($request->validated());
            return Response::success(null, 'KOL berhasil diupdate');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function destroy($id)
    {
        try {
            $kol = MasterKol::where('id_kol', $id)->firstOrFail();
            $kol->delete();
            return Response::success(null, 'KOL berhasil dihapus');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }
}
