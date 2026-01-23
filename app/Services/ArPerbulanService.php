<?php

namespace App\Services;

use App\Models\ArPerbulan;
use App\Models\MasterDebiturDanInvestor;
use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArPerbulanService
{
    /**
     * Update atau create AR Perbulan untuk debitur tertentu di bulan tertentu
     */
    public function updateOrCreateAR(string $id_debitur, string $bulan): ?ArPerbulan
    {
        try {
            DB::beginTransaction();

            $debitur = MasterDebiturDanInvestor::find($id_debitur);
            if (!$debitur) {
                Log::warning("ArPerbulanService: Debitur tidak ditemukan", ['id_debitur' => $id_debitur]);
                return null;
            }

            $periode = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $year = $periode->year;
            $month = $periode->month;

            $totalPinjaman = $this->calculateTotalPinjaman($id_debitur, $year, $month);
            $totalPengembalian = $this->calculateTotalPengembalian($id_debitur, $year, $month);

            $sisaArPokok = $totalPinjaman['pokok'] - $totalPengembalian['pokok'];
            $sisaBagiHasil = $totalPinjaman['bagi_hasil'] - $totalPengembalian['bagi_hasil'];
            $sisaArTotal = $sisaArPokok + $sisaBagiHasil;

            $status = $this->determineStatus($sisaArTotal);

            $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
            
            $jumlahPinjaman = PengajuanPeminjaman::where('pengajuan_peminjaman.id_debitur', $id_debitur)
                ->where('pengajuan_peminjaman.status', 'Dana Sudah Dicairkan')
                ->join('history_status_pengajuan_pinjaman', function($join) use ($endOfMonth) {
                    $join->on('pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'history_status_pengajuan_pinjaman.id_pengajuan_peminjaman')
                        ->whereNotNull('history_status_pengajuan_pinjaman.tanggal_pencairan')
                        ->whereDate('history_status_pengajuan_pinjaman.tanggal_pencairan', '<=', $endOfMonth);
                })
                ->distinct()
                ->count('pengajuan_peminjaman.id_pengajuan_peminjaman');
            $arPerbulan = ArPerbulan::updateOrCreate(
                [
                    'id_debitur' => $id_debitur,
                    'bulan' => $bulan,
                ],
                [
                    'nama_perusahaan' => $debitur->nama,
                    'periode' => $periode->toDateString(),
                    'total_pinjaman_pokok' => $totalPinjaman['pokok'],
                    'total_bagi_hasil' => $totalPinjaman['bagi_hasil'],
                    'total_pengembalian_pokok' => $totalPengembalian['pokok'],
                    'total_pengembalian_bagi_hasil' => $totalPengembalian['bagi_hasil'],
                    'sisa_ar_pokok' => $sisaArPokok,
                    'sisa_bagi_hasil' => $sisaBagiHasil,
                    'sisa_ar_total' => $sisaArTotal,
                    'jumlah_pinjaman' => $jumlahPinjaman,
                    'status' => $status,
                ]
            );

            DB::commit();

            Log::info("ArPerbulanService: AR berhasil diupdate", [
                'id_debitur' => $id_debitur,
                'bulan' => $bulan,
                'sisa_ar_total' => $sisaArTotal,
            ]);

            return $arPerbulan;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ArPerbulanService: Error saat update AR", [
                'id_debitur' => $id_debitur,
                'bulan' => $bulan,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * hitunng total pinjaman yang sudah dicairkan sampai bulan tertentu
     */
    private function calculateTotalPinjaman(string $id_debitur, int $year, int $month): array
    {
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $result = PengajuanPeminjaman::where('pengajuan_peminjaman.id_debitur', $id_debitur)
            ->where('pengajuan_peminjaman.status', 'Dana Sudah Dicairkan')
            ->join('history_status_pengajuan_pinjaman', function($join) use ($endOfMonth) {
                $join->on('pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'history_status_pengajuan_pinjaman.id_pengajuan_peminjaman')
                    ->whereNotNull('history_status_pengajuan_pinjaman.tanggal_pencairan')
                    ->whereDate('history_status_pengajuan_pinjaman.tanggal_pencairan', '<=', $endOfMonth);
            })
            ->selectRaw('
                COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_pinjaman), 0) as total_pokok,
                COALESCE(SUM(DISTINCT pengajuan_peminjaman.total_bagi_hasil), 0) as total_bagi_hasil
            ')
            ->first();

        return [
            'pokok' => $result->total_pokok ?? 0,
            'bagi_hasil' => $result->total_bagi_hasil ?? 0,
        ];
    }

    /**
     * hitung total pengembalian yang sudah dibayar sampai bulan tertentu
     * rumus: Total Dibayar = Total Pinjaman Awal - Sisa Hutang Terakhir
     */
    private function calculateTotalPengembalian(string $id_debitur, int $year, int $month): array
    {
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $pengembalianTerakhir = PengembalianPinjaman::whereHas('pengajuanPeminjaman', function ($query) use ($id_debitur) {
                $query->where('id_debitur', $id_debitur);
            })
            ->whereDate('created_at', '<=', $endOfMonth)
            ->orderBy('created_at', 'desc')
            ->get();
        $totalBayarPokok = 0;
        $totalBayarBagiHasil = 0;

        foreach ($pengembalianTerakhir->groupBy('id_pengajuan_peminjaman') as $pengajuanId => $pengembalians) {
            $terakhir = $pengembalians->first();
            $pengajuan = PengajuanPeminjaman::find($pengajuanId);
            
            if ($pengajuan) {
                $totalBayarPokok += ($pengajuan->total_pinjaman - $terakhir->sisa_bayar_pokok);
                $totalBayarBagiHasil += ($pengajuan->total_bagi_hasil - $terakhir->sisa_bagi_hasil);
            }
        }

        return [
            'pokok' => $totalBayarPokok,
            'bagi_hasil' => $totalBayarBagiHasil,
        ];
    }

    /**
     * Tentukan status AR berdasarkan sisa total
     */
    private function determineStatus(float $sisaArTotal): string
    {
        return $sisaArTotal <= 0 ? 'lunas' : 'active';
    }

    /**
     * Update AR saat pencairan dana
     */
    public function updateAROnPencairan(string $id_debitur, Carbon $tanggalPencairan): void
    {
        $bulan = $tanggalPencairan->format('Y-m');
        $this->updateOrCreateAR($id_debitur, $bulan);
    }

    /**
     * Update AR saat pengembalian pinjaman
     */
    public function updateAROnPengembalian(string $id_pengajuan_peminjaman, Carbon $tanggalPengembalian): void
    {
        $pengajuan = PengajuanPeminjaman::find($id_pengajuan_peminjaman);
        
        if (!$pengajuan) {
            Log::warning("ArPerbulanService: Pengajuan tidak ditemukan", [
                'id_pengajuan_peminjaman' => $id_pengajuan_peminjaman,
            ]);
            return;
        }

        $bulan = $tanggalPengembalian->format('Y-m');
        $this->updateOrCreateAR($pengajuan->id_debitur, $bulan);
    }

    /**
     * Archive AR yang sudah lebih dari X bulan
     */
    public function archiveOldAR(int $monthsAgo = 6): int
    {
        $cutoffDate = Carbon::now()->subMonths($monthsAgo)->startOfMonth();

        $count = ArPerbulan::where('periode', '<', $cutoffDate)
            ->where('status', '!=', 'archived')
            ->update(['status' => 'archived']);

        Log::info("ArPerbulanService: AR archived", [
            'count' => $count,
            'cutoff_date' => $cutoffDate->toDateString(),
        ]);

        return $count;
    }
}
