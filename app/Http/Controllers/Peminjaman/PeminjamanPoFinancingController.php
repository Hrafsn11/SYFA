<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeminjamanPoFinancing;
use App\Models\PoFinancing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\PeminjamanNumberService;

class PeminjamanPoFinancingController extends Controller
{
    /**
     * List all PO Financing headers (with pagination).
     */
    public function index()
    {
        $data = PeminjamanPoFinancing::with('details')->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($data);
    }

    /**
     * Store a new PO Financing header and details.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_debitur' => 'required|integer',
            'id_instansi' => 'nullable|integer',
            'no_kontrak' => 'required|string|max:255|unique:peminjaman_po_financing,no_kontrak',
            'nama_bank' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_rekening' => 'nullable|string',
            'lampiran_sid' => 'nullable|file',
            'tujuan_pembiayaan' => 'nullable|string',
            'total_pinjaman' => 'required|numeric',
            'harapan_tanggal_pencairan' => 'nullable|date',
            'total_bagi_hasil' => 'required|numeric',
            'rencana_tgl_pembayaran' => 'nullable|date',
            'pembayaran_total' => 'required|numeric',
            'catatan_lainnya' => 'nullable|string',
            'status' => 'required|string',
            'sumber_pembiayaan' => 'required|string',
            'details' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Handle lampiran SID file
            if ($request->hasFile('lampiran_sid')) {
                $lampiranSidPath = $request->file('lampiran_sid')->store('lampiran_sid', 'public');
                $validated['lampiran_sid'] = $lampiranSidPath;
            }
            $validated['created_by'] = auth()->id();
            // create header first
            $header = PeminjamanPoFinancing::create($validated);
            // generate nomor based on header id (no sequences table required)
            $header->nomor_peminjaman = (new PeminjamanNumberService())->generateFromId($header->id_po_financing, 'PO', $header->created_at?->format('Ym'));
            $header->save();

            $details = $request->input('details', []);
            foreach ($details as $i => $detail) {
                $detailData = [
                    'id_po_financing' => $header->id_po_financing,
                    'no_kontrak' => $header->no_kontrak,
                    'nama_client' => $detail['nama_client'] ?? null,
                    'nilai_invoice' => $detail['nilai_invoice'],
                    'nilai_pinjaman' => $detail['nilai_pinjaman'],
                    'nilai_bagi_hasil' => $detail['nilai_bagi_hasil'],
                    'kontrak_date' => $detail['kontrak_date'],
                    'due_date' => $detail['due_date'],
                    'created_by' => auth()->id(),
                ];

                // Handle dokumen files coming from multipart form 'details[i][dokumen_x]'
                foreach (['dokumen_kontrak','dokumen_so','dokumen_bast','dokumen_lainnya'] as $fileKey) {
                    if ($request->hasFile("details.$i.$fileKey")) {
                        $file = $request->file("details.$i.$fileKey");
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $detailData[$fileKey] = $file->store('peminjaman/po_financing', 'public');
                        }
                    }
                }

                PoFinancing::create($detailData);
            }
            DB::commit();
            return response()->json(['success' => true, 'id' => $header->id_po_financing]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show PO Financing detail.
     */
    public function show($id)
    {
        $header = PeminjamanPoFinancing::with('details')->findOrFail($id);
        return response()->json($header);
    }

    /**
     * Update PO Financing header and details.
     */
    public function update(Request $request, $id)
    {
        $header = PeminjamanPoFinancing::findOrFail($id);
        $validated = $request->validate([
            'id_debitur' => 'required|integer',
            'id_instansi' => 'nullable|integer',
            'no_kontrak' => 'required|string|max:255',
            'nama_bank' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_rekening' => 'nullable|string',
            'lampiran_sid' => 'nullable|file',
            'tujuan_pembiayaan' => 'nullable|string',
            'total_pinjaman' => 'required|numeric',
            'harapan_tanggal_pencairan' => 'nullable|date',
            'total_bagi_hasil' => 'required|numeric',
            'rencana_tgl_pembayaran' => 'nullable|date',
            'pembayaran_total' => 'required|numeric',
            'catatan_lainnya' => 'nullable|string',
            'status' => 'required|string',
            'sumber_pembiayaan' => 'required|string',
            'details' => 'required|array|min:1',
        ]);
    DB::beginTransaction();
        try {
            if ($request->hasFile('lampiran_sid')) {
                $lampiranSidPath = $request->file('lampiran_sid')->store('lampiran_sid', 'public');
                $validated['lampiran_sid'] = $lampiranSidPath;
            }
            $validated['updated_by'] = auth()->id();
            $header->update($validated);
            // Hapus semua detail lama, lalu insert ulang
            $header->details()->delete();
            $details = $request->input('details', []);
            foreach ($details as $i => $detail) {
                $detailData = [
                    'id_po_financing' => $header->id_po_financing,
                    'no_kontrak' => $header->no_kontrak,
                    'nama_client' => $detail['nama_client'] ?? null,
                    'nilai_invoice' => $detail['nilai_invoice'],
                    'nilai_pinjaman' => $detail['nilai_pinjaman'],
                    'nilai_bagi_hasil' => $detail['nilai_bagi_hasil'],
                    'kontrak_date' => $detail['kontrak_date'],
                    'due_date' => $detail['due_date'],
                    'created_by' => auth()->id(),
                ];
                foreach (['dokumen_kontrak','dokumen_so','dokumen_bast','dokumen_lainnya'] as $fileKey) {
                    if ($request->hasFile("details.$i.$fileKey")) {
                        $file = $request->file("details.$i.$fileKey");
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $detailData[$fileKey] = $file->store('peminjaman/po_financing', 'public');
                        }
                    }
                }
                \App\Models\PoFinancing::create($detailData);
            }
            DB::commit();
            return response()->json(['success' => true, 'id' => $header->id_po_financing]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete PO Financing header and details.
     */
    public function destroy($id)
    {
        $header = PeminjamanPoFinancing::findOrFail($id);
        $header->details()->delete();
        $header->delete();
        return response()->json(['success' => true]);
    }
}
