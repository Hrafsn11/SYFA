<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use App\Helpers\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PenyaluranDepositoSfinlog;
use App\Models\PengajuanInvestasiFinlog;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SFinlog\PenyaluranDepositoSfinlogRequest;

class PenyaluranDepositoController extends Controller
{

    /**
     * Store a newly created penyaluran deposito for SFinlog
     */
    public function store(PenyaluranDepositoSfinlogRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $penyaluran = PenyaluranDepositoSfinlog::create($validated);
            $penyaluran->load('pengajuanInvestasiFinlog.project', 'cellsProject', 'project');

            DB::commit();

            return Response::success(null, 'Data penyaluran deposito berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    /**
     * Show the form for editing the specified resource for SFinlog
     */
    public function edit($id)
    {
        try {
            $penyaluran = PenyaluranDepositoSfinlog::where('id_penyaluran_deposito_sfinlog', $id)
                ->select('id_penyaluran_deposito_sfinlog', 'id_pengajuan_investasi_finlog', 'id_cells_project', 'id_project', 'nominal_yang_disalurkan', 'tanggal_pengiriman_dana', 'tanggal_pengembalian', 'bukti_pengembalian')
                ->firstOrFail();

            $result = [
                'id' => $penyaluran->id_penyaluran_deposito_sfinlog,
                'id_pengajuan_investasi_finlog' => $penyaluran->id_pengajuan_investasi_finlog,
                'id_cells_project' => $penyaluran->id_cells_project,
                'id_project' => $penyaluran->id_project,
                'nominal_yang_disalurkan' => $penyaluran->nominal_yang_disalurkan,
                'tanggal_pengiriman_dana' => $penyaluran->tanggal_pengiriman_dana->format('Y-m-d'),
                'tanggal_pengembalian' => $penyaluran->tanggal_pengembalian->format('Y-m-d'),
                'bukti_pengembalian' => $penyaluran->bukti_pengembalian,
            ];

            return Response::success($result, 'Data berhasil ditemukan');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    /**
     * Update the specified resource for SFinlog
     */
    public function update(PenyaluranDepositoSfinlogRequest $request, $id)
    {
        try {
            $penyaluran = PenyaluranDepositoSfinlog::where('id_penyaluran_deposito_sfinlog', $id)->firstOrFail();
            $validated = $request->validated();

            DB::beginTransaction();

            $penyaluran->update($validated);

            DB::commit();
            return Response::success(null, 'Data penyaluran deposito berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    /**
     * Remove the specified resource for SFinlog
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $penyaluran = PenyaluranDepositoSfinlog::where('id_penyaluran_deposito_sfinlog', $id)->firstOrFail();

            if ($penyaluran->bukti_pengembalian && Storage::disk('public')->exists($penyaluran->bukti_pengembalian)) {
                Storage::disk('public')->delete($penyaluran->bukti_pengembalian);
            }
            
            $penyaluran->delete();

            DB::commit();

            return Response::success(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    /**
     * Upload bukti for SFinlog
     */
    public function uploadBukti(PenyaluranDepositoSfinlogRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $penyaluran = PenyaluranDepositoSfinlog::where('id_penyaluran_deposito_sfinlog', $id)->firstOrFail();
            
            if ($request->hasFile('bukti_pengembalian')) {
                // Delete old file if exists
                if ($penyaluran->bukti_pengembalian && Storage::disk('public')->exists($penyaluran->bukti_pengembalian)) {
                    Storage::disk('public')->delete($penyaluran->bukti_pengembalian);
                }
                
                // Store new file
                $file = Storage::disk('public')->put('bukti_pengembalian', $request->file('bukti_pengembalian'));
                $penyaluran->update(['bukti_pengembalian' => $file]);
            }

            DB::commit();

            return Response::success(null, 'Bukti pengembalian berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }
}
