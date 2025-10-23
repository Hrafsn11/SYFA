<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeminjamanFactoring;
use App\Models\FactoringDetail;
use Illuminate\Support\Facades\DB;

class PeminjamanFactoringController extends Controller
{
    public function index()
    {
        $data = PeminjamanFactoring::with('details')->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_debitur' => 'required|integer',
            'nama_bank' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_rekening' => 'nullable|string',
            'total_nominal_yang_dialihkan' => 'required|numeric',
            'harapan_tanggal_pencairan' => 'nullable|date',
            'total_bagi_hasil' => 'required|numeric',
            'rencana_tgl_pembayaran' => 'nullable|date',
            'pembayaran_total' => 'required|numeric',
            'catatan_lainnya' => 'nullable|string',
            'status' => 'nullable|string',
            'details' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            if (empty($validated['status'])) $validated['status'] = 'submitted';
            $header = PeminjamanFactoring::create($validated);

            $details = $request->input('details', []);
            foreach ($details as $i => $detail) {
                $detailData = [
                    'id_factoring' => $header->id_factoring,
                    'no_kontrak' => $detail['no_kontrak'] ?? null,
                    'nama_client' => $detail['nama_client'] ?? null,
                    'nilai_invoice' => $detail['nilai_invoice'] ?? 0,
                    'nilai_pinjaman' => $detail['nilai_pinjaman'] ?? 0,
                    'nilai_bagi_hasil' => $detail['nilai_bagi_hasil'] ?? 0,
                    'kontrak_date' => $detail['kontrak_date'] ?? null,
                    'due_date' => $detail['due_date'] ?? null,
                ];

                foreach (['dokumen_invoice','dokumen_so','dokumen_bast','dokumen_kontrak'] as $fileKey) {
                    if ($request->hasFile("details.$i.$fileKey")) {
                        $file = $request->file("details.$i.$fileKey");
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $detailData[$fileKey] = $file->store('peminjaman/factoring', 'public');
                        }
                    }
                }

                FactoringDetail::create($detailData);
            }

            DB::commit();
            return response()->json(['success' => true, 'id' => $header->id_factoring]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $header = PeminjamanFactoring::with('details')->findOrFail($id);
        return response()->json($header);
    }

    public function update(Request $request, $id)
    {
        $header = PeminjamanFactoring::findOrFail($id);
        $validated = $request->validate([
            'id_debitur' => 'required|integer',
            'nama_bank' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_rekening' => 'nullable|string',
            'total_nominal_yang_dialihkan' => 'required|numeric',
            'harapan_tanggal_pencairan' => 'nullable|date',
            'total_bagi_hasil' => 'required|numeric',
            'rencana_tgl_pembayaran' => 'nullable|date',
            'pembayaran_total' => 'required|numeric',
            'catatan_lainnya' => 'nullable|string',
            'status' => 'nullable|string',
            'details' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            if (empty($validated['status'])) $validated['status'] = 'submitted';
            $header->update($validated);

            // delete old details then re-insert
            $header->details()->delete();

            $details = $request->input('details', []);
            foreach ($details as $i => $detail) {
                $detailData = [
                    'id_factoring' => $header->id_factoring,
                    'no_kontrak' => $detail['no_kontrak'] ?? null,
                    'nama_client' => $detail['nama_client'] ?? null,
                    'nilai_invoice' => $detail['nilai_invoice'] ?? 0,
                    'nilai_pinjaman' => $detail['nilai_pinjaman'] ?? 0,
                    'nilai_bagi_hasil' => $detail['nilai_bagi_hasil'] ?? 0,
                    'kontrak_date' => $detail['kontrak_date'] ?? null,
                    'due_date' => $detail['due_date'] ?? null,
                ];

                foreach (['dokumen_invoice','dokumen_so','dokumen_bast','dokumen_kontrak'] as $fileKey) {
                    if ($request->hasFile("details.$i.$fileKey")) {
                        $file = $request->file("details.$i.$fileKey");
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $detailData[$fileKey] = $file->store('peminjaman/factoring', 'public');
                        }
                    }
                }

                FactoringDetail::create($detailData);
            }

            DB::commit();
            return response()->json(['success' => true, 'id' => $header->id_factoring]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $header = PeminjamanFactoring::findOrFail($id);
        $header->details()->delete();
        $header->delete();
        return response()->json(['success' => true]);
    }
}
