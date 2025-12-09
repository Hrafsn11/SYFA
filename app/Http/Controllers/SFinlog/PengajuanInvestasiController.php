<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\HistoryStatusPengajuanInvestasiFinlog;
use App\Models\MasterDebiturDanInvestor;
use App\Models\CellsProject;
use App\Http\Requests\SFinlog\PengajuanInvestasiFinlogRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanInvestasiController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(PengajuanInvestasiFinlogRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $tanggalInvestasi = Carbon::parse($validated['tanggal_investasi']);
            $tanggalBerakhir = $tanggalInvestasi->copy()->addMonths($validated['lama_investasi']);

            $nominalBagiHasil = ($validated['nominal_investasi'] * $validated['persentase_bagi_hasil'] / 100) * ($validated['lama_investasi'] / 12);

            $payload = array_merge($validated, [
                'tanggal_berakhir_investasi' => $tanggalBerakhir,
                'nominal_bagi_hasil_yang_didapat' => $nominalBagiHasil,
                'status' => 'Menunggu Validasi Finance SKI',
                'current_step' => 2,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $pengajuan = PengajuanInvestasiFinlog::create($payload);

            HistoryStatusPengajuanInvestasiFinlog::create([
                'id_pengajuan_investasi_finlog' => $pengajuan->id_pengajuan_investasi_finlog,
                'status' => 'Menunggu Validasi Finance SKI',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 2,
                'submit_step1_by' => Auth::id(),
            ]);

            DB::commit();

            return Response::success($pengajuan, 'Pengajuan investasi berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal membuat pengajuan investasi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $pengajuan = PengajuanInvestasiFinlog::with([
                'investor', 
                'project', 
                'histories' => function($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'histories.submitBy',
                'histories.approvedBy',
                'histories.rejectedBy'
            ])->findOrFail($id);
            
            return Response::success($pengajuan, 'Data pengajuan investasi berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $pengajuan = PengajuanInvestasiFinlog::with(['investor', 'project'])->findOrFail($id);
            return Response::success($pengajuan, 'Data pengajuan investasi berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PengajuanInvestasiFinlogRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);
            
            if ($pengajuan->status !== 'Draft') {
                return Response::error('Pengajuan tidak dapat diubah setelah disubmit');
            }

            $validated = $request->validated();

            $tanggalInvestasi = Carbon::parse($validated['tanggal_investasi']);
            $tanggalBerakhir = $tanggalInvestasi->copy()->addMonths($validated['lama_investasi']);

            $nominalBagiHasil = ($validated['nominal_investasi'] * $validated['persentase_bagi_hasil'] / 100) * ($validated['lama_investasi'] / 12);

            $payload = array_merge($validated, [
                'tanggal_berakhir_investasi' => $tanggalBerakhir,
                'nominal_bagi_hasil_yang_didapat' => $nominalBagiHasil,
                'updated_by' => Auth::id(),
            ]);

            $pengajuan->update($payload);

            DB::commit();

            return Response::success($pengajuan, 'Pengajuan investasi berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal memperbarui pengajuan investasi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);
            
            if ($pengajuan->status !== 'Draft') {
                return Response::error('Pengajuan hanya dapat dihapus jika masih berstatus Draft');
            }

            $pengajuan->delete();

            DB::commit();

            return Response::success(null, 'Pengajuan investasi berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal menghapus pengajuan investasi');
        }
    }

    /**
     * Submit pengajuan (from Draft to Submitted - Step 1)
     * Note: This is now automatic on creation, kept for backward compatibility
     */
    public function submit($id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);
            
            if ($pengajuan->status !== 'Draft') {
                return Response::error('Pengajuan sudah disubmit sebelumnya');
            }

            $pengajuan->update([
                'status' => 'Menunggu Validasi Finance SKI',
                'current_step' => 2,
                'updated_by' => Auth::id(),
            ]);

            HistoryStatusPengajuanInvestasiFinlog::create([
                'id_pengajuan_investasi_finlog' => $pengajuan->id_pengajuan_investasi_finlog,
                'status' => 'Menunggu Validasi Finance SKI',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 2,
                'submit_step1_by' => Auth::id(),
            ]);

            DB::commit();

            return Response::success($pengajuan, 'Pengajuan berhasil disubmit!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal submit pengajuan');
        }
    }

    /**
     * Validasi Finance SKI (Step 2)
     */
    public function validasiFinanceSKI(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);
            
            if ($pengajuan->current_step !== 2) {
                return Response::error('Pengajuan tidak dalam tahap validasi Finance SKI');
            }

            $request->validate([
                'validasi_pengajuan' => 'required|in:disetujui,ditolak',
                'catatan_penolakan' => 'required_if:validasi_pengajuan,ditolak',
                'tanggal_investasi' => 'nullable|date',
            ], [
                'validasi_pengajuan.required' => 'Status validasi harus dipilih',
                'validasi_pengajuan.in' => 'Status validasi tidak valid',
                'catatan_penolakan.required_if' => 'Catatan penolakan wajib diisi jika ditolak',
                'tanggal_investasi.date' => 'Format tanggal investasi tidak valid',
            ]);

            $validasiPengajuan = $request->validasi_pengajuan;

            if ($validasiPengajuan === 'disetujui') {
                $updateData = [
                    'status' => 'Menunggu Persetujuan CEO Finlog',
                    'current_step' => 3,
                    'updated_by' => Auth::id(),
                ];

                if ($request->filled('tanggal_investasi')) {
                    $tanggalInvestasi = Carbon::parse($request->tanggal_investasi);
                    $tanggalBerakhir = $tanggalInvestasi->copy()->addMonths($pengajuan->lama_investasi);
                    
                    $updateData['tanggal_investasi'] = $tanggalInvestasi;
                    $updateData['tanggal_berakhir_investasi'] = $tanggalBerakhir;
                }

                $pengajuan->update($updateData);

                $historyStatus = 'Menunggu Persetujuan CEO Finlog';
                $currentStep = 3;
            } else {
                $pengajuan->update([
                    'status' => 'Ditolak Finance SKI',
                    'current_step' => 2,
                    'updated_by' => Auth::id(),
                ]);

                $historyStatus = 'Ditolak Finance SKI';
                $currentStep = 2;
            }

            HistoryStatusPengajuanInvestasiFinlog::create([
                'id_pengajuan_investasi_finlog' => $pengajuan->id_pengajuan_investasi_finlog,
                'status' => $historyStatus,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => $currentStep,
                'validasi_pengajuan' => $validasiPengajuan,
                'catatan_penolakan' => $request->catatan_penolakan,
                'approve_by' => $validasiPengajuan === 'disetujui' ? Auth::id() : null,
                'reject_by' => $validasiPengajuan === 'ditolak' ? Auth::id() : null,
            ]);

            DB::commit();

            $message = $validasiPengajuan === 'disetujui' 
                ? 'Pengajuan berhasil divalidasi! Menunggu persetujuan CEO Finlog.' 
                : 'Pengajuan telah ditolak';

            return Response::success($pengajuan, $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal memvalidasi pengajuan');
        }
    }

    /**
     * Validasi CEO Finlog (Step 3)
     */
    public function validasiCEO(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);
            
            if ($pengajuan->current_step !== 3) {
                return Response::error('Pengajuan tidak dalam tahap validasi CEO Finlog');
            }

            $request->validate([
                'persetujuan_ceo_finlog' => 'required|in:disetujui,ditolak',
                'catatan_penolakan' => 'required_if:persetujuan_ceo_finlog,ditolak',
            ], [
                'persetujuan_ceo_finlog.required' => 'Status persetujuan harus dipilih',
                'persetujuan_ceo_finlog.in' => 'Status persetujuan tidak valid',
                'catatan_penolakan.required_if' => 'Catatan penolakan wajib diisi jika ditolak',
            ]);

            $persetujuan = $request->persetujuan_ceo_finlog;

            if ($persetujuan === 'disetujui') {
                $pengajuan->update([
                    'status' => 'Menunggu Informasi Rekening',
                    'current_step' => 4,
                    'updated_by' => Auth::id(),
                ]);

                $historyStatus = 'Disetujui CEO Finlog';
                $currentStep = 4;
            } else {
                $pengajuan->update([
                    'status' => 'Ditolak CEO Finlog',
                    'current_step' => 3,
                    'updated_by' => Auth::id(),
                ]);

                $historyStatus = 'Ditolak CEO Finlog';
                $currentStep = 3;
            }

            HistoryStatusPengajuanInvestasiFinlog::create([
                'id_pengajuan_investasi_finlog' => $pengajuan->id_pengajuan_investasi_finlog,
                'status' => $historyStatus,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => $currentStep,
                'persetujuan_ceo_finlog' => $persetujuan,
                'catatan_penolakan' => $request->catatan_penolakan,
                'approve_by' => $persetujuan === 'disetujui' ? Auth::id() : null,
                'reject_by' => $persetujuan === 'ditolak' ? Auth::id() : null,
            ]);

            DB::commit();

            $message = $persetujuan === 'disetujui' 
                ? 'Pengajuan berhasil disetujui CEO Finlog!' 
                : 'Pengajuan telah ditolak oleh CEO Finlog';

            return Response::success($pengajuan, $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal memproses persetujuan CEO');
        }
    }

    /**
     * Informasi Rekening ke Investor (Step 4)
     */
    public function informasiRekening(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);
            
            if ($pengajuan->current_step !== 4) {
                return Response::error('Pengajuan tidak dalam tahap informasi rekening');
            }

            $pengajuan->update([
                'status' => 'Menunggu Upload Bukti Transfer',
                'current_step' => 5,
                'updated_by' => Auth::id(),
            ]);

            HistoryStatusPengajuanInvestasiFinlog::create([
                'id_pengajuan_investasi_finlog' => $pengajuan->id_pengajuan_investasi_finlog,
                'status' => 'Informasi Rekening Terkirim',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 5,
                'approve_by' => Auth::id(),
            ]);

            DB::commit();

            return Response::success($pengajuan, 'Informasi rekening berhasil dikirim ke investor!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal mengirim informasi rekening');
        }
    }

    /**
     * Upload Bukti Transfer (Step 5)
     */
    public function uploadBuktiTransfer(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);
            
            if ($pengajuan->current_step !== 5) {
                return Response::error('Pengajuan tidak dalam tahap upload bukti transfer');
            }

            $request->validate([
                'upload_bukti_transfer' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ], [
                'upload_bukti_transfer.required' => 'File bukti transfer wajib diupload',
                'upload_bukti_transfer.file' => 'File tidak valid',
                'upload_bukti_transfer.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG',
                'upload_bukti_transfer.max' => 'Ukuran file maksimal 2MB',
            ]);

            $file = $request->file('upload_bukti_transfer');
            $fileName = 'bukti_transfer_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('pengajuan_investasi_finlog/bukti_transfer', $fileName, 'public');

            $pengajuan->update([
                'upload_bukti_transfer' => $filePath,
                'status' => 'Menunggu Generate Kontrak',
                'current_step' => 6,
                'updated_by' => Auth::id(),
            ]);

            HistoryStatusPengajuanInvestasiFinlog::create([
                'id_pengajuan_investasi_finlog' => $pengajuan->id_pengajuan_investasi_finlog,
                'status' => 'Bukti Transfer Diupload',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 6,
                'approve_by' => Auth::id(),
            ]);

            DB::commit();

            return Response::success($pengajuan, 'Bukti transfer berhasil diupload!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal upload bukti transfer');
        }
    }

    /**
     * Generate Kontrak (Step 6)
     */
    public function generateKontrak(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);
            
            if ($pengajuan->current_step !== 6) {
                return Response::error('Pengajuan tidak dalam tahap generate kontrak');
            }

            $request->validate([
                'nomor_kontrak' => 'required|string|max:255',
            ], [
                'nomor_kontrak.required' => 'Nomor kontrak wajib diisi',
                'nomor_kontrak.string' => 'Format nomor kontrak tidak valid',
                'nomor_kontrak.max' => 'Nomor kontrak maksimal 255 karakter',
            ]);

            // Update with kontrak
            $pengajuan->update([
                'nomor_kontrak' => $request->nomor_kontrak,
                'status' => 'Selesai',
                'current_step' => 6,
                'updated_by' => Auth::id(),
            ]);

            // Create history record
            HistoryStatusPengajuanInvestasiFinlog::create([
                'id_pengajuan_investasi_finlog' => $pengajuan->id_pengajuan_investasi_finlog,
                'status' => 'Selesai',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 6,
                'approve_by' => Auth::id(),
            ]);

            DB::commit();

            return Response::success($pengajuan, 'Kontrak berhasil digenerate! Proses pengajuan selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal generate kontrak');
        }
    }
}

