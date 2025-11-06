<?php

namespace App\Http\Controllers;

use App\Models\FormKerjaInvestor;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FormKerjaInvestorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $investor = null;
        
        if (Auth::check()) {
            $investor = MasterDebiturDanInvestor::where('user_id', Auth::id())
                ->where('flagging', 'ya')
                ->first();
        }
        
        return view('livewire.pengajuan-investasi.index', compact('investor'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_investor' => 'required|string|max:255',
            'deposito' => 'required|in:reguler,khusus',
            'tanggal_pembayaran' => 'nullable|date',
            'lama_investasi' => 'nullable|integer|min:1',
            'jumlah_investasi' => 'required|numeric|min:0',
            'bagi_hasil' => 'required|numeric|min:0|max:100',
            'bagi_hasil_keseluruhan' => 'required|numeric|min:0',
        ]);

        if ($validated['deposito'] === 'khusus' && $validated['bagi_hasil'] < 7) {
            return response()->json([
                'success' => false,
                'message' => 'Bagi hasil minimum untuk deposito khusus adalah 7%',
                'errors' => [
                    'bagi_hasil' => ['Bagi hasil minimum untuk deposito khusus adalah 7%']
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $investor = MasterDebiturDanInvestor::where('user_id', Auth::id())
                ->where('flagging', 'ya')
                ->firstOrFail();

            $bagiHasilKeseluruhan = $this->calculateBagiHasil(
                $validated['jumlah_investasi'],
                $validated['bagi_hasil'],
                $validated['lama_investasi']
            );

            // Create form kerja investor
            $formKerjaInvestor = FormKerjaInvestor::create([
                'id_debitur' => $investor->id_debitur,
                'nama_investor' => $validated['nama_investor'],
                'deposito' => $validated['deposito'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'lama_investasi' => $validated['lama_investasi'],
                'jumlah_investasi' => $validated['jumlah_investasi'],
                'bagi_hasil' => $validated['bagi_hasil'],
                'bagi_hasil_keseluruhan' => $bagiHasilKeseluruhan,
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan investasi berhasil disimpan',
                'data' => $formKerjaInvestor
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate Bagi Hasil Keseluruhan
     * (Jumlah Investasi × Bagi Hasil%) / 12 × Lama Investasi
     */
    private function calculateBagiHasil($jumlahInvestasi, $bagiHasilPersen, $lamaInvestasi)
    {
        if ($jumlahInvestasi <= 0 || $bagiHasilPersen <= 0 || $lamaInvestasi <= 0) {
            return 0;
        }

        return round(($jumlahInvestasi * $bagiHasilPersen / 100) / 12 * $lamaInvestasi);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $formKerjaInvestor = FormKerjaInvestor::with('investor')
            ->findOrFail($id);

        return view('livewire.pengajuan-investasi.detail', compact('formKerjaInvestor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $formKerjaInvestor = FormKerjaInvestor::with('investor')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $formKerjaInvestor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $formKerjaInvestor = FormKerjaInvestor::findOrFail($id);

        $validated = $request->validate([
            'nama_investor' => 'sometimes|string|max:255',
            'deposito' => 'sometimes|in:reguler,khusus',
            'tanggal_pembayaran' => 'nullable|date',
            'lama_investasi' => 'nullable|integer|min:1',
            'jumlah_investasi' => 'sometimes|numeric|min:0',
            'bagi_hasil' => 'sometimes|numeric|min:0|max:100',
            'bagi_hasil_keseluruhan' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:pending,approved,rejected,completed',
            'alasan_penolakan' => 'nullable|string',
        ]);

        $deposito = $validated['deposito'] ?? $formKerjaInvestor->deposito;
        $bagiHasil = $validated['bagi_hasil'] ?? $formKerjaInvestor->bagi_hasil;
        
        if ($deposito === 'khusus' && $bagiHasil < 7) {
            return response()->json([
                'success' => false,
                'message' => 'Bagi hasil minimum untuk deposito khusus adalah 7%',
                'errors' => [
                    'bagi_hasil' => ['Bagi hasil minimum untuk deposito khusus adalah 7%']
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            if (isset($validated['jumlah_investasi']) || isset($validated['bagi_hasil']) || isset($validated['lama_investasi'])) {
                $validated['bagi_hasil_keseluruhan'] = $this->calculateBagiHasil(
                    $validated['jumlah_investasi'] ?? $formKerjaInvestor->jumlah_investasi,
                    $validated['bagi_hasil'] ?? $formKerjaInvestor->bagi_hasil,
                    $validated['lama_investasi'] ?? $formKerjaInvestor->lama_investasi
                );
            }
            
            $formKerjaInvestor->update($validated);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data' => $formKerjaInvestor
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $formKerjaInvestor = FormKerjaInvestor::findOrFail($id);

            // Delete bukti transfer if exists
            if ($formKerjaInvestor->bukti_transfer) {
                Storage::disk('public')->delete($formKerjaInvestor->bukti_transfer);
            }

            $formKerjaInvestor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status (approve/reject)
     */
    public function updateStatus(Request $request, $id)
    {
        $formKerjaInvestor = FormKerjaInvestor::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'alasan_penolakan' => 'required_if:status,rejected|nullable|string',
        ]);

        try {
            $formKerjaInvestor->update([
                'status' => $validated['status'],
                'alasan_penolakan' => $validated['alasan_penolakan'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'approved' 
                    ? 'Pengajuan berhasil disetujui' 
                    : 'Pengajuan ditolak',
                'data' => $formKerjaInvestor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload bukti transfer
     */
    public function uploadBuktiTransfer(Request $request, $id)
    {
        $formKerjaInvestor = FormKerjaInvestor::findOrFail($id);

        $validated = $request->validate([
            'bukti_transfer' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan_bukti' => 'nullable|string',
        ]);

        try {
            if ($request->hasFile('bukti_transfer')) {
                // Delete old file if exists
                if ($formKerjaInvestor->bukti_transfer) {
                    Storage::disk('public')->delete($formKerjaInvestor->bukti_transfer);
                }

                $file = $request->file('bukti_transfer');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('bukti_transfer', $filename, 'public');

                $formKerjaInvestor->update([
                    'bukti_transfer' => $path,
                    'keterangan_bukti' => $validated['keterangan_bukti'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bukti transfer berhasil diupload',
                'data' => $formKerjaInvestor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate kontrak
     */
    public function generateKontrak(Request $request, $id)
    {
        $formKerjaInvestor = FormKerjaInvestor::findOrFail($id);

        $validated = $request->validate([
            'nomor_kontrak' => 'required|string|max:255',
            'tanggal_kontrak' => 'required|date',
            'catatan_kontrak' => 'nullable|string',
        ]);

        try {
            $formKerjaInvestor->update([
                'nomor_kontrak' => $validated['nomor_kontrak'],
                'tanggal_kontrak' => $validated['tanggal_kontrak'],
                'catatan_kontrak' => $validated['catatan_kontrak'] ?? null,
                'status' => 'completed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kontrak berhasil digenerate',
                'data' => $formKerjaInvestor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
