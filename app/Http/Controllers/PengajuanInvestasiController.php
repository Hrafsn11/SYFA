<?php

namespace App\Http\Controllers;

use App\Models\PengajuanInvestasi;
use App\Models\HistoryStatusPengajuanInvestor;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PengajuanInvestasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get current logged in user's investor data if exists
        // flagging = 'ya' berarti Investor, 'tidak' berarti Debitur
        $investor = MasterDebiturDanInvestor::where('user_id', Auth::id())
                                            ->where('flagging', 'ya')
                                            ->first();
        
        return view('livewire.pengajuan-investasi.index', compact('investor'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get active investors (flagging = 'ya')
        $investors = MasterDebiturDanInvestor::where('flagging', 'ya')
                                             ->where('status', 'Aktif')
                                             ->get();
        
        return view('livewire.pengajuan-investasi.create', compact('investors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_debitur_dan_investor' => 'required|exists:master_debitur_dan_investor,id_debitur',
            'nama_investor' => 'required|string|max:255',
            'deposito' => 'required|in:Reguler,Khusus',
            'tanggal_investasi' => 'required|date',
            'lama_investasi' => 'required|integer|min:1',
            'jumlah_investasi' => 'required|numeric|min:0',
            'bagi_hasil_pertahun' => 'required|integer|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Calculate nominal bagi hasil
            $nominalBagiHasil = ($validated['jumlah_investasi'] * $validated['bagi_hasil_pertahun'] / 100) * ($validated['lama_investasi'] / 12);

            $pengajuan = PengajuanInvestasi::create([
                'id_debitur_dan_investor' => $validated['id_debitur_dan_investor'],
                'nama_investor' => $validated['nama_investor'],
                'deposito' => $validated['deposito'],
                'tanggal_investasi' => $validated['tanggal_investasi'],
                'lama_investasi' => $validated['lama_investasi'],
                'jumlah_investasi' => $validated['jumlah_investasi'],
                'bagi_hasil_pertahun' => $validated['bagi_hasil_pertahun'],
                'nominal_bagi_hasil_yang_didapatkan' => $nominalBagiHasil,
                'status' => 'Draft',
                'current_step' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Create initial history record
            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                'status' => 'Draft',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 1,
                'submit_step1_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan investasi berhasil dibuat!',
                'data' => $pengajuan,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pengajuan investasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pengajuan = PengajuanInvestasi::with(['investor', 'histories.submittedBy', 'histories.approvedBy', 'histories.rejectedBy'])
                                       ->findOrFail($id);
        
        $histories = $pengajuan->histories()
                              ->orderBy('id_history_status_pengajuan_investor', 'desc')
                              ->get();
        
        $latestHistory = $histories->first();

        // Prepare data array similar to peminjaman
        $investasi = [
            'id' => $pengajuan->id_pengajuan_investasi,
            'nama_investor' => $pengajuan->nama_investor,
            'nama_perusahaan' => $pengajuan->investor->nama ?? '-',
            'deposito' => $pengajuan->deposito,
            'tanggal_investasi' => $pengajuan->tanggal_investasi,
            'lama_investasi' => $pengajuan->lama_investasi,
            'jumlah_investasi' => $pengajuan->jumlah_investasi,
            'bagi_hasil_pertahun' => $pengajuan->bagi_hasil_pertahun,
            'nominal_bagi_hasil_yang_didapatkan' => $pengajuan->nominal_bagi_hasil_yang_didapatkan,
            'upload_bukti_transfer' => $pengajuan->upload_bukti_transfer,
            'status' => $pengajuan->status,
            'current_step' => $pengajuan->current_step,
        ];

        return view('livewire.pengajuan-investasi.detail', compact('investasi', 'histories', 'latestHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $pengajuan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle approval/rejection workflow
     */
    public function approval(Request $request, $id)
    {
        $pengajuan = PengajuanInvestasi::findOrFail($id);
        $status = $request->input('status');
        
        try {
            DB::beginTransaction();

            // Determine next step based on status
            // NEW: 6 steps total (removed Persetujuan Investor & Validasi Direktur)
            // Step 1: Pengajuan Investasi
            // Step 2: Validasi Bagi Hasil
            // Step 3: Validasi CEO SKI
            // Step 4: Upload Bukti Transfer
            // Step 5: Generate Kontrak
            // Step 6: Selesai
            
            // Special handling for rejection
            if ($status === 'Ditolak') {
                // If rejected at Step 2 (Validasi Bagi Hasil) -> back to Step 1 (can resubmit)
                if ($pengajuan->current_step == 2) {
                    $currentStep = 1;
                }
                // If rejected at Step 3 (Validasi CEO) -> jump to Step 6 (Selesai/Final)
                elseif ($pengajuan->current_step == 3) {
                    $currentStep = 6;
                }
                else {
                    $currentStep = $pengajuan->current_step;
                }
            } else {
                $stepMapping = [
                    'Submit Dokumen' => 2,
                    'Dokumen Tervalidasi' => 3,
                    'Disetujui oleh CEO SKI' => 4,
                    'Dana Sudah Dicairkan' => 5,
                    'Selesai' => 6,
                ];
                $currentStep = $stepMapping[$status] ?? $pengajuan->current_step;
            }

            // Update pengajuan status and step
            $pengajuan->update([
                'status' => $status,
                'current_step' => $currentStep,
                'updated_by' => Auth::id(),
            ]);

            // Handle file upload for Dana Sudah Dicairkan
            if ($status === 'Dana Sudah Dicairkan' && $request->hasFile('dokumen_transfer')) {
                $file = $request->file('dokumen_transfer');
                $path = $file->store('bukti_transfer_investasi', 'public');
                $pengajuan->update(['upload_bukti_transfer' => $path]);
            }

            // Create history record
            $historyData = [
                'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                'status' => $status,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => $currentStep,
                'submit_step1_by' => Auth::id(),
            ];

            // Add validasi_bagi_hasil from request if provided
            if ($request->has('validasi_bagi_hasil')) {
                $historyData['validasi_bagi_hasil'] = $request->input('validasi_bagi_hasil');
            }

            // Add approval/rejection data
            if (in_array($status, ['Dokumen Tervalidasi', 'Disetujui oleh CEO SKI', 'Dana Sudah Dicairkan', 'Selesai'])) {
                $historyData['approve_by'] = Auth::id();
                if (!isset($historyData['validasi_bagi_hasil'])) {
                    $historyData['validasi_bagi_hasil'] = 'disetujui';
                }
            } elseif ($status === 'Ditolak' || str_contains($status, 'Ditolak')) {
                $historyData['reject_by'] = Auth::id();
                if (!isset($historyData['validasi_bagi_hasil'])) {
                    $historyData['validasi_bagi_hasil'] = 'ditolak';
                }
                $historyData['catatan_validasi_dokumen_ditolak'] = $request->input('catatan_validasi_dokumen_ditolak', $request->input('catatan', ''));
            }

            HistoryStatusPengajuanInvestor::create($historyData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui!',
                'status' => $status,
                'current_step' => $currentStep,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get history detail for modal
     */
    public function getHistoryDetail($historyId)
    {
        try {
            $history = HistoryStatusPengajuanInvestor::with(['submittedBy', 'approvedBy', 'rejectedBy'])
                                                     ->findOrFail($historyId);

            return response()->json([
                'success' => true,
                'history' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data histori: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pengajuan = PengajuanInvestasi::findOrFail($id);

        $validated = $request->validate([
            'id_debitur_dan_investor' => 'required|exists:master_debitur_dan_investor,id_debitur',
            'nama_investor' => 'required|string|max:255',
            'deposito' => 'required|in:Reguler,Khusus',
            'tanggal_investasi' => 'required|date',
            'lama_investasi' => 'required|integer|min:1',
            'jumlah_investasi' => 'required|numeric|min:0',
            'bagi_hasil_pertahun' => 'required|integer|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Recalculate nominal bagi hasil
            $nominalBagiHasil = ($validated['jumlah_investasi'] * $validated['bagi_hasil_pertahun'] / 100) * ($validated['lama_investasi'] / 12);

            $pengajuan->update([
                'id_debitur_dan_investor' => $validated['id_debitur_dan_investor'],
                'nama_investor' => $validated['nama_investor'],
                'deposito' => $validated['deposito'],
                'tanggal_investasi' => $validated['tanggal_investasi'],
                'lama_investasi' => $validated['lama_investasi'],
                'jumlah_investasi' => $validated['jumlah_investasi'],
                'bagi_hasil_pertahun' => $validated['bagi_hasil_pertahun'],
                'nominal_bagi_hasil_yang_didapatkan' => $nominalBagiHasil,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan investasi berhasil diperbarui!',
                'data' => $pengajuan,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pengajuan investasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);

            // Delete file if exists
            if ($pengajuan->upload_bukti_transfer) {
                Storage::disk('public')->delete($pengajuan->upload_bukti_transfer);
            }

            $pengajuan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan investasi berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pengajuan investasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update status (approve/reject)
     */
    public function updateStatus(Request $request, $id)
    {
        $pengajuan = PengajuanInvestasi::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan->update([
                'status' => $validated['status'],
                'updated_by' => Auth::id(),
            ]);

            // Create history record
            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                'status' => $validated['status'],
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'submit_step1_by' => Auth::id(),
                'catatan_validasi_dokumen_ditolak' => $validated['catatan'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui!',
                'data' => $pengajuan
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload bukti transfer
     */
    public function uploadBuktiTransfer(Request $request, $id)
    {
        $pengajuan = PengajuanInvestasi::findOrFail($id);

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($pengajuan->upload_bukti_transfer) {
                    Storage::disk('public')->delete($pengajuan->upload_bukti_transfer);
                }

                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('bukti_transfer_investasi', $filename, 'public');

                $pengajuan->update([
                    'upload_bukti_transfer' => $path,
                    'updated_by' => Auth::id(),
                ]);

                // Update status to next step (Step 5: Generate Kontrak)
                $pengajuan->update([
                    'status' => 'Dana Sudah Dicairkan',
                    'current_step' => 5,
                ]);

                // Create history
                HistoryStatusPengajuanInvestor::create([
                    'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                    'status' => 'Dana Sudah Dicairkan',
                    'date' => now()->toDateString(),
                    'time' => now()->toTimeString(),
                    'current_step' => 5,
                    'submit_step1_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bukti transfer berhasil diupload',
                'data' => $pengajuan
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
     * Generate kontrak
     */
    public function generateKontrak(Request $request, $id)
    {
        $pengajuan = PengajuanInvestasi::findOrFail($id);

        $validated = $request->validate([
            'nomor_kontrak' => 'required|string|max:255',
            'tanggal_kontrak' => 'required|date',
            'catatan_kontrak' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update status to completed (Step 6: Selesai)
            $pengajuan->update([
                'status' => 'Selesai',
                'current_step' => 6,
                'updated_by' => Auth::id(),
            ]);

            // Create history
            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                'status' => 'Selesai',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 6,
                'submit_step1_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kontrak berhasil digenerate',
                'data' => $pengajuan
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
