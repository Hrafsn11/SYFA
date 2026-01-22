<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebiturPiutangFinlogController extends Controller
{
    /**
     * Update debitur piutang finlog data
     */
    public function update(Request $request): JsonResponse
    {
        // Check permission
        if (!auth()->user()->can('debitur_piutang_finlog.edit')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengedit data ini',
            ], 403);
        }

        $validated = $request->validate([
            'id_peminjaman' => 'required|string',
            'id_pengembalian' => 'nullable|string',
            'nilai_pinjaman' => 'required|numeric|min:0',
            'presentase_bagi_hasil' => 'required|numeric|min:0|max:100',
            'nilai_bagi_hasil' => 'required|numeric|min:0',
            'sisa_pinjaman' => 'required|numeric|min:0',
            'sisa_bagi_hasil' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update PeminjamanFinlog
            $peminjaman = PeminjamanFinlog::where('id_peminjaman_finlog', $validated['id_peminjaman'])->first();

            if (!$peminjaman) {
                throw new \Exception('Data peminjaman tidak ditemukan');
            }

            // Calculate total_pinjaman = nilai_pinjaman + nilai_bagi_hasil
            $totalPinjaman = $validated['nilai_pinjaman'] + $validated['nilai_bagi_hasil'];

            $peminjaman->update([
                'nilai_pinjaman' => $validated['nilai_pinjaman'],
                'presentase_bagi_hasil' => $validated['presentase_bagi_hasil'],
                'nilai_bagi_hasil' => $validated['nilai_bagi_hasil'],
                'total_pinjaman' => $totalPinjaman,
            ]);

            // 2. Update PengembalianPinjamanFinlog (latest) if exists
            if (!empty($validated['id_pengembalian'])) {
                $pengembalian = PengembalianPinjamanFinlog::where('id_pengembalian_pinjaman_finlog', $validated['id_pengembalian'])->first();
                
                if ($pengembalian) {
                    $totalSisa = $validated['sisa_pinjaman'] + $validated['sisa_bagi_hasil'];
                    
                    // Determine status
                    $status = $totalSisa <= 0 ? 'Lunas' : $pengembalian->status;

                    $pengembalian->update([
                        'sisa_pinjaman' => $validated['sisa_pinjaman'],
                        'sisa_bagi_hasil' => $validated['sisa_bagi_hasil'],
                        'total_sisa_pinjaman' => $totalSisa,
                        'status' => $status,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
