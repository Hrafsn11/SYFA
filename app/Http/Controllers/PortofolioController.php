<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use Illuminate\Http\Request;
use App\Services\ImportExcel;
use App\Models\LaporanInvestasi;
use Illuminate\Support\Facades\DB;
use App\Jobs\ImportExcelPortofolio;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PortofolioRequest;

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
            DB::beginTransaction();
            $path_prev = null;
            $port = LaporanInvestasi::where('nama_sbu', $request->nama_sbu)->first();

            $path = null;
            if ($request->file_excel) {
                $path = Storage::disk('public')->put('portofolio', $request->file_excel);
            }

            $data = $request->validated();
            $data['path_file'] = $path;
            unset($data['file_excel']);

            if ($port) {
                $path_prev = $port->path_file;

                if ($path_prev != null && Storage::disk('public')->exists($path_prev)) {
                    Storage::disk('public')->delete($path_prev);
                }

                $port->update($data);
            } else {

                $port = LaporanInvestasi::create($data);
            }

            (new ImportExcel($path))->import();

            DB::commit();

            ImportExcelPortofolio::dispatch($path);

            return Response::success(
                null,
                'Data berhasil disimpan.'
            );
        } catch (\Exception $e) {
            DB::rollback();
            return Response::errorCatch($e);
        }
    }
}
