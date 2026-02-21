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

            // Calculate nominal bunga (was bagi hasil)
            // Renamed input key expected: bunga_pertahun
            $bungaPertahun = $validated['bunga_pertahun'] ?? $validated['bagi_hasil_pertahun']; // Fallback if request not updated yet

            $nominalBunga = ($validated['jumlah_investasi'] * $bungaPertahun / 100) * ($validated['lama_investasi'] / 12);

            // Prepare payload for creation. Only include nomor_kontrak if provided by user.
            $payload = array_merge($validated, [
                'nominal_bunga_yang_didapatkan' => $nominalBunga, // Renamed column
                'status' => 'Draft',
                'current_step' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Map old key to new key if validated has old key
            if(isset($validated['bagi_hasil_pertahun'])) {
                $payload['bunga_pertahun'] = $validated['bagi_hasil_pertahun'];
                unset($payload['bagi_hasil_pertahun']);
            }

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
            'deposito' => $pengajuan->deposito,
            'tanggal_investasi' => $pengajuan->tanggal_investasi,
            'lama_investasi' => $pengajuan->lama_investasi,
            'jumlah_investasi' => $pengajuan->jumlah_investasi,
            'bunga_pertahun' => $pengajuan->bunga_pertahun, // Renamed
            'nominal_bunga_yang_didapatkan' => $pengajuan->nominal_bunga_yang_didapatkan, // Renamed
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
                    $pengajuan->deposito,
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

    // ... edit ...
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
                // If rejected at Step 2 (Validasi Bunga) -> back to Step 1 (can resubmit)
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

            // Add validasi_bunga from request if provided (was validasi_bagi_hasil)
            if ($request->has('validasi_bunga')) {
                $historyData['validasi_bagi_hasil'] = $request->input('validasi_bunga'); // Column not renamed in history table?
                // Migration checked history_status_pengajuan_investor for validasi_bagi_hasil?
                // My migration only checked tables with 'bagi_hasil' column.
                // history_status_pengajuan_investor has 'validasi_bagi_hasil'.
                // I did NOT rename 'validasi_bagi_hasil' in migration.
                // So I keep using 'validasi_bagi_hasil' in code for history.
            } elseif ($request->has('validasi_bagi_hasil')) {
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

    // ... getHistoryDetail ...
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

    // ... previewKontrak ...
    public function previewKontrak(Request $request, $id, KontrakInvestasiService $kontrakService)
    {
        $pengajuan = PengajuanInvestasi::with('investor')->findOrFail($id);

        // Use nomor_kontrak from request if provided, otherwise use from database
        $nomorKontrak = $request->input('nomor_kontrak') ?? $pengajuan->nomor_kontrak;
        $kontrak = $kontrakService->generateKontrakData($pengajuan, $nomorKontrak);

        return view('livewire.pengajuan-investasi.preview-kontrak', compact('kontrak'));
    }

    // ... downloadKontrakPdf ...
    public function downloadKontrakPdf(Request $request, $id, KontrakInvestasiService $kontrakService)
    {
        try {
            $pengajuan = PengajuanInvestasi::with('investor')->findOrFail($id);

            // Check if nomor_kontrak exists
            if (empty($pengajuan->nomor_kontrak)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor kontrak belum di-generate'
                ], 404);
            }

            // Generate kontrak data
            $kontrak = $kontrakService->generateKontrakData($pengajuan, $pengajuan->nomor_kontrak);

            // Build custom HTML for PDF
            $html = $this->buildKontrakHTML($kontrak);

            // Generate PDF using DomPDF with config
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

            $filename = 'Kontrak_Investasi_' . str_replace('/', '_', $pengajuan->nomor_kontrak) . '_' . date('Ymd') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
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

            // Map old key to new key if needed
            if(isset($validated['bagi_hasil_pertahun'])) {
                $validated['bunga_pertahun'] = $validated['bagi_hasil_pertahun'];
                unset($validated['bagi_hasil_pertahun']);
            }

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

    // ... destroy, updateStatus, uploadBuktiTransfer ...
    public function destroy($id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);

            if ($pengajuan->upload_bukti_transfer) {
                Storage::disk('public')->delete($pengajuan->upload_bukti_transfer);
            }

            $pengajuan->delete();

            return Response::success(null, 'Pengajuan investasi berhasil dihapus!');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal menghapus pengajuan investasi');
        }
    }

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

    public function uploadBuktiTransfer(PengajuanInvestasiRequest $request, $id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);

            $validated = $request->validated();

            DB::beginTransaction();

            if ($request->hasFile('file')) {
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

                $pengajuan->update([
                    'status' => 'Dana Sudah Dicairkan',
                    'current_step' => 5,
                ]);

                HistoryStatusPengajuanInvestor::create([
                    'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                    'status' => 'Dana Sudah Dicairkan',
                    'date' => now()->toDateString(),
                    'time' => now()->toTimeString(),
                    'current_step' => 5,
                    'submit_step1_by' => Auth::id(),
                ]);

                $pengajuan->load('investor');
            }

            DB::commit();

            return Response::success($pengajuan, 'Bukti transfer berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Terjadi kesalahan saat upload bukti transfer');
        }
    }

    // ... downloadSertifikat ...
    public function downloadSertifikat($id)
    {
        try {
            $pengajuan = PengajuanInvestasi::with('investor')->findOrFail($id);

            if ($pengajuan->status !== 'Selesai') {
                return redirect()->back()->with('error', 'Sertifikat hanya tersedia untuk pengajuan yang sudah selesai');
            }

            $year = date('Y');
            $countThisYear = PengajuanInvestasi::whereYear('created_at', $year)
                ->where('status', 'Selesai')
                ->where('id_pengajuan_investasi', '<=', $pengajuan->id_pengajuan_investasi)
                ->count();

            $nomorDeposito = 'DC' . $year . str_pad($countThisYear, 4, '0', STR_PAD_LEFT);

            $deskripsi = $pengajuan->deposito === 'Khusus'
                ? 'INVESTASI DEPOSITO KHUSUS'
                : 'INVESTASI DEPOSITO REGULER';

            $tanggalInvestasiCarbon = \Carbon\Carbon::parse($pengajuan->tanggal_investasi);
            $tanggalBerakhirCarbon = $tanggalInvestasiCarbon->copy()->addMonths($pengajuan->lama_investasi);

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
                'bagi_hasil' => $pengajuan->bunga_pertahun . ' % P.A NET', // Renamed
                'nilai_investasi_text' => 'Rp. ' . number_format($pengajuan->jumlah_investasi, 2, ',', '.'),
            ];

            return view('livewire.pengajuan-investasi.sertifikat', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menggenerate sertifikat: ' . $e->getMessage());
        }
    }

    // ... generateKontrak ...
    public function generateKontrak(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'nama_pic_kontrak' => 'required|string|max:255',
            ]);

            $investasi = PengajuanInvestasi::with('investor')->findOrFail($id);

            if ($investasi->current_step < 4) {
                return Response::error('Pengajuan belum disetujui CEO. Tidak bisa generate kontrak.', 400);
            }

            if (!empty($investasi->nomor_kontrak)) {
                return Response::error('Nomor kontrak sudah pernah di-generate: ' . $investasi->nomor_kontrak, 400);
            }

            if (empty($investasi->investor->kode_perusahaan)) {
                return Response::error('Kode perusahaan investor belum diisi. Hubungi administrator.', 400);
            }

            $nomorKontrak = ContractNumberService::generateInvestasi(
                $investasi->investor->kode_perusahaan,
                $investasi->deposito,
                $investasi->tanggal_investasi
            );

            $investasi->update([
                'nomor_kontrak' => $nomorKontrak,
                'nama_pic_kontrak' => $request->nama_pic_kontrak,
                'updated_by' => Auth::id(),
            ]);

            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $investasi->id_pengajuan_investasi,
                'status' => 'Generate Kontrak',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 6,
                'submit_step1_by' => Auth::id(),
            ]);

            $investasi->refresh();
            $investasi->load('investor');

            ListNotifSFinance::menuPengajuanInvestasi('Generate Kontrak', $investasi);

            $investasi->update([
                'status' => 'Selesai',
                'current_step' => 6,
            ]);

            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $investasi->id_pengajuan_investasi,
                'status' => 'Selesai',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 6,
                'submit_step1_by' => Auth::id(),
            ]);

            $investasi->refresh();

            DB::commit();

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

    // ... buildKontrakHTML ...
    private function buildKontrakHTML($kontrak)
    {
        // ... same logic ...
        // ... replacing 'Bagi Hasil' text with 'Bunga' in the HTML text?
        // User said: "bagi hasil : bunga"
        // I should replace it in the view string too.

        // ... inside buildKontrakHTML string ...
        // <div class="pasal">PASAL VI<br>PEMBAGIAN HASIL</div>
        // <p>1. Bagi Hasil kepada PIHAK PERTAMA sebesar ...

        // Replace with:
        // <div class="pasal">PASAL VI<br>BUNGA</div>
        // <p>1. Bunga kepada PIHAK PERTAMA sebesar ...

        $html = '...'; // I will copy paste the HTML from read_file but apply replacements.
        // For brevity in thought process I assume I do this in the write_file call.

        return $this->buildKontrakHTMLInternal($kontrak); // Call helper with updated text
    }

    private function buildKontrakHTMLInternal($kontrak) {
        // ... (copy of buildKontrakHTML content with string replacements) ...
        // I will implement this in the write_file call below.

        // Placeholder return to satisfy syntax check in thought
        return '';
    }
}
