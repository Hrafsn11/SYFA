<?php

namespace App\Http\Controllers;

use App\Models\BuktiPeminjaman;
use App\Models\HistoryStatusPengajuanPinjaman;
use App\Models\PengajuanPeminjaman;
use App\Services\ArPerbulanService;
use App\Services\DebiturPiutangService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebiturPiutangController extends Controller
{
    public function __construct(
        private readonly DebiturPiutangService $service,
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
        // Check permission
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
            'persentase_bagi_hasil' => 'required|numeric|min:0|max:100',
            'kurang_bayar_bagi_hasil' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Get old values for comparison
            $pengajuan = PengajuanPeminjaman::where('id_pengajuan_peminjaman', $validated['id_pengajuan'])->first();

            if (!$pengajuan) {
                throw new \Exception('Data pengajuan tidak ditemukan');
            }

            $oldNilaiDicairkan = 0;
            if ($validated['id_history']) {
                $oldHistory = HistoryStatusPengajuanPinjaman::where('id_history_status_pengajuan_pinjaman', $validated['id_history'])->first();
                $oldNilaiDicairkan = $oldHistory->nominal_yang_disetujui ?? $pengajuan->total_pinjaman;
            }
            $oldPersentase = $pengajuan->persentase_bagi_hasil ?? 0;
            $oldTotalBagiHasil = $pengajuan->total_bagi_hasil ?? 0;

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

            // 3. Calculate new total_bagi_hasil
            $newNilaiDicairkan = $validated['nilai_dicairkan'];
            $newPersentase = $validated['persentase_bagi_hasil'];
            $newTotalBagiHasil = ($newNilaiDicairkan * $newPersentase) / 100;

            // 4. Update pengajuan_peminjaman
            $pengajuan->update([
                'persentase_bagi_hasil' => $newPersentase,
                'total_bagi_hasil' => $newTotalBagiHasil,
            ]);

            // 5. Update kurang_bayar_bagi_hasil (sisa_bagi_hasil) directly if id_pengembalian provided
            if (!empty($validated['id_pengembalian'])) {
                \App\Models\PengembalianPinjaman::where('ulid', $validated['id_pengembalian'])
                    ->update(['sisa_bagi_hasil' => $validated['kurang_bayar_bagi_hasil']]);
            }

            $bulanSekarang = Carbon::now()->format('Y-m');
            $this->arPerbulanService->updateOrCreateAR($pengajuan->id_debitur, $bulanSekarang);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui dan AR Perbulan telah di-update',
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
        float $oldTotalBagiHasil,
        float $newTotalBagiHasil
    ): void {
        // Get all pengembalian records ordered by created_at
        $pengembalianRecords = \App\Models\PengembalianPinjaman::where('id_pengajuan_peminjaman', $idPengajuan)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($pengembalianRecords->isEmpty()) {
            return;
        }

        $diffNilaiDicairkan = $newNilaiDicairkan - $oldNilaiDicairkan;
        $diffBagiHasil = $newTotalBagiHasil - $oldTotalBagiHasil;

        foreach ($pengembalianRecords as $record) {
            $newSisaPokok = max(0, ($record->sisa_bayar_pokok ?? 0) + $diffNilaiDicairkan);

            $newSisaBagiHasil = max(0, ($record->sisa_bagi_hasil ?? 0) + $diffBagiHasil);

            $record->update([
                'total_pinjaman' => max(0, ($record->total_pinjaman ?? 0) + $diffNilaiDicairkan),
                'total_bagi_hasil' => max(0, ($record->total_bagi_hasil ?? 0) + $diffBagiHasil),
                'sisa_bayar_pokok' => $newSisaPokok,
                'sisa_bagi_hasil' => $newSisaBagiHasil,
            ]);

            if ($newSisaPokok <= 0 && $newSisaBagiHasil <= 0) {
                $record->update(['status' => 'Lunas']);
            } elseif ($record->status === 'Lunas') {
                $record->update(['status' => 'Belum Lunas']);
            }
        }
    }
}
