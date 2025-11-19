<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ConfigMatrixPinjaman;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ConfigMatrixPinjamanRequest;

class ConfigMatrixPinjamanController extends Controller
{
    public function store(ConfigMatrixPinjamanRequest $request)
    {
        try {
            $data = $request->validated();
            ConfigMatrixPinjaman::create($data);

            return Response::success(null, 'Matrix Pinjaman berhasil ditambahkan');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function edit($id)
    {
        $m = ConfigMatrixPinjaman::where('id_matrix_pinjaman', $id)->firstOrFail();
        return Response::success($m, 'Matrix Pinjaman berhasil diambil');
    }

    public function update(ConfigMatrixPinjamanRequest $request, $id)
    {
        try {
            $m = ConfigMatrixPinjaman::where('id_matrix_pinjaman', $id)->firstOrFail();
            $m->update([
                'nominal' => $request->input('nominal'),
                'approve_oleh' => $request->input('approve_oleh'),
            ]);

            return Response::success(null, 'Matrix Pinjaman berhasil diupdate');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function destroy($id)
    {
        try {
            $m = ConfigMatrixPinjaman::where('id_matrix_pinjaman', $id)->firstOrFail();
            $m->delete();
            return Response::success(null, 'Matrix Pinjaman berhasil dihapus');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }
}
