<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengembalianPinjamanFinlog;
use App\Http\Requests\SFinlog\PengembalianPinjamanFinlogRequest;
use Illuminate\Support\Facades\Storage;

class PengembalianPinjamanController extends Controller
{
    /**
     * Store pengembalian pinjaman (Alternative jika tidak pakai Livewire)
     */
    public function store(PengembalianPinjamanFinlogRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Handle file upload jika ada
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = 'pengembalian_' . time() . '_' . uniqid() . '.' . $file->extension();
                $path = $file->storeAs('pengembalian_finlog', $filename, 'public');
                $validated['bukti_pembayaran'] = $path;
            }
            
            PengembalianPinjamanFinlog::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Pengembalian pinjaman berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}

