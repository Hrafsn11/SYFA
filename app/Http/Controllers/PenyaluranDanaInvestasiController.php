<?php

namespace App\Http\Controllers;

use App\Models\PenyaluranDanaInvestasi;
use App\Models\RiwayatPengembalianDanaInvestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PenyaluranDanaInvestasiController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pengajuan_investasi' => 'required|exists:pengajuan_investasi,id_pengajuan_investasi',
            'id_debitur' => 'required|exists:master_debitur_dan_investor,id_debitur',
            'nominal_yang_disalurkan' => 'required|numeric|min:0',
            'tanggal_pengiriman_dana' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            PenyaluranDanaInvestasi::create([
                'id_pengajuan_investasi' => $request->id_pengajuan_investasi,
                'id_debitur' => $request->id_debitur,
                'nominal_yang_disalurkan' => $request->nominal_yang_disalurkan,
                'tanggal_pengiriman_dana' => $request->tanggal_pengiriman_dana,
                'tanggal_pengembalian' => $request->tanggal_pengembalian,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data penyaluran dana investasi berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating penyaluran dana investasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nominal_yang_disalurkan' => 'required|numeric|min:0',
            'tanggal_pengiriman_dana' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $penyaluran = PenyaluranDanaInvestasi::findOrFail($id);
            $penyaluran->update([
                'nominal_yang_disalurkan' => $request->nominal_yang_disalurkan,
                'tanggal_pengiriman_dana' => $request->tanggal_pengiriman_dana,
                'tanggal_pengembalian' => $request->tanggal_pengembalian,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $penyaluran = PenyaluranDanaInvestasi::findOrFail($id);

            // Delete related files if any (e.g. bukti pengembalian in history)
            foreach ($penyaluran->riwayatPengembalian as $riwayat) {
                if ($riwayat->bukti_pengembalian && Storage::disk('public')->exists($riwayat->bukti_pengembalian)) {
                    Storage::disk('public')->delete($riwayat->bukti_pengembalian);
                }
                $riwayat->delete();
            }

            $penyaluran->delete();

            DB::commit();

            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }

    /**
     * Handle update pengembalian from modal
     */
    public function updatePengembalian(Request $request)
    {
        $request->validate([
            'id_penyaluran_dana_investasi' => 'required|exists:penyaluran_dana_investasi,id_penyaluran_dana_investasi',
            'nominal_yang_disalurkan' => 'nullable|numeric|min:0',
            'nominal_yang_dikembalikan' => 'nullable|numeric|min:0',
            'tanggal_pengiriman_dana' => 'nullable|date',
            'tanggal_pengembalian' => 'nullable|date',
            'nominal_dikembalikan' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $penyaluran = PenyaluranDanaInvestasi::findOrFail($request->id_penyaluran_dana_investasi);

            // Update main record info if needed
            if ($request->has('nominal_yang_disalurkan')) {
                $penyaluran->nominal_yang_disalurkan = $request->nominal_yang_disalurkan;
            }
            if ($request->has('tanggal_pengiriman_dana')) {
                $penyaluran->tanggal_pengiriman_dana = $request->tanggal_pengiriman_dana;
            }
            $penyaluran->save();

            // Handle pengembalian logic if provided
            if ($request->nominal_dikembalikan && $request->nominal_dikembalikan > 0) {
                RiwayatPengembalianDanaInvestasi::create([
                    'id_penyaluran_dana_investasi' => $penyaluran->id_penyaluran_dana_investasi,
                    'nominal_dikembalikan' => $request->nominal_dikembalikan,
                    'tanggal_pengembalian' => $request->tanggal_pengembalian ?? now(),
                    'diinput_oleh' => auth()->id(),
                ]);

                // Recalculate total returned
                $totalDikembalikan = $penyaluran->riwayatPengembalian()->sum('nominal_dikembalikan');
                $penyaluran->update(['nominal_yang_dikembalikan' => $totalDikembalikan]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data pengembalian berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pengembalian: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data pengembalian.');
        }
    }

    public function uploadBukti(Request $request, $id)
    {
        // Implementation placeholder if needed, similar to SFinlog logic
        return redirect()->back()->with('success', 'Fitur upload bukti belum diimplementasikan di controller baru.');
    }
}
