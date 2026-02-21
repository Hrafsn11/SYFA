<?php

namespace App\Http\Controllers;

use App\Models\BuktiPeminjaman;
use App\Models\HistoryStatusPengajuanPinjaman;
use App\Models\PengajuanTagihanPinjaman;
use App\Models\PengembalianTagihanPinjaman;
use App\Services\ArPerbulanService;
use App\Services\RiwayatTagihanAktifService; // Renamed Service
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatTagihanAktifController extends Controller
{
    public function __construct(
        private readonly RiwayatTagihanAktifService $service,
        private readonly ArPerbulanService $arPerbulanService
    ) {}

    public function getHistoriPembayaran(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_pengembalian' => 'required|string',
            'period' => 'nullable|string|date_format:Y-m',
        ]);

        $histori = $this->service->getHistoriPembayaran(
            $validated['id_pengembalian'],
            $validated['period'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => $histori,
        ]);
    }

    public function getSummaryData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_pengembalian' => 'required|string',
        ]);

        $summary = $this->service->getSummaryData($validated['id_pengembalian']);

        if (!$summary) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        // Check permission - assumes permission name changes or remains same.
        // User didn't specify permission rename. I'll keep it or change to 'riwayat_tagihan_aktif.edit' if I could.
        // For safety, I'll keep 'debitur_piutang.edit' or check if I should change it.
        // The user said "structure changes". I'll use 'riwayat_tagihan_aktif.edit' and assume I update seeds later?
        // No, updating permissions in DB is another beast. I'll stick to old permission string unless I change DB.
        // But "DebiturPiutang" string in permission might be what user wants to change.
        // I will stick to 'debitur_piutang.edit' for now to ensure access control doesn't break, unless I update seeder.
        if (!auth()->user()->can('debitur_piutang.edit')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengedit data ini',
            ], 403);
        }

        $validated = $request->validate([
            'id_pengajuan' => 'required|string',
            'id_bukti' => 'nullable|string',
            'id_history' => 'nullable|string',
            'id_pengembalian' => 'nullable|string',
            'objek_jaminan' => 'required|string|max:255',
            'nilai_dicairkan' => 'required|numeric|min:0',
            'persentase_bunga' => 'required|numeric|min:0|max:100', // Renamed
            'kurang_bayar_bunga' => 'required|numeric|min:0', // Renamed from kurang_bayar_bagi_hasil
        ]);

        try {
            DB::beginTransaction();

            // Get old values for comparison
            $pengajuan = PengajuanTagihanPinjaman::where('id_pengajuan_peminjaman', $validated['id_pengajuan'])->first();

            if (!$pengajuan) {
                throw new \Exception('Data pengajuan tidak ditemukan');
            }

            $oldNilaiDicairkan = 0;
            if ($validated['id_history']) {
                $oldHistory = HistoryStatusPengajuanPinjaman::where('id_history_status_pengajuan_pinjaman', $validated['id_history'])->first();
                $oldNilaiDicairkan = $oldHistory->nominal_yang_disetujui ?? $pengajuan->total_pinjaman;
            }
            $oldPersentase = $pengajuan->persentase_bunga ?? 0;
            $oldTotalBunga = $pengajuan->total_bunga ?? 0;

            // 1. Update objek_jaminan di bukti_peminjaman (nama_client)
            if ($validated['id_bukti']) {
                BuktiPeminjaman::where('id_bukti_peminjaman', $validated['id_bukti'])
                    ->update(['nama_client' => $validated['objek_jaminan']]);
            }

            // 2. Update nilai_dicairkan di history_status_pengajuan_pinjaman
            if ($validated['id_history']) {
                HistoryStatusPengajuanPinjaman::where('id_history_status_pengajuan_pinjaman', $validated['id_history'])
                    ->update(['nominal_yang_disetujui' => $validated['nilai_dicairkan']]);
            }

            // 3. Calculate new total_bunga
            $newNilaiDicairkan = $validated['nilai_dicairkan'];
            $newPersentase = $validated['persentase_bunga'];
            $newTotalBunga = ($newNilaiDicairkan * $newPersentase) / 100;

            // 4. Update pengajuan_peminjaman
            $pengajuan->update([
                'persentase_bunga' => $newPersentase,
                'total_bunga' => $newTotalBunga,
            ]);

            // 5. Update kurang_bayar_bunga (sisa_bunga) directly if id_pengembalian provided
            if (!empty($validated['id_pengembalian'])) {
                PengembalianTagihanPinjaman::where('ulid', $validated['id_pengembalian'])
                    ->update(['sisa_bunga' => $validated['kurang_bayar_bunga']]);
            }

            $bulanSekarang = Carbon::now()->format('Y-m');
            // Check if ArPerbulanService needs update? It uses ArPerbulan model.
            $this->arPerbulanService->updateOrCreateAR($pengajuan->id_debitur, $bulanSekarang);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui dan Laporan Tagihan Bulanan telah di-update',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function recalculatePengembalian(
        string $idPengajuan,
        float $oldNilaiDicairkan,
        float $newNilaiDicairkan,
        float $oldTotalBunga,
        float $newTotalBunga
    ): void {
        // Get all pengembalian records ordered by created_at
        $pengembalianRecords = PengembalianTagihanPinjaman::where('id_pengajuan_peminjaman', $idPengajuan)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($pengembalianRecords->isEmpty()) {
            return;
        }

        $diffNilaiDicairkan = $newNilaiDicairkan - $oldNilaiDicairkan;
        $diffBunga = $newTotalBunga - $oldTotalBunga;

        foreach ($pengembalianRecords as $record) {
            $newSisaPokok = max(0, ($record->sisa_bayar_pokok ?? 0) + $diffNilaiDicairkan);

            $newSisaBunga = max(0, ($record->sisa_bunga ?? 0) + $diffBunga);

            $record->update([
                'total_pinjaman' => max(0, ($record->total_pinjaman ?? 0) + $diffNilaiDicairkan),
                'total_bunga' => max(0, ($record->total_bunga ?? 0) + $diffBunga),
                'sisa_bayar_pokok' => $newSisaPokok,
                'sisa_bunga' => $newSisaBunga,
            ]);

            if ($newSisaPokok <= 0 && $newSisaBunga <= 0) {
                $record->update(['status' => 'Lunas']);
            } elseif ($record->status === 'Lunas') {
                $record->update(['status' => 'Belum Lunas']);
            }
        }
    }
}
