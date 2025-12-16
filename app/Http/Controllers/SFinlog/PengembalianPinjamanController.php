<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengembalianPinjamanFinlog;
use App\Models\PeminjamanFinlog;
use App\Http\Requests\SFinlog\PengembalianPinjamanFinlogRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PengembalianPinjamanController extends Controller
{
    public function store(PengembalianPinjamanFinlogRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            
            // Handle file upload jika ada
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = 'pengembalian_' . time() . '_' . uniqid() . '.' . $file->extension();
                $path = $file->storeAs('pengembalian_finlog', $filename, 'public');
                $validated['bukti_pembayaran'] = $path;
            }
            
            $pengembalian = PengembalianPinjamanFinlog::create($validated);
            
            DB::commit();
            
            // Auto-update AR Perbulan
            $peminjaman = PeminjamanFinlog::find($pengembalian->id_pinjaman_finlog);
            if ($peminjaman) {
                $this->autoUpdateARPerbulan($peminjaman->id_debitur, $pengembalian->created_at);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Pengembalian pinjaman berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-update AR Perbulan saat ada pengembalian
     */
    private function autoUpdateARPerbulan(string $id_debitur, $date): void
    {
        try {
            $bulan = \Carbon\Carbon::parse($date)->format('Y-m');
            
            // Call ArPerbulanController untuk update AR
            $arController = new \App\Http\Controllers\SFinlog\ArPerbulanController();
            $request = new \Illuminate\Http\Request([
                'id_debitur' => $id_debitur,
                'bulan' => $bulan,
            ]);
            
            $arController->updateAR($request);
            
            \Log::info('AR Perbulan auto-updated from PengembalianPinjamanController', [
                'id_debitur' => $id_debitur,
                'bulan' => $bulan,
            ]);
        } catch (\Exception $e) {
            // Log error tapi tidak throw exception agar tidak mengganggu flow utama
            \Log::error('Failed to auto-update AR Perbulan from pengembalian', [
                'id_debitur' => $id_debitur,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

