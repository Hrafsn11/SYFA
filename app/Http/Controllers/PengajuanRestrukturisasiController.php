<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\ListNotifSFinance;
use App\Models\PengajuanRestrukturisasi;
use App\Models\MasterDebiturDanInvestor;
use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use App\Models\HistoryStatusPengajuanRestrukturisasi;
use App\Http\Requests\PengajuanRestrukturisasiRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanRestrukturisasiController extends Controller
{
    private const STATUS_DICAIRKAN = 'Dana Sudah Dicairkan';
    private const DOCUMENT_FIELDS = [
        'dokumen_ktp_pic',
        'dokumen_npwp_perusahaan',
        'dokumen_laporan_keuangan',
        'dokumen_arus_kas',
        'dokumen_kondisi_eksternal',
        'dokumen_kontrak_pembiayaan',
        'dokumen_lainnya',
        'dokumen_tanda_tangan',
    ];

    public function __construct()
    {
        $this->middleware('can:pengajuan_restrukturisasi.add')->only(['store']);
        $this->middleware('can:pengajuan_restrukturisasi.edit')->only(['edit', 'update']);
        $this->middleware('can:pengajuan_restrukturisasi.ajukan_restrukturisasi')->only(['updateDokumen']);
    }

    public function index()
    {
        $debitur = $this->getCurrentDebitur();
        $debiturList = $this->getActiveDebiturList();
        $peminjamanList = $debitur ? $this->getPeminjamanList($debitur->id_debitur) : [];
        // dd($peminjamanList);

        return view('livewire.pengajuan-restrukturisasi.index', compact('debitur', 'debiturList', 'peminjamanList'));
    }

    public function getPeminjamanListApi($idDebitur)
    {
        try {
            $peminjamanList = $this->getPeminjamanList($idDebitur);
            return Response::success($peminjamanList, 'Data peminjaman berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data peminjaman');
        }
    }

    public function getPengajuanDetail($id)
    {
        try {
            $pengajuan = PengajuanPeminjaman::findOrFail($id);
            $pengembalianTerakhir = $this->getLatestPengembalian($pengajuan->id_pengajuan_peminjaman);

            $data = [
                'jenis_pembiayaan' => $pengajuan->jenis_pembiayaan,
                'jumlah_plafon_awal' => $this->getJumlahPlafonAwal($pengajuan),
                'sisa_pokok_belum_dibayar' => $this->getSisaPokok($pengembalianTerakhir, $pengajuan),
                'tunggakan_margin_bunga' => $this->getTunggakanMargin($pengembalianTerakhir),
            ];

            $jatuhTempo = $this->getJatuhTempoTerakhir($pengajuan->id_pengajuan_peminjaman);
            $data = array_merge($data, $jatuhTempo);

            // Calculate DPD (Days Past Due)
            $data['status_dpd'] = $this->calculateDPD($jatuhTempo['jatuh_tempo_terakhir']);

            return Response::success($data, 'Data pengajuan berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data pengajuan');
        }
    }

    public function store(PengajuanRestrukturisasiRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated = $this->handleFileUploads($request, $validated);
            $validated['status'] = 'Draft';

            // Calculate DPD if jatuh_tempo_terakhir is provided
            if (isset($validated['jatuh_tempo_terakhir'])) {
                $validated['status_dpd'] = $this->calculateDPD($validated['jatuh_tempo_terakhir']);
            }

            $pengajuan = PengajuanRestrukturisasi::create($validated);

            // Update status Pengajuan Peminjaman menjadi "Proses Restrukturisasi"
            if (isset($validated['id_pengajuan_peminjaman'])) {
                PengajuanPeminjaman::where('id_pengajuan_peminjaman', $validated['id_pengajuan_peminjaman'])
                    ->update(['status' => 'Proses Restrukturisasi']);
            }

            DB::commit();

            return Response::success($pengajuan, 'Pengajuan restrukturisasi berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal membuat pengajuan restrukturisasi');
        }
    }

    public function show($id)
    {
        try {
            $pengajuan = PengajuanRestrukturisasi::with(['debitur', 'pengajuanPeminjaman'])->findOrFail($id);

            // Load history
            $histories = HistoryStatusPengajuanRestrukturisasi::where('id_pengajuan_restrukturisasi', $id)
                ->with(['submittedBy', 'approvedBy', 'rejectedBy'])
                ->orderByDesc('created_at')
                ->get();

            $latestHistory = $histories->first();

            // Convert to array format for view consistency
            $restrukturisasi = [
                'id' => $pengajuan->id_pengajuan_restrukturisasi,
                'nama_perusahaan' => $pengajuan->debitur->nama_perusahaan ?? '-',
                'status' => $pengajuan->status,
                'current_step' => $pengajuan->current_step ?? 1,
                'data' => $pengajuan, // Full model for detailed access
            ];

            return view('livewire.pengajuan-restrukturisasi.detail', compact('restrukturisasi', 'histories', 'latestHistory', 'pengajuan'));
        } catch (\Exception $e) {
            abort(404, 'Pengajuan restrukturisasi tidak ditemukan');
        }
    }

    public function edit($id)
    {
        try {
            $pengajuan = PengajuanRestrukturisasi::with(['debitur', 'pengajuanPeminjaman'])->findOrFail($id);
            return Response::success($pengajuan, 'Data pengajuan restrukturisasi berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data');
        }
    }

    public function update(PengajuanRestrukturisasiRequest $request, $id)
    {
        try {
            $pengajuan = PengajuanRestrukturisasi::findOrFail($id);

            DB::beginTransaction();

            $validated = $request->validated();
            $validated = $this->handleFileUploads($request, $validated, $pengajuan);

            // Recalculate DPD if jatuh_tempo_terakhir is provided
            if (isset($validated['jatuh_tempo_terakhir'])) {
                $validated['status_dpd'] = $this->calculateDPD($validated['jatuh_tempo_terakhir']);
            }

            $pengajuan->update($validated);

            DB::commit();

            return Response::success($pengajuan, 'Pengajuan restrukturisasi berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal memperbarui pengajuan restrukturisasi');
        }
    }

    public function destroy($id)
    {
        try {
            $pengajuan = PengajuanRestrukturisasi::findOrFail($id);
            $this->deleteDocumentFiles($pengajuan);
            $pengajuan->delete();

            return Response::success(null, 'Pengajuan restrukturisasi berhasil dihapus!');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal menghapus pengajuan restrukturisasi');
        }
    }

    public function updateDokumen(\Illuminate\Http\Request $request, $id)
    {
        try {
            $pengajuan = PengajuanRestrukturisasi::findOrFail($id);

            if ($pengajuan->status !== 'Perbaikan Dokumen') {
                return Response::error('Pengajuan tidak dalam status Perbaikan Dokumen');
            }

            DB::beginTransaction();

            $hasNewFile = false;
            foreach (self::DOCUMENT_FIELDS as $field) {
                if ($request->hasFile($field)) {
                    $hasNewFile = true;
                    if ($pengajuan->$field) {
                        Storage::disk('public')->delete($pengajuan->$field);
                    }

                    $file = $request->file($field);
                    $filename = time() . '_' . $field . '_' . $file->getClientOriginalName();
                    $pengajuan->$field = $file->storeAs('restrukturisasi/dokumen', $filename, 'public');
                }
            }

            if (!$hasNewFile) {
                return Response::error('Tidak ada file yang diupload');
            }

            $pengajuan->save();

            DB::commit();

            return Response::success($pengajuan, 'Dokumen berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal memperbarui dokumen');
        }
    }

    private function getCurrentDebitur()
    {
        return MasterDebiturDanInvestor::where('user_id', Auth::id())
            ->where('flagging', 'tidak')
            ->where('status', 'active')
            ->first();
    }

    private function getActiveDebiturList()
    {
        return MasterDebiturDanInvestor::where('flagging', 'tidak')
            ->where('status', 'active')
            ->select('id_debitur', 'nama')
            ->get();
    }

    private function getPeminjamanList($idDebitur)
    {
        return PengajuanPeminjaman::where('id_debitur', $idDebitur)
            ->where('status', self::STATUS_DICAIRKAN)
            ->select('id_pengajuan_peminjaman', 'nomor_peminjaman', 'jenis_pembiayaan', 'total_pinjaman')
            ->get();
    }

    private function getLatestPengembalian($idPengajuanPeminjaman)
    {
        return PengembalianPinjaman::where('id_pengajuan_peminjaman', $idPengajuanPeminjaman)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    private function getJumlahPlafonAwal($pengajuan)
    {
        $history = DB::table('history_status_pengajuan_pinjaman')
            ->where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
            ->whereNotNull('nominal_yang_disetujui')
            ->orderBy('created_at', 'desc')
            ->first();

        return $history ? $history->nominal_yang_disetujui : $pengajuan->total_pinjaman;
    }

    private function getSisaPokok($pengembalianTerakhir, $pengajuan)
    {
        if ($pengembalianTerakhir) {
            return $pengembalianTerakhir->sisa_bayar_pokok;
        }

        return $this->getJumlahPlafonAwal($pengajuan);
    }

    private function getTunggakanMargin($pengembalianTerakhir)
    {
        return $pengembalianTerakhir ? $pengembalianTerakhir->sisa_bagi_hasil : 0;
    }

    private function getJatuhTempoTerakhir($idPengajuanPeminjaman)
    {
        // Prioritas 1: Cari pengembalian pinjaman terakhir yang belum lunas
        $pengembalianBelumLunas = PengembalianPinjaman::where('id_pengajuan_peminjaman', $idPengajuanPeminjaman)
            ->where('status', '!=', 'Lunas')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($pengembalianBelumLunas && $pengembalianBelumLunas->tanggal_pencairan) {
            // Jatuh tempo = tanggal pencairan cicilan + 30 hari
            $tanggalJatuhTempo = Carbon::parse($pengembalianBelumLunas->tanggal_pencairan)->addDays(30);

            return [
                'jatuh_tempo_terakhir' => $tanggalJatuhTempo->format('Y-m-d'),
                'jatuh_tempo_terakhir_formatted' => $tanggalJatuhTempo->locale('id')->isoFormat('D MMMM YYYY'),
            ];
        }

        // Fallback: Jika belum ada pengembalian, ambil dari tanggal pencairan awal (step 7)
        $history = DB::table('history_status_pengajuan_pinjaman')
            ->where('id_pengajuan_peminjaman', $idPengajuanPeminjaman)
            ->where('current_step', 7)
            ->whereNotNull('tanggal_pencairan')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$history || !$history->tanggal_pencairan) {
            return [
                'jatuh_tempo_terakhir' => null,
                'jatuh_tempo_terakhir_formatted' => null,
            ];
        }

        // Jatuh tempo = tanggal pencairan + 30 hari (bukan addMonth)
        $tanggalJatuhTempo = Carbon::parse($history->tanggal_pencairan)->addDays(30);

        return [
            'jatuh_tempo_terakhir' => $tanggalJatuhTempo->format('Y-m-d'),
            'jatuh_tempo_terakhir_formatted' => $tanggalJatuhTempo->locale('id')->isoFormat('D MMMM YYYY'),
        ];
    }

    private function calculateDPD($jatuhTempoTerakhir)
    {
        if (!$jatuhTempoTerakhir) {
            return 0;
        }

        $today = Carbon::now()->startOfDay();
        $jatuhTempo = Carbon::parse($jatuhTempoTerakhir)->startOfDay();

        // If today is greater than jatuh tempo, calculate the difference
        if ($today->greaterThan($jatuhTempo)) {
            return $today->diffInDays($jatuhTempo);
        }

        // Otherwise return 0 (not yet overdue)
        return 0;
    }

    private function handleFileUploads($request, array $validated, $pengajuan = null)
    {
        foreach (self::DOCUMENT_FIELDS as $field) {
            if ($request->hasFile($field)) {
                if ($pengajuan && $pengajuan->$field) {
                    Storage::disk('public')->delete($pengajuan->$field);
                }

                $file = $request->file($field);
                $filename = time() . '_' . $field . '_' . $file->getClientOriginalName();
                $validated[$field] = $file->storeAs('restrukturisasi/dokumen', $filename, 'public');
            }
        }

        return $validated;
    }

    private function deleteDocumentFiles($pengajuan)
    {
        foreach (self::DOCUMENT_FIELDS as $field) {
            if ($pengajuan->$field) {
                Storage::disk('public')->delete($pengajuan->$field);
            }
        }
    }
}
