<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\ListNotifSFinance;
use App\Models\PengajuanCicilan;
use App\Models\MasterDebiturDanInvestor;
use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use App\Models\HistoryStatusPengajuanCicilan;
use App\Models\HistoryStatusPengajuanPinjaman;
use App\Http\Requests\PengajuanCicilanRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanCicilanController extends Controller
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
        $this->middleware('can:pengajuan_cicilan.add')->only(['store']);
        $this->middleware('can:pengajuan_cicilan.edit')->only(['edit', 'update']);
        $this->middleware('can:pengajuan_cicilan.ajukan_cicilan')->only(['updateDokumen']);
    }

    public function index()
    {
        $debitur = $this->getCurrentDebitur();
        $debiturList = $this->getActiveDebiturList();
        $peminjamanList = $debitur ? $this->getPeminjamanList($debitur->id_debitur) : [];
        // dd($peminjamanList);

        return view('livewire.pengajuan-cicilan.index', compact('debitur', 'debiturList', 'peminjamanList'));
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
                'tunggakan_margin_bunga' => $this->getTunggakanMargin($pengajuan),
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

    public function store(PengajuanCicilanRequest $request)
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

            $pengajuan = PengajuanCicilan::create($validated);

            // Update status Pengajuan Peminjaman menjadi "Proses Cicilan"
            if (isset($validated['id_pengajuan_peminjaman'])) {
                PengajuanPeminjaman::where('id_pengajuan_peminjaman', $validated['id_pengajuan_peminjaman'])
                    ->update(['status' => 'Proses Cicilan']);
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
            $pengajuan = PengajuanCicilan::with(['debitur', 'pengajuanPeminjaman'])->findOrFail($id);

            // Load history
            $histories = HistoryStatusPengajuanCicilan::where('id_pengajuan_cicilan', $id)
                ->with(['submittedBy', 'approvedBy', 'rejectedBy'])
                ->orderByDesc('created_at')
                ->get();

            $latestHistory = $histories->first();

            // Convert to array format for view consistency
            $restrukturisasi = [
                'id' => $pengajuan->id_pengajuan_cicilan,
                'nama_perusahaan' => $pengajuan->debitur->nama_perusahaan ?? '-',
                'status' => $pengajuan->status,
                'current_step' => $pengajuan->current_step ?? 1,
                'data' => $pengajuan, // Full model for detailed access
            ];

            return view('livewire.pengajuan-cicilan.detail', compact('restrukturisasi', 'histories', 'latestHistory', 'pengajuan'));
        } catch (\Exception $e) {
            abort(404, 'Pengajuan restrukturisasi tidak ditemukan');
        }
    }

    public function edit($id)
    {
        try {
            $pengajuan = PengajuanCicilan::with(['debitur', 'pengajuanPeminjaman'])->findOrFail($id);
            return Response::success($pengajuan, 'Data pengajuan restrukturisasi berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data');
        }
    }

    public function update(PengajuanCicilanRequest $request, $id)
    {
        try {
            $pengajuan = PengajuanCicilan::findOrFail($id);

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
            $pengajuan = PengajuanCicilan::findOrFail($id);
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
            $pengajuan = PengajuanCicilan::findOrFail($id);

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
        // Prioritas: ambil dari pengajuan_peminjaman.sisa_bayar_pokok
        if ($pengajuan->sisa_bayar_pokok !== null) {
            return $pengajuan->sisa_bayar_pokok;
        }

        // Fallback: ambil dari jumlah plafon awal jika sisa_bayar_pokok belum diset
        return $this->getJumlahPlafonAwal($pengajuan);
    }

    private function getTunggakanMargin($pengajuan)
    {
        // Ambil dari pengajuan_peminjaman.sisa_bunga
        return $pengajuan->sisa_bunga ?? 0;
    }

    private function getJatuhTempoTerakhir($idPengajuanPeminjaman)
    {
        // Ambil langsung dari pengajuan_peminjaman.tanggal_jatuh_tempo
        $pengajuan = PengajuanPeminjaman::find($idPengajuanPeminjaman);

        if ($pengajuan && $pengajuan->tanggal_jatuh_tempo) {
            $tanggalJatuhTempo = Carbon::parse($pengajuan->tanggal_jatuh_tempo);

            return [
                'jatuh_tempo_terakhir' => $tanggalJatuhTempo->format('Y-m-d'),
                'jatuh_tempo_terakhir_formatted' => $tanggalJatuhTempo->locale('id')->isoFormat('D MMMM YYYY'),
            ];
        }

        // Fallback: hitung dari tanggal_pencairan di history jika tanggal_jatuh_tempo masih NULL
        if ($pengajuan) {
            $historyPencairan = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $idPengajuanPeminjaman)
                ->whereNotNull('tanggal_pencairan')
                ->latest()
                ->first();

            if ($historyPencairan && $historyPencairan->tanggal_pencairan) {
                if ($pengajuan->jenis_pembiayaan === 'Installment' && $pengajuan->tenor_pembayaran) {
                    $hariJatuhTempo = $pengajuan->tenor_pembayaran * 30;
                } else {
                    $hariJatuhTempo = 30;
                }

                $tanggalJatuhTempo = Carbon::parse($historyPencairan->tanggal_pencairan)->addDays($hariJatuhTempo);

                // Simpan ke DB agar tidak perlu dihitung ulang di masa mendatang
                $pengajuan->tanggal_jatuh_tempo = $tanggalJatuhTempo;
                $pengajuan->sisa_bayar_pokok = $pengajuan->sisa_bayar_pokok ?? $pengajuan->total_pinjaman;
                $pengajuan->sisa_bunga = $pengajuan->sisa_bunga ?? $pengajuan->total_bunga;
                $pengajuan->save();

                return [
                    'jatuh_tempo_terakhir' => $tanggalJatuhTempo->format('Y-m-d'),
                    'jatuh_tempo_terakhir_formatted' => $tanggalJatuhTempo->locale('id')->isoFormat('D MMMM YYYY'),
                ];
            }
        }

        return [
            'jatuh_tempo_terakhir' => null,
            'jatuh_tempo_terakhir_formatted' => null,
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
