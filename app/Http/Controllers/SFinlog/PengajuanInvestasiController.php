<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Helpers\ListNotifSFinlog;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\HistoryStatusPengajuanInvestasiFinlog;
use App\Http\Requests\SFinlog\PengajuanInvestasiFinlogRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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

            $history = $this->createHistory($pengajuan->id_pengajuan_investasi_finlog, [
                'status' => 'Menunggu Validasi Finance SKI',
                'current_step' => 2,
                'submit_step1_by' => Auth::id(),
            ]);

            DB::commit();

            // Reload pengajuan dengan relasi investor untuk notifikasi
            $pengajuan->refresh();
            $pengajuan->load('investor');
            ListNotifSFinlog::menuPengajuanInvestasi($history->status, $pengajuan);

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
                'histories' => function ($query) {
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

            $history = $this->createHistory($pengajuan->id_pengajuan_investasi_finlog, $historyData);

            DB::commit();

            // Reload pengajuan dengan relasi investor untuk notifikasi
            $pengajuan->refresh();
            $pengajuan->load('investor');

            // Try to send notification - don't fail the whole request if notification fails
            try {
                ListNotifSFinlog::menuPengajuanInvestasi($history->status, $pengajuan);
            } catch (\Exception $notifException) {
                // Log notification error but don't stop the process
                Log::error('Failed to send notification for pengajuan investasi', [
                    'pengajuan_id' => $pengajuan->id_pengajuan_investasi_finlog,
                    'error' => $notifException->getMessage()
                ]);
            }

            return Response::success([
                'status' => $status,
                'current_step' => $currentStep,
            ], 'Status berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log detailed error for debugging
            Log::error('Failed to update pengajuan investasi status', [
                'pengajuan_id' => $id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

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

                $history = $this->createHistory($pengajuan->id_pengajuan_investasi_finlog, [
                    'status' => 'Bukti Transfer Diupload',
                    'current_step' => 5,
                    'approve_by' => Auth::id(),
                ]);
            }

            DB::commit();

            // Reload pengajuan dengan relasi investor untuk notifikasi
            $pengajuan->refresh();
            $pengajuan->load('investor');
            if (isset($history)) {
                // Notifikasi investasi berhasil ditransfer
                ListNotifSFinlog::menuPengajuanInvestasi($history->status, $pengajuan);
            }

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
            $history = $this->createHistory($pengajuan->id_pengajuan_investasi_finlog, [
                'status' => 'Selesai',
                'current_step' => 6,
                'approve_by' => Auth::id(),
            ]);

            DB::commit();

            // Reload pengajuan dengan relasi investor untuk notifikasi
            $pengajuan->refresh();
            $pengajuan->load('investor');
            // Notifikasi kontrak dibuat (karena ada nomor_kontrak)
            ListNotifSFinlog::menuPengajuanInvestasi($history->status, $pengajuan);

            return Response::success($pengajuan, 'Kontrak berhasil digenerate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Terjadi kesalahan saat generate kontrak');
        }
    }

    /**
     * Preview Kontrak
     */

    /**
     * Preview Kontrak
     */
    public function previewKontrak(Request $request, $id)
    {
        try {
            $pengajuan = PengajuanInvestasiFinlog::with(['investor', 'project'])->findOrFail($id);
            $data = $this->prepareContractData($pengajuan, $request->input('nomor_kontrak'));

            return view('livewire.sfinlog.pengajuan-investasi.preview-kontrak', compact('data', 'pengajuan'));
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal memuat preview kontrak');
        }
    }

    /**
     * Download Kontrak PDF
     */
    public function downloadKontrakPdf($id)
    {
        try {
            $pengajuan = PengajuanInvestasiFinlog::with(['investor', 'project'])->findOrFail($id);
            $data = $this->prepareContractData($pengajuan);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.sfinlog.pengajuan-investasi.preview-kontrak-pdf', compact('data', 'pengajuan'));
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream('Kontrak-Investasi-' . $data['nomor_kontrak'] . '.pdf');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal generate PDF kontrak');
        }
    }

    /**
     * Helper: Prepare data array for contract view
     */
    private function prepareContractData($pengajuan, $nomorKontrakInput = null)
    {
        $nomorKontrak = $pengajuan->nomor_kontrak ?? ($nomorKontrakInput ?? 'DRAFT-' . date('Ymd-His'));

        $historySelesai = HistoryStatusPengajuanInvestasiFinlog::where('id_pengajuan_investasi_finlog', $pengajuan->id_pengajuan_investasi_finlog)
            ->where('status', 'Selesai')
            ->first();
        $tanggalKontrak = $historySelesai ? $historySelesai->date : now()->toDateString();

        return [
            'nomor_kontrak' => $nomorKontrak,
            'tanggal_kontrak' => $tanggalKontrak,
            'nama_investor' => $pengajuan->nama_investor,
            'nama_perusahaan' => $pengajuan->nama_investor, // Asumsi dari code lama sama
            'project' => $pengajuan->project->nama_cells_bisnis ?? '-',
            'nominal_investasi' => $pengajuan->nominal_investasi,
            'persentase_bagi_hasil' => $pengajuan->persentase_bagi_hasil,
            'lama_investasi' => $pengajuan->lama_investasi,
            'tanggal_investasi' => $pengajuan->tanggal_investasi,
            'tanggal_berakhir' => $pengajuan->tanggal_berakhir_investasi,
            'alamat' => $pengajuan->investor->alamat ?? '-',
        ];
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

    /**
     * Download Certificate for Pengajuan Investasi Finlog
     * 
     * Method ini digunakan untuk generate dan download sertifikat investasi
     * Hanya bisa diakses jika status pengajuan = "Selesai"
     * 
     * @param int $id - ID pengajuan investasi finlog
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function downloadSertifikat($id)
    {
        try {
            // 1. Ambil data pengajuan dengan relasi investor
            $pengajuan = PengajuanInvestasiFinlog::with('investor')->findOrFail($id);

            // 2. Validasi: Hanya status "Selesai" yang bisa cetak sertifikat
            if ($pengajuan->status !== 'Selesai') {
                return redirect()->back()->with(
                    'error',
                    'Sertifikat hanya tersedia untuk pengajuan yang sudah selesai'
                );
            }

            // 3. Generate Nomor Sertifikat
            // Format: DCI (Deposito Certificate Investment) + TAHUN + NOMOR URUT 4 DIGIT
            // Contoh: DCI20250001, DCI20250002, dst.
            $year = date('Y');
            $countThisYear = PengajuanInvestasiFinlog::whereYear('created_at', $year)
                ->where('status', 'Selesai')
                ->where('id_pengajuan_investasi_finlog', '<=', $pengajuan->id_pengajuan_investasi_finlog)
                ->count();

            $nomorSertifikat = 'DCI' . $year . str_pad($countThisYear, 4, '0', STR_PAD_LEFT);

            // 4. Tentukan deskripsi
            $deskripsi = 'INVESTASI DEPOSITO FINLOG';

            // 5. Hitung jangka waktu investasi
            // Dari tanggal_investasi sampai tanggal_berakhir_investasi
            $tanggalInvestasiCarbon = \Carbon\Carbon::parse($pengajuan->tanggal_investasi);
            $tanggalBerakhirCarbon = \Carbon\Carbon::parse($pengajuan->tanggal_berakhir_investasi);

            // 6. Format tanggal untuk ditampilkan (bahasa Indonesia)
            $tanggalInvestasi = $tanggalInvestasiCarbon->translatedFormat('d F Y');
            $tanggalBerakhir = $tanggalBerakhirCarbon->translatedFormat('d F Y');
            $jangkaWaktu = $tanggalInvestasi . ' - ' . $tanggalBerakhir;

            // 7. Siapkan data untuk view sertifikat
            $data = [
                // Nama deposan/investor
                'nama_deposan' => $pengajuan->nama_investor,

                // Nomor sertifikat yang sudah digenerate
                'nomor_deposito' => $nomorSertifikat,

                // Deskripsi jenis investasi
                'deskripsi' => $deskripsi,

                // Nilai deposito/investasi (formatted Rupiah tanpa desimal)
                'nilai_deposito' => 'Rp ' . number_format($pengajuan->nominal_investasi, 0, ',', '.'),

                // Kode transaksi (nomor kontrak)
                'kode_transaksi' => $pengajuan->nomor_kontrak ?? '-',

                // Jangka waktu (range tanggal)
                'jangka_waktu' => $jangkaWaktu,

                // Bagi hasil (persentase per tahun)
                'bagi_hasil' => $pengajuan->persentase_bagi_hasil . ' % P.A NET',

                // Nilai investasi dalam text (formatted Rupiah dengan 2 desimal)
                // Digunakan di halaman 2 sertifikat
                'nilai_investasi_text' => 'Rp. ' . number_format($pengajuan->nominal_investasi, 2, ',', '.'),
            ];

            // 8. Return view sertifikat (menggunakan view yang sama dengan S-Finance)
            return view('livewire.pengajuan-investasi.sertifikat', compact('data'));
        } catch (\Exception $e) {
            // Handle error dan redirect kembali dengan pesan error
            return redirect()->back()->with(
                'error',
                'Gagal menggenerate sertifikat: ' . $e->getMessage()
            );
        }
    }
}
