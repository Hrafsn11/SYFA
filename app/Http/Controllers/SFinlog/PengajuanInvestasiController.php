<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\HistoryStatusPengajuanInvestasiFinlog;
use App\Http\Requests\SFinlog\PengajuanInvestasiFinlogRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanInvestasiController extends Controller
{
    /**
     */
    public function __construct()
    {
        $this->middleware('can:pengajuan_investasi_finlog.view')->only(['show', 'edit', 'getHistoryDetail']);
        $this->middleware('can:pengajuan_investasi_finlog.add')->only(['store']);
        $this->middleware('can:pengajuan_investasi_finlog.edit')->only(['update']);
        $this->middleware('can:pengajuan_investasi_finlog.delete')->only(['destroy']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PengajuanInvestasiFinlogRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Calculate dates and bagi hasil
            $tanggalInvestasi = Carbon::parse($validated['tanggal_investasi']);
            $tanggalBerakhir = $tanggalInvestasi->copy()->addMonths($validated['lama_investasi']);
            $nominalBagiHasil = $this->calculateBagiHasil(
                $validated['nominal_investasi'],
                $validated['persentase_bagi_hasil'],
                $validated['lama_investasi']
            );

            $payload = array_merge($validated, [
                'tanggal_berakhir_investasi' => $tanggalBerakhir,
                'nominal_bagi_hasil_yang_didapat' => $nominalBagiHasil,
                'status' => 'Menunggu Validasi Finance SKI',
                'current_step' => 2,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $pengajuan = PengajuanInvestasiFinlog::create($payload);

            $this->createHistory($pengajuan->id_pengajuan_investasi_finlog, [
                'status' => 'Menunggu Validasi Finance SKI',
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

            // Recalculate dates and bagi hasil
            $tanggalInvestasi = Carbon::parse($validated['tanggal_investasi']);
            $tanggalBerakhir = $tanggalInvestasi->copy()->addMonths($validated['lama_investasi']);
            $nominalBagiHasil = $this->calculateBagiHasil(
                $validated['nominal_investasi'],
                $validated['persentase_bagi_hasil'],
                $validated['lama_investasi']
            );

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
     * Handle approval/rejection workflow
     */
    public function approval(PengajuanInvestasiFinlogRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);
            $status = $request->input('status');

            // Determine next step based on status
            $stepMapping = [
                'Submit Pengajuan' => ['status' => 'Menunggu Validasi Finance SKI', 'step' => 2],
                'Dokumen Tervalidasi' => ['status' => 'Menunggu Persetujuan CEO Finlog', 'step' => 3],
                'Disetujui CEO Finlog' => ['status' => 'Menunggu Upload Bukti Transfer', 'step' => 4],
                'Bukti Transfer Diupload' => ['status' => 'Menunggu Generate Kontrak', 'step' => 5],
                'Selesai' => ['status' => 'Selesai', 'step' => 6],
            ];

            // Handle rejection
            if ($status === 'Ditolak' || str_contains($status, 'Ditolak')) {
                $currentStep = $pengajuan->current_step; // Stay at current step
            } else {
                $mapping = $stepMapping[$status] ?? ['status' => $status, 'step' => $pengajuan->current_step];
                $status = $mapping['status'];
                $currentStep = $mapping['step'];
            }

            // Update pengajuan status and step
            $pengajuan->update([
                'status' => $status,
                'current_step' => $currentStep,
                'updated_by' => Auth::id(),
            ]);

            // Handle file upload for Bukti Transfer
            if ($status === 'Bukti Transfer Diupload' && $request->hasFile('dokumen_transfer')) {
                $file = $request->file('dokumen_transfer');
                $fileName = 'bukti_transfer_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('pengajuan_investasi_finlog/bukti_transfer', $fileName, 'public');
                $pengajuan->update(['upload_bukti_transfer' => $filePath]);
            }

            // Handle tanggal investasi update for Finance SKI validation
            if ($status === 'Menunggu Persetujuan CEO Finlog' && $request->filled('tanggal_investasi')) {
                $tanggalInvestasi = Carbon::parse($request->tanggal_investasi);
                $tanggalBerakhir = $tanggalInvestasi->copy()->addMonths($pengajuan->lama_investasi);
                $pengajuan->update([
                    'tanggal_investasi' => $tanggalInvestasi,
                    'tanggal_berakhir_investasi' => $tanggalBerakhir,
                ]);
            }

            // Create history record
            $historyData = [
                'status' => $status,
                'current_step' => $currentStep,
                'submit_step1_by' => Auth::id(),
            ];

            if ($request->has('validasi_pengajuan')) {
                $historyData['validasi_pengajuan'] = $request->input('validasi_pengajuan');
            }
            if ($request->has('persetujuan_ceo_finlog')) {
                $historyData['persetujuan_ceo_finlog'] = $request->input('persetujuan_ceo_finlog');
            }

            if (str_contains($status, 'Ditolak')) {
                $historyData['reject_by'] = Auth::id();
                $historyData['catatan_penolakan'] = $request->input('catatan_penolakan', $request->input('catatan', ''));
            } else {
                $historyData['approve_by'] = Auth::id();
            }

            $this->createHistory($pengajuan->id_pengajuan_investasi_finlog, $historyData);

            DB::commit();

            return Response::success([
                'status' => $status,
                'current_step' => $currentStep,
            ], 'Status berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal memperbarui status');
        }
    }

    /**
     */
    public function getHistoryDetail($historyId)
    {
        try {
            $history = HistoryStatusPengajuanInvestasiFinlog::with(['submitBy', 'approvedBy', 'rejectedBy'])
                ->findOrFail($historyId);

            return Response::success($history, 'Data histori berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data histori');
        }
    }

    /**
     * Upload Bukti Transfer
     */
    public function uploadBuktiTransfer(PengajuanInvestasiFinlogRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('pengajuan_investasi_finlog/bukti_transfer', $filename, 'public');

                $pengajuan->update([
                    'upload_bukti_transfer' => $path,
                    'updated_by' => Auth::id(),
                ]);

                // Update status using approval method pattern
                $pengajuan->update([
                    'status' => 'Menunggu Generate Kontrak',
                    'current_step' => 5,
                ]);

                $this->createHistory($pengajuan->id_pengajuan_investasi_finlog, [
                    'status' => 'Bukti Transfer Diupload',
                    'current_step' => 5,
                    'approve_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return Response::success($pengajuan, 'Bukti transfer berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Terjadi kesalahan saat upload bukti transfer');
        }
    }

    /**
     * Generate kontrak
     */
    public function generateKontrak(PengajuanInvestasiFinlogRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanInvestasiFinlog::findOrFail($id);

            // Update nomor kontrak and status to completed (Step 6: Selesai)
            $pengajuan->update([
                'nomor_kontrak' => $request->input('nomor_kontrak'),
                'status' => 'Selesai',
                'current_step' => 6,
                'updated_by' => Auth::id(),
            ]);

            // Create history for "Selesai"
            $this->createHistory($pengajuan->id_pengajuan_investasi_finlog, [
                'status' => 'Selesai',
                'current_step' => 6,
                'approve_by' => Auth::id(),
            ]);

            DB::commit();

            return Response::success($pengajuan, 'Kontrak berhasil digenerate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Terjadi kesalahan saat generate kontrak');
        }
    }

    /**
     * Preview Kontrak
     */
    public function previewKontrak(Request $request, $id)
    {
        try {
            $pengajuan = PengajuanInvestasiFinlog::with(['investor', 'project'])->findOrFail($id);
            
            $nomorKontrak = $pengajuan->nomor_kontrak ?? $request->input('nomor_kontrak', 'DRAFT-' . date('Ymd-His'));
            
            $historySelesai = HistoryStatusPengajuanInvestasiFinlog::where('id_pengajuan_investasi_finlog', $id)
                ->where('status', 'Selesai')
                ->first();
            $tanggalKontrak = $historySelesai ? $historySelesai->date : now()->toDateString();
            
            $data = [
                'nomor_kontrak' => $nomorKontrak,
                'tanggal_kontrak' => $tanggalKontrak,
                'nama_investor' => $pengajuan->nama_investor,
                'nama_perusahaan' => $pengajuan->nama_investor,
                'project' => $pengajuan->project->nama_project ?? '-',
                'nominal_investasi' => $pengajuan->nominal_investasi,
                'persentase_bagi_hasil' => $pengajuan->persentase_bagi_hasil,
                'lama_investasi' => $pengajuan->lama_investasi,
                'tanggal_investasi' => $pengajuan->tanggal_investasi,
                'tanggal_berakhir' => $pengajuan->tanggal_berakhir_investasi,
                'alamat' => $pengajuan->investor->alamat ?? '-',
            ];
            
            return view('livewire.sfinlog.pengajuan-investasi.preview-kontrak', compact('data', 'pengajuan'));
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal memuat preview kontrak');
        }
    }

    /**
     * Helper: Calculate nominal bagi hasil
     */
    private function calculateBagiHasil($nominalInvestasi, $persentaseBagiHasil, $lamaInvestasi)
    {
        return ($nominalInvestasi * $persentaseBagiHasil / 100) * ($lamaInvestasi / 12);
    }

    /**
     * Helper: Create history record
     */
    private function createHistory($pengajuanId, array $data)
    {
        $defaultData = [
            'id_pengajuan_investasi_finlog' => $pengajuanId,
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
        ];

        return HistoryStatusPengajuanInvestasiFinlog::create(array_merge($defaultData, $data));
    }
}
