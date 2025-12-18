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
            
            // Auto-update AR Perbulan menggunakan Service
            $peminjaman = PeminjamanFinlog::find($pengembalian->id_pinjaman_finlog);
            if ($peminjaman) {
                app(\App\Services\ArPerbulanFinlogService::class)->updateAROnPengembalian(
                    $pengembalian->id_pinjaman_finlog,
                    now()
                );
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
}

