<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\ListNotifSFinance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\PengembalianInvestasi;
use App\Models\PengajuanInvestasi;
use App\Http\Requests\PengembalianInvestasiRequest;

class PengembalianInvestasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:pengembalian_investasi.add')->only(['store']);

        $this->middleware('can:pengembalian_investasi.edit')->only(['edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('livewire.pengembalian-investasi.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PengembalianInvestasiRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $file = null;
            if ($request->bukti_transfer) {
                $file = Storage::disk('public')->put('bukti_pengembalian_investasi', $request->bukti_transfer);
            }
            $validated['bukti_transfer'] = $file;

            // Create pengembalian
            $pengembalian = PengembalianInvestasi::create($validated);


            $investasi = PengajuanInvestasi::findOrFail($validated['id_pengajuan_investasi']);

            $sisaPokokBaru = $investasi->sisa_pokok - $validated['dana_pokok_dibayar'];
            $sisaBunaBaru = $investasi->sisa_bunga - $validated['bunga_dibayar'];

            $updateData = [
                'sisa_pokok' => max(0, $sisaPokokBaru),
                'sisa_bunga' => max(0, $sisaBunaBaru),
            ];

            // Auto-update status jika lunas
            if ($sisaPokokBaru <= 0 && $sisaBunaBaru <= 0) {
                $updateData['status'] = 'Lunas';
            }

            $investasi->update($updateData);

            // Reload pengembalian dengan relasi untuk notifikasi
            $pengembalian->load('pengajuanInvestasi.investor');

            DB::commit();

            // Kirim notifikasi saat SKI Finance melakukan transfer pengembalian investasi ke investor
            // Hanya jika ada bukti transfer (berarti sudah ditransfer)
            if ($pengembalian->bukti_transfer) {
                ListNotifSFinance::transferPengembalianInvestasiKeInvestor($pengembalian);
            }

            return Response::success(null, 'Data pengembalian investasi berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $pengembalian = PengembalianInvestasi::where('id_pengembalian_investasi', $id)
                ->select('id_pengembalian_investasi', 'id_pengajuan_investasi', 'dana_pokok_dibayar', 'bunga_dibayar', 'tanggal_pengembalian', 'bukti_transfer')
                ->firstOrFail();

            $result = [
                'id' => $pengembalian->id_pengembalian_investasi,
                'id_pengajuan_investasi' => $pengembalian->id_pengajuan_investasi,
                'dana_pokok_dibayar' => $pengembalian->dana_pokok_dibayar,
                'bunga_dibayar' => $pengembalian->bunga_dibayar,
                'tanggal_pengembalian' => $pengembalian->tanggal_pengembalian,
                'bukti_transfer' => $pengembalian->bukti_transfer,
            ];

            return Response::success($result, 'Data berhasil ditemukan');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PengembalianInvestasiRequest $request, $id)
    {
        $pengembalian = PengembalianInvestasi::where('id_pengembalian_investasi', $id)->firstOrFail();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $file = $pengembalian->bukti_transfer;
            if ($request->bukti_transfer) {
                // Delete old file if exists
                if ($file && Storage::disk('public')->exists($pengembalian->bukti_transfer)) {
                    Storage::disk('public')->delete($pengembalian->bukti_transfer);
                }

                $file = Storage::disk('public')->put('bukti_pengembalian_investasi', $request->bukti_transfer);
                $validated['bukti_transfer'] = $file;
            }

            // Recalculate sisa (restore old then subtract new)
            $investasi = PengajuanInvestasi::findOrFail($pengembalian->id_pengajuan_investasi);

            // Restore sisa from old pengembalian
            $sisaPokokRestored = $investasi->sisa_pokok + $pengembalian->dana_pokok_dibayar;
            $sisaBunaRestored = $investasi->sisa_bunga + $pengembalian->bunga_dibayar;

            // Subtract new pengembalian
            $sisaPokokBaru = $sisaPokokRestored - $validated['dana_pokok_dibayar'];
            $sisaBunaBaru = $sisaBunaRestored - $validated['bunga_dibayar'];

            $investasi->update([
                'sisa_pokok' => max(0, $sisaPokokBaru),
                'sisa_bunga' => max(0, $sisaBunaBaru),
                'status' => ($sisaPokokBaru <= 0 && $sisaBunaBaru <= 0) ? 'Lunas' : $investasi->status
            ]);

            // Update pengembalian
            $pengembalian->update($validated);

            DB::commit();
            return Response::success(null, 'Data pengembalian investasi berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pengembalian = PengembalianInvestasi::where('id_pengembalian_investasi', $id)->firstOrFail();

            // Restore sisa to investasi
            $investasi = PengajuanInvestasi::findOrFail($pengembalian->id_pengajuan_investasi);
            $investasi->update([
                'sisa_pokok' => $investasi->sisa_pokok + $pengembalian->dana_pokok_dibayar,
                'sisa_bunga' => $investasi->sisa_bunga + $pengembalian->bunga_dibayar,
            ]);

            // Delete file
            if ($pengembalian->bukti_transfer && Storage::disk('public')->exists($pengembalian->bukti_transfer)) {
                Storage::disk('public')->delete($pengembalian->bukti_transfer);
            }

            // Delete record
            $pengembalian->delete();

            DB::commit();

            return Response::success(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }
}
