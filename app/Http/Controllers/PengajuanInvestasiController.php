<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\ListNotifSFinance;
use App\Models\PengajuanInvestasi;
use App\Models\HistoryStatusPengajuanInvestor;
use App\Models\MasterDebiturDanInvestor;
use App\Http\Requests\PengajuanInvestasiRequest;
use App\Services\KontrakInvestasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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

            // Calculate nominal bagi hasil
            $nominalBagiHasil = ($validated['jumlah_investasi'] * $validated['bagi_hasil_pertahun'] / 100) * ($validated['lama_investasi'] / 12);

            // Prepare payload for creation. Only include nomor_kontrak if provided by user.
            $payload = array_merge($validated, [
                'nominal_bagi_hasil_yang_didapatkan' => $nominalBagiHasil,
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
            'deposito' => $pengajuan->deposito,
            'tanggal_investasi' => $pengajuan->tanggal_investasi,
            'lama_investasi' => $pengajuan->lama_investasi,
            'jumlah_investasi' => $pengajuan->jumlah_investasi,
            'bagi_hasil_pertahun' => $pengajuan->bagi_hasil_pertahun,
            'nominal_bagi_hasil_yang_didapatkan' => $pengajuan->nominal_bagi_hasil_yang_didapatkan,
            'upload_bukti_transfer' => $pengajuan->upload_bukti_transfer,
            'nomor_kontrak' => $pengajuan->nomor_kontrak,
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
     * Generate kontrak
     */
    public function generateKontrak(PengajuanInvestasiRequest $request, $id)
    {
        try {
            $pengajuan = PengajuanInvestasi::findOrFail($id);

            DB::beginTransaction();

            // Update nomor kontrak first
            $pengajuan->update([
                'nomor_kontrak' => $request->input('nomor_kontrak'),
                'updated_by' => Auth::id(),
            ]);

            // Create history for "Generate Kontrak"
            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                'status' => 'Generate Kontrak',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 6,
                'submit_step1_by' => Auth::id(),
            ]);

            // Reload pengajuan dengan relasi untuk notifikasi kontrak dibuat
            $pengajuan->load('investor');

            // Kirim notifikasi untuk kontrak investasi dibuat
            ListNotifSFinance::menuPengajuanInvestasi('Generate Kontrak', $pengajuan);

            // Update status to completed (Step 6: Selesai)
            $pengajuan->update([
                'status' => 'Selesai',
                'current_step' => 6,
            ]);

            // Create history for "Selesai"
            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                'status' => 'Selesai',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'current_step' => 6,
                'submit_step1_by' => Auth::id(),
            ]);

            // Reload pengajuan untuk memastikan nomor_kontrak ter-update
            $pengajuan->refresh();
            $pengajuan->load('investor');

            DB::commit();

            // Kirim notifikasi untuk investasi berhasil ditransfer (status Selesai)
            ListNotifSFinance::menuPengajuanInvestasi('Selesai', $pengajuan, $pengajuan->jumlah_investasi);

            return Response::success($pengajuan, 'Kontrak berhasil digenerate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Terjadi kesalahan saat generate kontrak');
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
            $deskripsi = $pengajuan->deposito === 'Khusus'
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
                'bagi_hasil' => $pengajuan->bagi_hasil_pertahun . ' % P.A NET',
                'nilai_investasi_text' => 'Rp. ' . number_format($pengajuan->jumlah_investasi, 2, ',', '.'),
            ];

            return view('livewire.pengajuan-investasi.sertifikat', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menggenerate sertifikat: ' . $e->getMessage());
        }
    }
}
