<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\ListNotifSFinance;
use App\Models\PengajuanInvestasi;
use App\Models\HistoryStatusPengajuanInvestor;
use App\Models\MasterDebiturDanInvestor;
use App\Http\Requests\PengajuanInvestasiRequest;
use App\Services\KontrakInvestasiService;
use App\Services\ContractNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanInvestasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:investasi.add')->only(['create', 'store']);
        $this->middleware('can:investasi.edit')->only(['edit', 'update']);
    }

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
    public function store(PengajuanInvestasiRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Calculate nominal bunga
            $nominalBunga = ($validated['jumlah_investasi'] * $validated['bunga_pertahun'] / 100) * ($validated['lama_investasi'] / 12);

            // Prepare payload for creation. Only include nomor_kontrak if provided by user.
            $payload = array_merge($validated, [
                'nominal_bunga_yang_didapatkan' => $nominalBunga,
                'status' => 'Draft',
                'current_step' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            if (!empty($validated['nomor_kontrak'] ?? null)) {
                if (PengajuanInvestasi::where('nomor_kontrak', $validated['nomor_kontrak'])->exists()) {
                    return Response::error('Nomor kontrak sudah digunakan.');
                }

                $payload['nomor_kontrak'] = $validated['nomor_kontrak'];
            }

            $pengajuan = PengajuanInvestasi::create($payload);

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
            'alamat' => $pengajuan->investor->alamat ?? '-',
            'jenis_investasi' => $pengajuan->jenis_investasi,
            'tanggal_investasi' => $pengajuan->tanggal_investasi,
            'lama_investasi' => $pengajuan->lama_investasi,
            'jumlah_investasi' => $pengajuan->jumlah_investasi,
            'bunga_pertahun' => $pengajuan->bunga_pertahun,
            'nominal_bunga_yang_didapatkan' => $pengajuan->nominal_bunga_yang_didapatkan,
            'upload_bukti_transfer' => $pengajuan->upload_bukti_transfer,
            'nomor_kontrak' => $pengajuan->nomor_kontrak,
            'status' => $pengajuan->status,
            'current_step' => $pengajuan->current_step,
        ];

        // Generate preview nomor kontrak jika belum ada dan current_step >= 4 (sudah disetujui CEO)
        $previewNomorKontrak = null;
        $kodePerusahaanMissing = false;

        if (empty($pengajuan->nomor_kontrak) && $pengajuan->current_step >= 4) {
            // Generate preview tanpa save ke database
            if ($pengajuan->investor && !empty($pengajuan->investor->kode_perusahaan)) {
                $previewNomorKontrak = ContractNumberService::generateInvestasi(
                    $pengajuan->investor->kode_perusahaan,
                    $pengajuan->jenis_investasi,
                    $pengajuan->tanggal_investasi
                );
            } else {
                // Investor belum punya kode perusahaan
                $kodePerusahaanMissing = true;
            }
        }
        $investasi['preview_nomor_kontrak'] = $previewNomorKontrak;
        $investasi['kode_perusahaan_missing'] = $kodePerusahaanMissing;

        return view('livewire.pengajuan-investasi.detail', compact('investasi', 'histories', 'latestHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);
            return Response::success($pengajuan, 'Data pengajuan investasi berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data');
        }
    }

    /**
     * Handle approval/rejection workflow
     */
    public function approval(Request $request, $id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);
            $status = $request->input('status');

            DB::beginTransaction();

            // Determine next step based on status
            // Special handling for rejection
            if ($status === 'Ditolak') {
                // If rejected at Step 2 (Validasi Bagi Hasil) -> back to Step 1 (can resubmit)
                if ($pengajuan->current_step == 2) {
                    $currentStep = 1;
                }
                // If rejected at Step 3 (Validasi CEO) -> jump to Step 6 (Selesai/Final)
                elseif ($pengajuan->current_step == 3) {
                    $currentStep = 6;
                } else {
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
                // Add catatan if provided
                if ($request->has('catatan') && !empty($request->input('catatan'))) {
                    $historyData['catatan'] = $request->input('catatan');
                }
            } elseif ($status === 'Ditolak' || str_contains($status, 'Ditolak')) {
                $historyData['reject_by'] = Auth::id();
                if (!isset($historyData['validasi_bagi_hasil'])) {
                    $historyData['validasi_bagi_hasil'] = 'ditolak';
                }
                $historyData['catatan_validasi_dokumen_ditolak'] = $request->input('catatan_validasi_dokumen_ditolak', $request->input('catatan', ''));
            }

            HistoryStatusPengajuanInvestor::create($historyData);

            // Reload pengajuan dengan relasi
            $pengajuan->load('investor');

            DB::commit();

            // Kirim notifikasi berdasarkan status
            ListNotifSFinance::menuPengajuanInvestasi($status, $pengajuan);

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
     * Get history detail for modal
     */
    public function getHistoryDetail($historyId)
    {
        try {
            $history = HistoryStatusPengajuanInvestor::with(['submittedBy', 'approvedBy', 'rejectedBy'])
                ->findOrFail($historyId);

            return Response::success($history, 'Data histori berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data histori');
        }
    }

    /**
     * Display preview kontrak investasi deposito
     */
    public function previewKontrak(Request $request, $id, KontrakInvestasiService $kontrakService)
    {
        $pengajuan = PengajuanInvestasi::with('investor')->findOrFail($id);

        // Use nomor_kontrak from request if provided, otherwise use from database
        $nomorKontrak = $request->input('nomor_kontrak') ?? $pengajuan->nomor_kontrak;
        $kontrak = $kontrakService->generateKontrakData($pengajuan, $nomorKontrak);

        return view('livewire.pengajuan-investasi.preview-kontrak', compact('kontrak'));
    }

    /**
     * Download kontrak PDF
     */
    public function downloadKontrakPdf(Request $request, $id, KontrakInvestasiService $kontrakService)
    {
        try {
            $pengajuan = PengajuanInvestasi::with('investor')->findOrFail($id);

            // Check if nomor_kontrak exists
            if (empty($pengajuan->nomor_kontrak)) {
                Log::error('Download kontrak failed: Nomor kontrak belum di-generate', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor kontrak belum di-generate'
                ], 404);
            }

            // Generate kontrak data
            $kontrak = $kontrakService->generateKontrakData($pengajuan, $pengajuan->nomor_kontrak);

            Log::info('Kontrak data generated', ['kontrak_keys' => array_keys($kontrak)]);

            // Build custom HTML for PDF
            $html = $this->buildKontrakHTML($kontrak);

            // Generate PDF using DomPDF with config
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            // Set DomPDF options
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
                'isFontSubsettingEnabled' => true,
                'debugPng' => false,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'debugLayout' => false,
                'debugLayoutLines' => false,
                'debugLayoutBlocks' => false,
                'debugLayoutInline' => false,
                'debugLayoutPaddingBox' => false,
            ]);

            $filename = 'Kontrak_Investasi_' . str_replace('/', '_', $pengajuan->nomor_kontrak) . '_' . date('Ymd') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error download kontrak PDF: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PengajuanInvestasiRequest $request, $id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);

            DB::beginTransaction();

            $validated = $request->validated();

            // Note: nomor_kontrak uniqueness already validated in PengajuanInvestasiRequest
            // No need to check again here

            $pengajuan->update(array_merge($validated, [
                'updated_by' => Auth::id(),
            ]));

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
            $pengajuan = PengajuanInvestasi::findOrFail($id);

            // Delete file if exists
            if ($pengajuan->upload_bukti_transfer) {
                Storage::disk('public')->delete($pengajuan->upload_bukti_transfer);
            }

            $pengajuan->delete();

            return Response::success(null, 'Pengajuan investasi berhasil dihapus!');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal menghapus pengajuan investasi');
        }
    }

    /**
     * Update status (approve/reject)
     */
    public function updateStatus(PengajuanInvestasiRequest $request, $id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);

            $validated = $request->validated();

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

            return Response::success($pengajuan, 'Status berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal memperbarui status');
        }
    }

    /**
     * Upload bukti transfer
     */
    public function uploadBuktiTransfer(PengajuanInvestasiRequest $request, $id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);

            $validated = $request->validated();

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

                // Reload pengajuan dengan relasi
                $pengajuan->load('investor');
            }

            DB::commit();

            return Response::success($pengajuan, 'Bukti transfer berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Terjadi kesalahan saat upload bukti transfer');
        }
    }

    /**
     * Download Certificate
     */
    public function downloadSertifikat($id)
    {
        try {
            $pengajuan = PengajuanInvestasi::with('investor')->findOrFail($id);

            // Check if status is Selesai
            if ($pengajuan->status !== 'Selesai') {
                return redirect()->back()->with('error', 'Sertifikat hanya tersedia untuk pengajuan yang sudah selesai');
            }

            // Generate Nomor Deposito
            $year = date('Y');
            $countThisYear = PengajuanInvestasi::whereYear('created_at', $year)
                ->where('status', 'Selesai')
                ->where('id_pengajuan_investasi', '<=', $pengajuan->id_pengajuan_investasi)
                ->count();

            $nomorDeposito = 'DC' . $year . str_pad($countThisYear, 4, '0', STR_PAD_LEFT);

            // Get description based on deposito type
            $deskripsi = $pengajuan->jenis_investasi === 'Khusus'
                ? 'INVESTASI DEPOSITO KHUSUS'
                : 'INVESTASI DEPOSITO REGULER';

            // Calculate tanggal berakhir (tanggal_investasi + lama_investasi bulan)
            $tanggalInvestasiCarbon = \Carbon\Carbon::parse($pengajuan->tanggal_investasi);
            $tanggalBerakhirCarbon = $tanggalInvestasiCarbon->copy()->addMonths($pengajuan->lama_investasi);

            // Format dates
            $tanggalInvestasi = $tanggalInvestasiCarbon->translatedFormat('d F Y');
            $tanggalBerakhir = $tanggalBerakhirCarbon->translatedFormat('d F Y');
            $jangkaWaktu = $tanggalInvestasi . ' - ' . $tanggalBerakhir;

            $data = [
                'nama_deposan' => $pengajuan->nama_investor,
                'nomor_deposito' => $nomorDeposito,
                'deskripsi' => $deskripsi,
                'nilai_deposito' => 'Rp ' . number_format($pengajuan->jumlah_investasi, 0, ',', '.'),
                'kode_transaksi' => $pengajuan->nomor_kontrak ?? '-',
                'jangka_waktu' => $jangkaWaktu,
                'bagi_hasil' => $pengajuan->bunga_pertahun . ' % P.A NET',
                'nilai_investasi_text' => 'Rp. ' . number_format($pengajuan->jumlah_investasi, 2, ',', '.'),
            ];

            return view('livewire.pengajuan-investasi.sertifikat', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menggenerate sertifikat: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor kontrak untuk pengajuan investasi
     */
    public function generateKontrak(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'nama_pic_kontrak' => 'required|string|max:255',
            ], [
                'nama_pic_kontrak.required' => 'Nama PIC/CEO harus diisi',
                'nama_pic_kontrak.string' => 'Nama PIC/CEO harus berupa teks',
                'nama_pic_kontrak.max' => 'Nama PIC/CEO maksimal 255 karakter',
            ]);

            // Get pengajuan investasi
            $investasi = PengajuanInvestasi::with('investor')->findOrFail($id);

            // Validasi: Pastikan pengajuan sudah disetujui CEO (current_step >= 4)
            if ($investasi->current_step < 4) {
                return Response::error('Pengajuan belum disetujui CEO. Tidak bisa generate kontrak.', 400);
            }

            // Validasi: Nomor kontrak belum pernah di-generate
            if (!empty($investasi->nomor_kontrak)) {
                return Response::error('Nomor kontrak sudah pernah di-generate: ' . $investasi->nomor_kontrak, 400);
            }

            // Validasi: Investor harus punya kode perusahaan
            if (empty($investasi->investor->kode_perusahaan)) {
                return Response::error('Kode perusahaan investor belum diisi. Hubungi administrator.', 400);
            }

            // Generate nomor kontrak
            $nomorKontrak = ContractNumberService::generateInvestasi(
                $investasi->investor->kode_perusahaan,
                $investasi->jenis_investasi,
                $investasi->tanggal_investasi
            );

            // Update pengajuan investasi dengan nomor kontrak DAN nama_pic_kontrak
            $investasi->update([
                'nomor_kontrak' => $nomorKontrak,
                'nama_pic_kontrak' => $request->nama_pic_kontrak,
                'updated_by' => Auth::id(),
            ]);

            // Create history for "Generate Kontrak"
            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $investasi->id_pengajuan_investasi,
                'status' => 'Generate Kontrak',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 6,
                'submit_step1_by' => Auth::id(),
            ]);

            // Reload pengajuan dengan relasi untuk notifikasi
            $investasi->refresh();
            $investasi->load('investor');

            // Kirim notifikasi untuk kontrak investasi dibuat
            ListNotifSFinance::menuPengajuanInvestasi('Generate Kontrak', $investasi);

            // Update status to completed (Step 6: Selesai)
            $investasi->update([
                'status' => 'Selesai',
                'current_step' => 6,
            ]);

            // Create history for "Selesai"
            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $investasi->id_pengajuan_investasi,
                'status' => 'Selesai',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 6,
                'submit_step1_by' => Auth::id(),
            ]);

            // Reload pengajuan untuk memastikan ter-update
            $investasi->refresh();

            DB::commit();

            // Kirim notifikasi untuk investasi berhasil (status Selesai)
            ListNotifSFinance::menuPengajuanInvestasi('Selesai', $investasi, $investasi->jumlah_investasi);

            return Response::success([
                'nomor_kontrak' => $nomorKontrak,
                'id_pengajuan' => $investasi->id_pengajuan_investasi
            ], 'Nomor kontrak berhasil di-generate: ' . $nomorKontrak);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generate kontrak investasi: ' . $e->getMessage());
            return Response::errorCatch($e, 'Gagal generate kontrak investasi');
        }
    }

    /**
     * Build custom HTML for PDF contract
     */
    private function buildKontrakHTML($kontrak)
    {
        try {
            // Load logo as base64 - resize if needed
            $logoPath = public_path('assets/img/branding/Logo.jpg');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
                try {
                    $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
                    Log::info('Logo loaded successfully');
                } catch (\Exception $e) {
                    Log::warning('Error loading logo: ' . $e->getMessage());
                }
            } else {
                Log::warning('Logo file not found: ' . $logoPath);
            }

            // Load TTD perusahaan as base64
            $ttdPerusahaanPath = public_path('assets/img/ttd.png');
            $ttdPerusahaan = '';
            if (file_exists($ttdPerusahaanPath)) {
                try {
                    $ttdPerusahaan = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdPerusahaanPath));
                    Log::info('TTD Perusahaan loaded successfully');
                } catch (\Exception $e) {
                    Log::warning('Error loading TTD Perusahaan: ' . $e->getMessage());
                }
            } else {
                Log::warning('TTD Perusahaan file not found: ' . $ttdPerusahaanPath);
            }

            // Load TTD investor as base64 if exists
            $ttdInvestor = '';
            if (!empty($kontrak['tanda_tangan_investor'])) {
                $ttdInvestorPath = storage_path('app/public/' . $kontrak['tanda_tangan_investor']);
                if (file_exists($ttdInvestorPath)) {
                    try {
                        $ttdInvestor = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdInvestorPath));
                        Log::info('TTD Investor loaded successfully');
                    } catch (\Exception $e) {
                        Log::warning('Error loading TTD Investor: ' . $e->getMessage());
                    }
                } else {
                    Log::warning('TTD Investor file not found: ' . $ttdInvestorPath);
                }
            }

            $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kontrak Investasi - ' . htmlspecialchars($kontrak['nomor_kontrak']) . '</title>
    <style>
        @page {
            margin: 20mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header-logo {
            text-align: right;
            margin-bottom: 20px;
        }
        .header-logo img {
            max-height: 50px;
            max-width: 200px;
        }
        .title {
            text-align: center;
            margin-bottom: 30px;
        }
        .title h5 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .title p {
            font-size: 12pt;
            font-weight: bold;
        }
        .content {
            text-align: justify;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .indent {
            padding-left: 20px;
        }
        .pasal {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            text-align: center;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-table {
            width: 100%;
        }
        .signature-col {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-space {
            height: 80px;
            margin-bottom: 10px;
        }
        .signature-img {
            max-height: 80px;
            max-width: 150px;
        }
    </style>
</head>
<body>
    <!-- Header dengan Logo -->
    <div class="header-logo">
        ' . ($logoBase64 ? '<img src="' . $logoBase64 . '" alt="S-Capital Logo">' : '') . '
    </div>

    <!-- Judul Kontrak -->
    <div class="title">
        <h5>SURAT PERJANJIAN KERJASAMA INVESTASI DEPOSITO ' . $kontrak['jenis_deposito'] . '</h5>
        <p>No: ' . $kontrak['nomor_kontrak'] . '</p>
    </div>

    <!-- Pembukaan -->
    <div class="content">
        <p>
            Pada hari ini <strong>' . $kontrak['hari'] . '</strong>, tanggal <strong>' . $kontrak['tanggal_kontrak'] . '</strong>, yang bertanda tangan di bawah ini:
        </p>

        <!-- Pihak Pertama (Investor) -->
        <table>
            <tr>
                <td width="5%">1.</td>
                <td width="25%">Nama</td>
                <td width="70%">: ' . $kontrak['nama_investor'] . '</td>
            </tr>
            <tr>
                <td></td>
                <td>Perusahaan</td>
                <td>: ' . $kontrak['perusahaan_investor'] . '</td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat</td>
                <td>: ' . $kontrak['alamat_investor'] . '</td>
            </tr>
        </table>
        <p>Untuk selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>

        <!-- Pihak Kedua (PT Synnovac) -->
        <table>
            <tr>
                <td width="5%">2.</td>
                <td width="25%">Nama</td>
                <td width="70%">: Muhamad Kurniawan</td>
            </tr>
            <tr>
                <td></td>
                <td>Perusahaan</td>
                <td>: PT. Synnovac Kapital Indonesia</td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat</td>
                <td>: Permata Kuningan Building 17th Floor, Kawasan Epicentrum, HR Rasuna Said, Jl. Kuningan Mulia, RT.6/RW.1, Menteng Atas, Setiabudi, South Jakarta City, Jakarta 12920</td>
            </tr>
        </table>
        <p>Untuk selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>

        <br>

        <!-- Pendahuluan -->
        <p>
            Bahwa sebelum ditandatanganinya Surat Perjanjian Investasi ini berupa Penempatan Dana Deposito, PARA PIHAK terlebih dahulu menerangkan hal–hal sebagai berikut:
        </p>

        <div class="indent">
            <p>1. Bahwa PIHAK PERTAMA adalah selaku Investor yang memiliki dana sebesar ' . $kontrak['jumlah_investasi_angka'] . ' (' . $kontrak['jumlah_investasi_text'] . ') untuk selanjutnya disebut sebagai Dana Deposito kepada S-Finance untuk Pembiayaan usaha yang dibawah naungan S-Finance.</p>
            
            <p>2. Bahwa PIHAK KEDUA adalah Penyalur, monitoring dan Penjamin Dana Deposito yang menerima Dana Deposito dari PIHAK PERTAMA.</p>
            
            <p>3. Bahwa PARA PIHAK setuju untuk saling mengikatkan diri dalam suatu perjanjian kerjasama Deposito sesuai dengan ketentuan hukum yang berlaku.</p>
            
            <p>4. PARA PIHAK menyatakan bahwa bertindak atas dasar sukarela dan tanpa paksaan dari pihak manapun.</p>
            
            <p>5. Bahwa berdasarkan hal-hal tersebut di atas, PARA PIHAK menyatakan sepakat dan setuju untuk mengadakan Perjanjian Kerjasama Deposito ini yang dilaksanakan dengan ketentuan dan syarat-syarat sebagai berikut.</p>
        </div>

        <br>

        <!-- PASAL I -->
        <div class="pasal">PASAL I<br>MAKSUD DAN TUJUAN</div>
        <div class="indent">
            <p>1. Membentuk kerjasama Deposito dari PARA PIHAK untuk pembiayaan S-Finance yang saling menguntungkan dengan saling menjaga etika bisnis dari para pihak serta dilakukan secara profesional dan amanah</p>
        </div>

        <!-- PASAL II -->
        <div class="pasal">PASAL II<br>RUANG LINGKUP</div>
        <div class="indent">
            <p>1. Dalam pelaksanaan perjanjian ini, PIHAK PERTAMA memberi Dana Deposito kepada PIHAK KEDUA sebesar ' . $kontrak['jumlah_investasi_angka'] . ' (' . $kontrak['jumlah_investasi_text'] . ') dan PIHAK KEDUA dengan ini menerima penyerahan Dana Deposito tersebut dari PIHAK PERTAMA serta menyanggupi sebagai penyalur, monitoring, dan penjamin dana Deposito</p>
            
            <p>2. PIHAK KEDUA dengan ini berjanji dan mengikatkan diri untuk mengelola perputaran Dana Deposito secara khusus pada Usaha Pembiayaan di dibawah naungan S-Finance.</p>
        </div>

        <!-- PASAL III -->
        <div class="pasal">PASAL III<br>JANGKA WAKTU KERJASAMA</div>
        <div class="indent">
            <p>1. Perjanjian kerjasama ini berlaku sampai tanggal ' . $kontrak['tanggal_jatuh_tempo'] . ' dan dapat diperpanjang dengan persetujuan PARA PIHAK dengan konfirmasi 2 minggu sebelum berakhir kontrak.</p>
            
            <p>2. Jangka waktu penutupan deposito adalah sampai ' . $kontrak['tanggal_jatuh_tempo'] . '. Jika deposito diambil sebelum masa waktunya, maka akan dikenakan penalti sebesar 1% dari nilai nominal deposito</p>
            
            <p>3. Persetujuan perpanjangan Perjanjian kerjasama yang dimaksudkan dapat dilakukan secara otomatis berdasarkan konfirmasi awal dari PIHAK PERTAMA kepada PIHAK KEDUA, atau Non Otomatis jika diperlukan adanya Keputusan Deposito dari PIHAK PERTAMA jika terdapat perubahan objek atau skema Deposito didalam kelolaan usaha PIHAK KETIGA</p>
        </div>

        <!-- Continue with other Pasal sections (abbreviated for space) -->
        <div class="pasal">PASAL IV<br>HAK DAN KEWAJIBAN PIHAK PERTAMA</div>
        <div class="indent">
            <p>1. Memberikan Dana Deposito kepada PIHAK KEDUA sebesar ' . $kontrak['jumlah_investasi_angka'] . ' (' . $kontrak['jumlah_investasi_text'] . ') yang di tempatkan/ditransfer ke rekening S-Finance</p>
            <p>2. Berhak meminta kembali Dana Deposito yang telah diserahkan kepada PIHAK KEDUA dengan ketentuan berdasarkan Pasal III Ayat 2.</p>
            <p>3. Menerima hasil keuntungan atas pengelolaan Dana Deposito dari PIHAK KEDUA, sesuai dengan Pasal VI perjanjian ini</p>
        </div>

        <div class="pasal">PASAL V<br>HAK DAN KEWAJIBAN PIHAK KEDUA</div>
        <div class="indent">
            <p>1. Menerima Dana Deposito dari PIHAK PERTAMA sebesar ' . $kontrak['jumlah_investasi_angka'] . ' (' . $kontrak['jumlah_investasi_text'] . ') yang ditempatkan di rekening S-Finance</p>
            <p>2. Menyalurkan, monitoring Dana Deposito PIHAK PERTAMA</p>
            <p>3. Memberikan bagian hasil keuntungan kepada PIHAK PERTAMA.</p>
        </div>

        <div class="pasal">PASAL VI<br>PEMBAGIAN HASIL</div>
        <div class="indent">
            <p>1. Bagi Hasil kepada PIHAK PERTAMA sebesar ' . $kontrak['bagi_hasil'] . ' % per Tahun terhitung dari tanggal diterimanya dana oleh PIHAK KEDUA dan nilai bagi hasil akan diberikan dari PIHAK KEDUA di akhir periode kerjasama.</p>
            <p>2. Jika dana masuk di atas tanggal 20, maka bagi hasil akan di hitung di bulan berikutnya.</p>
        </div>

        <br>
        <p class="text-start">Jakarta, ' . $kontrak['tanggal_kontrak'] . '</p>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-col">
                        <strong>PIHAK PERTAMA</strong><br>
                        (Investor)<br><br>
                        <div class="signature-space">
                            ' . ($ttdInvestor ? '<img src="' . $ttdInvestor . '" class="signature-img">' : '') . '
                        </div>
                        <strong>' . $kontrak['nama_investor'] . '</strong>
                    </td>
                    <td class="signature-col">
                        <strong>PIHAK KEDUA</strong><br>
                        (PT. Synnovac Kapital Indonesia)<br><br>
                        <div class="signature-space">
                            ' . ($ttdPerusahaan ? '<img src="' . $ttdPerusahaan . '" class="signature-img">' : '') . '
                        </div>
                        <strong>Muhamad Kurniawan</strong><br>
                        Direktur
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
        ';

            return $html;
        } catch (\Exception $e) {
            Log::error('Error building kontrak HTML: ' . $e->getMessage());
            throw $e;
        }
    }
}
