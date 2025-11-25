<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Models\PenyaluranDeposito;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PenyaluranDepositoRequest;

class PenyaluranDepositoController extends Controller
{
    public function index()
    {
        return view('livewire.penyaluran-deposito.index');
    }

    public function store(PenyaluranDepositoRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $penyaluran = PenyaluranDeposito::create($validated);
            $penyaluran->load('pengajuanInvestasi', 'debitur');

            DB::commit();

            return Response::success(null, 'Data penyaluran deposito berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    public function edit($id)
    {
        try {
            $penyaluran = PenyaluranDeposito::where('id_penyaluran_deposito', $id)
                ->select('id_penyaluran_deposito', 'id_pengajuan_investasi', 'id_debitur', 'nominal_yang_disalurkan', 'tanggal_pengiriman_dana', 'tanggal_pengembalian', 'bukti_pengembalian')
                ->firstOrFail();

            $result = [
                'id' => $penyaluran->id_penyaluran_deposito,
                'id_pengajuan_investasi' => $penyaluran->id_pengajuan_investasi,
                'id_debitur' => $penyaluran->id_debitur,
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

    public function update(PenyaluranDepositoRequest $request, $id)
    {
        $penyaluran = PenyaluranDeposito::where('id_penyaluran_deposito', $id)->firstOrFail();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $penyaluran->update($validated);

            DB::commit();
            return Response::success(null, 'Data penyaluran deposito berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    public function destroy($id)
    {
        try {
            $penyaluran = PenyaluranDeposito::where('id_penyaluran_deposito', $id)->firstOrFail();
            
            if ($penyaluran->bukti_pengembalian && Storage::disk('public')->exists($penyaluran->bukti_pengembalian)) {
                Storage::disk('public')->delete($penyaluran->bukti_pengembalian);
            }
            
            $penyaluran->delete();

            return Response::success(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function uploadBukti($id, PenyaluranDepositoRequest $request)
    {
        try {
            $penyaluran = PenyaluranDeposito::where('id_penyaluran_deposito', $id)->firstOrFail();
            
            if ($request->hasFile('bukti_pengembalian')) {
                // Delete old file if exists
                if ($penyaluran->bukti_pengembalian && Storage::disk('public')->exists($penyaluran->bukti_pengembalian)) {
                    Storage::disk('public')->delete($penyaluran->bukti_pengembalian);
                }
                
                // Store new file
                $file = Storage::disk('public')->put('bukti_pengembalian', $request->file('bukti_pengembalian'));
                $penyaluran->update(['bukti_pengembalian' => $file]);
            }

            return Response::success(null, 'Bukti pengembalian berhasil diupload');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }
}

