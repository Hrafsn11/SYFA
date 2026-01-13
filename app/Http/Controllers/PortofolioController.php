<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Http\Requests\PortofolioRequest;
use App\Models\LaporanInvestasi;
use Illuminate\Http\Request;

class PortofolioController extends Controller
{
    public function getData($id)
    {
        try {
            $port = LaporanInvestasi::findOrFail($id);

            return Response::success(
                $port,
                'Data berhasil dimuat.'
            );
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function store(PortofolioRequest $request)
    {
        try {
            $port = LaporanInvestasi::where('nama_sbu', $request->nama_sbu)->first();

            if ($port) {
                $port->update($request->validated());
            } else {
                $port = LaporanInvestasi::create($request->validated());
            }

            return Response::success(
                null,
                'Data berhasil disimpan.'
            );
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }
}
