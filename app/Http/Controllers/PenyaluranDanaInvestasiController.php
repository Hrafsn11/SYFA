<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\ListNotifSFinance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PenyaluranDanaInvestasi;
use App\Models\PengajuanInvestasi;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PenyaluranDanaInvestasiRequest;

class PenyaluranDanaInvestasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:penyaluran_dana_investasi.add')->only(['store']);
        $this->middleware('can:penyaluran_dana_investasi.edit')->only(['edit', 'update']);
        $this->middleware('can:penyaluran_dana_investasi.upload_bukti')->only(['uploadBukti']);
    }

    public function index()
    {
        return view('livewire.penyaluran-dana-investasi.index');
    }

    public function store(PenyaluranDanaInvestasiRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $penyaluran = PenyaluranDanaInvestasi::create($validated);
            $penyaluran->load('pengajuanInvestasi', 'debitur');

            $pengajuan = PengajuanInvestasi::findOrFail($validated['id_pengajuan_investasi']);
            $pengajuan->total_disalurkan += $validated['nominal_yang_disalurkan'];
            $pengajuan->save();

            DB::commit();

            // Kirim notifikasi saat debitur menerima dana investasi
            ListNotifSFinance::penyaluranInvestasi($penyaluran);

            return Response::success(null, 'Data penyaluran dana investasi berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    public function edit($id)
    {
        try {
            $penyaluran = PenyaluranDanaInvestasi::where('id_penyaluran_dana_investasi', $id)
                ->select('id_penyaluran_dana_investasi', 'id_pengajuan_investasi', 'id_debitur', 'nominal_yang_disalurkan', 'tanggal_pengiriman_dana', 'tanggal_pengembalian', 'bukti_pengembalian')
                ->firstOrFail();

            $result = [
                'id' => $penyaluran->id_penyaluran_dana_investasi,
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

    public function update(PenyaluranDanaInvestasiRequest $request, $id)
    {
        $penyaluran = PenyaluranDanaInvestasi::where('id_penyaluran_dana_investasi', $id)->firstOrFail();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Store old nominal for recalculation
            $oldNominal = floatval($penyaluran->nominal_yang_disalurkan);
            $newNominal = floatval($validated['nominal_yang_disalurkan']);

            // Update penyaluran record
            $penyaluran->update($validated);

            // If nominal changed, update parent table
            if ($oldNominal != $newNominal) {
                $pengajuan = PengajuanInvestasi::findOrFail($penyaluran->id_pengajuan_investasi);

                // Recalculate: subtract old, add new
                $pengajuan->total_disalurkan = floatval($pengajuan->total_disalurkan) - $oldNominal + $newNominal;
                $pengajuan->save();

                Log::info('PenyaluranDanaInvestasi Updated', [
                    'id' => $id,
                    'old_nominal' => $oldNominal,
                    'new_nominal' => $newNominal,
                    'pengajuan_id' => $pengajuan->id_pengajuan_investasi,
                    'old_total_disalurkan' => floatval($pengajuan->total_disalurkan) + $oldNominal - $newNominal,
                    'new_total_disalurkan' => $pengajuan->total_disalurkan,
                ]);
            }

            DB::commit();
            return Response::success(null, 'Data penyaluran dana investasi berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $penyaluran = PenyaluranDanaInvestasi::where('id_penyaluran_dana_investasi', $id)->firstOrFail();

            $pengajuan = PengajuanInvestasi::findOrFail($penyaluran->id_pengajuan_investasi);
            $pengajuan->total_disalurkan -= $penyaluran->nominal_yang_disalurkan;
            $pengajuan->save();

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

    public function uploadBukti($id, PenyaluranDanaInvestasiRequest $request)
    {
        try {
            DB::beginTransaction();

            $penyaluran = PenyaluranDanaInvestasi::where('id_penyaluran_dana_investasi', $id)->firstOrFail();

            $isFirstUpload = empty($penyaluran->bukti_pengembalian);

            if ($request->hasFile('bukti_pengembalian')) {
                // Delete old file if exists
                if ($penyaluran->bukti_pengembalian && Storage::disk('public')->exists($penyaluran->bukti_pengembalian)) {
                    Storage::disk('public')->delete($penyaluran->bukti_pengembalian);
                }

                // Store new file
                $file = Storage::disk('public')->put('bukti_pengembalian', $request->file('bukti_pengembalian'));
                $penyaluran->update(['bukti_pengembalian' => $file]);

                if ($isFirstUpload) {
                    $pengajuan = PengajuanInvestasi::findOrFail($penyaluran->id_pengajuan_investasi);
                    $pengajuan->total_kembali_dari_penyaluran += $penyaluran->nominal_yang_disalurkan;
                    $pengajuan->save();

                    // Reload penyaluran dengan relasi untuk notifikasi
                    $penyaluran->load('debitur', 'pengajuanInvestasi');

                    // Kirim notifikasi saat debitur mengembalikan dana investasi
                    ListNotifSFinance::pengembalianInvestasi($penyaluran);
                }
            }

            DB::commit();

            return Response::success(null, 'Bukti pengembalian berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }
}
