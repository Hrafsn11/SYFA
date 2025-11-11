<?php

namespace App\Http\Controllers\Master;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterSumberPendanaanEksternalRequest;
use App\Models\MasterSumberPendanaanEksternal;
use Illuminate\Http\Request;

class MasterSumberPendanaanEksternalController extends Controller
{
    // public function index()
    // {
    //     return view('livewire.master-sumber-pendanaan-eksternal.index');
    // }

    public function create() {}

    public function store(MasterSumberPendanaanEksternalRequest $request)
    {
        try {
            MasterSumberPendanaanEksternal::create($request->validated());
            return Response::success(null, 'Sumber Pendanaan berhasil ditambahkan');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function edit($id)
    {
        $item = MasterSumberPendanaanEksternal::where('id_instansi', $id)->firstOrFail();
        return Response::success($item, 'Sumber Pendanaan berhasil diambil');
    }

    public function update(MasterSumberPendanaanEksternalRequest $request, $id)
    {
        try {
            $item = MasterSumberPendanaanEksternal::where('id_instansi', $id)->firstOrFail();
            $item->update($request->validated());
            return Response::success(null, 'Sumber Pendanaan berhasil diupdate');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function destroy($id)
    {
        try {
            $item = MasterSumberPendanaanEksternal::where('id_instansi', $id)->firstOrFail();
            $item->delete();
            return Response::success(null , 'Sumber Pendanaan berhasil dihapus');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }
}
