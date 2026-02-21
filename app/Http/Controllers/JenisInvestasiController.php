<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\ListNotifSFinance;
use Illuminate\Support\Facades\DB;
use App\Models\JenisInvestasi;
use App\Models\PengajuanInvestasi;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\JenisInvestasiRequest; // Expecting rename

class JenisInvestasiController extends Controller
{
    public function __construct()
    {
        // Permissions kept as is for now
        $this->middleware('can:penyaluran_deposito.add')->only(['store']);
        $this->middleware('can:penyaluran_deposito.edit')->only(['edit', 'update']);
        $this->middleware('can:penyaluran_deposito.upload_bukti')->only(['uploadBukti']);
    }

    public function index()
    {
        return view('livewire.jenis-investasi.index'); // Expecting view rename
    }

    public function store(JenisInvestasiRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $jenisInvestasi = JenisInvestasi::create($validated);
            $jenisInvestasi->load('pengajuanInvestasi', 'debitur');

            $pengajuan = PengajuanInvestasi::findOrFail($validated['id_pengajuan_investasi']);
            $pengajuan->total_disalurkan += $validated['nominal_yang_disalurkan'];
            $pengajuan->save();

            DB::commit();

            // Kirim notifikasi saat debitur menerima dana investasi
            ListNotifSFinance::penyaluranInvestasi($jenisInvestasi); // Need to check if ListNotif handles new model

            return Response::success(null, 'Data jenis investasi berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    public function edit($id)
    {
        try {
            $jenisInvestasi = JenisInvestasi::where('id_penyaluran_deposito', $id)
                ->select('id_penyaluran_deposito', 'id_pengajuan_investasi', 'id_debitur', 'nominal_yang_disalurkan', 'tanggal_pengiriman_dana', 'tanggal_pengembalian', 'bukti_pengembalian')
                ->firstOrFail();

            $result = [
                'id' => $jenisInvestasi->id_penyaluran_deposito,
                'id_pengajuan_investasi' => $jenisInvestasi->id_pengajuan_investasi,
                'id_debitur' => $jenisInvestasi->id_debitur,
                'nominal_yang_disalurkan' => $jenisInvestasi->nominal_yang_disalurkan,
                'tanggal_pengiriman_dana' => $jenisInvestasi->tanggal_pengiriman_dana->format('Y-m-d'),
                'tanggal_pengembalian' => $jenisInvestasi->tanggal_pengembalian->format('Y-m-d'),
                'bukti_pengembalian' => $jenisInvestasi->bukti_pengembalian,
            ];

            return Response::success($result, 'Data berhasil ditemukan');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function update(JenisInvestasiRequest $request, $id)
    {
        $jenisInvestasi = JenisInvestasi::where('id_penyaluran_deposito', $id)->firstOrFail();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Store old nominal for recalculation
            $oldNominal = floatval($jenisInvestasi->nominal_yang_disalurkan);
            $newNominal = floatval($validated['nominal_yang_disalurkan']);

            // Update jenisInvestasi record
            $jenisInvestasi->update($validated);

            // If nominal changed, update parent table
            if ($oldNominal != $newNominal) {
                $pengajuan = PengajuanInvestasi::findOrFail($jenisInvestasi->id_pengajuan_investasi);

                // Recalculate: subtract old, add new
                $pengajuan->total_disalurkan = floatval($pengajuan->total_disalurkan) - $oldNominal + $newNominal;
                $pengajuan->save();

                \Log::info('JenisInvestasi Updated', [
                    'id' => $id,
                    'old_nominal' => $oldNominal,
                    'new_nominal' => $newNominal,
                    'pengajuan_id' => $pengajuan->id_pengajuan_investasi,
                    'old_total_disalurkan' => floatval($pengajuan->total_disalurkan) + $oldNominal - $newNominal,
                    'new_total_disalurkan' => $pengajuan->total_disalurkan,
                ]);
            }

            DB::commit();
            return Response::success(null, 'Data jenis investasi berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $jenisInvestasi = JenisInvestasi::where('id_penyaluran_deposito', $id)->firstOrFail();

            $pengajuan = PengajuanInvestasi::findOrFail($jenisInvestasi->id_pengajuan_investasi);
            $pengajuan->total_disalurkan -= $jenisInvestasi->nominal_yang_disalurkan;
            $pengajuan->save();

            if ($jenisInvestasi->bukti_pengembalian && Storage::disk('public')->exists($jenisInvestasi->bukti_pengembalian)) {
                Storage::disk('public')->delete($jenisInvestasi->bukti_pengembalian);
            }

            $jenisInvestasi->delete();

            DB::commit();

            return Response::success(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    public function uploadBukti($id, JenisInvestasiRequest $request)
    {
        try {
            DB::beginTransaction();

            $jenisInvestasi = JenisInvestasi::where('id_penyaluran_deposito', $id)->firstOrFail();

            $isFirstUpload = empty($jenisInvestasi->bukti_pengembalian);

            if ($request->hasFile('bukti_pengembalian')) {
                // Delete old file if exists
                if ($jenisInvestasi->bukti_pengembalian && Storage::disk('public')->exists($jenisInvestasi->bukti_pengembalian)) {
                    Storage::disk('public')->delete($jenisInvestasi->bukti_pengembalian);
                }

                // Store new file
                $file = Storage::disk('public')->put('bukti_pengembalian', $request->file('bukti_pengembalian'));
                $jenisInvestasi->update(['bukti_pengembalian' => $file]);


                if ($isFirstUpload) {
                    $pengajuan = PengajuanInvestasi::findOrFail($jenisInvestasi->id_pengajuan_investasi);
                    $pengajuan->total_kembali_dari_penyaluran += $jenisInvestasi->nominal_yang_disalurkan;
                    $pengajuan->save();

                    // Reload jenisInvestasi dengan relasi untuk notifikasi
                    $jenisInvestasi->load('debitur', 'pengajuanInvestasi');

                    // Kirim notifikasi saat debitur mengembalikan dana investasi
                    // ListNotifSFinance::pengembalianInvestasi expects the model.
                    // Assuming renaming model class name doesn't break notification if it uses duck typing or interface.
                    ListNotifSFinance::pengembalianInvestasi($jenisInvestasi);
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
