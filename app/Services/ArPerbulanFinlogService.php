<?php

namespace App\Services;

use App\Models\ArPerbulanFinlog;
use App\Models\MasterDebiturDanInvestor;
use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArPerbulanFinlogService
{
    /**
     * Update atau create AR Perbulan untuk debitur tertentu di bulan tertentu
     */
    public function updateOrCreateAR(string $id_debitur, string $bulan): ?ArPerbulanFinlog
    {
        try {
            DB::beginTransaction();

            $debitur = MasterDebiturDanInvestor::find($id_debitur);
            if (!$debitur) {
                Log::warning("ArPerbulanFinlogService: Debitur tidak ditemukan", ['id_debitur' => $id_debitur]);
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
            $jumlahPinjaman = PeminjamanFinlog::where('id_debitur', $id_debitur)
                ->where('status', 'Selesai')
                ->whereDate('created_at', '<=', $endOfMonth)
                ->count();

            $arPerbulan = ArPerbulanFinlog::updateOrCreate(
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

            Log::info("ArPerbulanFinlogService: AR berhasil diupdate", [
                'id_debitur' => $id_debitur,
                'bulan' => $bulan,
                'sisa_ar_total' => $sisaArTotal,
            ]);

            return $arPerbulan;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ArPerbulanFinlogService: Error saat update AR", [
                'id_debitur' => $id_debitur,
                'bulan' => $bulan,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Hitung total pinjaman yang status nya "Selesai" sampai bulan tertentu
     */
    private function calculateTotalPinjaman(string $id_debitur, int $year, int $month): array
    {
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $result = PeminjamanFinlog::where('id_debitur', $id_debitur)
            ->where('status', 'Selesai')
            ->whereDate('created_at', '<=', $endOfMonth)
            ->selectRaw('
                COALESCE(SUM(nilai_pinjaman), 0) as total_pokok,
                COALESCE(SUM(nilai_bagi_hasil), 0) as total_bagi_hasil
            ')
            ->first();

        return [
            'pokok' => $result->total_pokok ?? 0,
            'bagi_hasil' => $result->total_bagi_hasil ?? 0,
        ];
    }

    /**
     * Hitung total pengembalian yang sudah dibayar sampai bulan tertentu
     * Rumus: Total Dibayar = Total Pinjaman Awal - Sisa Hutang Terakhir
     */
    private function calculateTotalPengembalian(string $id_debitur, int $year, int $month): array
    {
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        try {
            $hasData = PengembalianPinjamanFinlog::exists();
            
            if (!$hasData) {
                return [
                    'pokok' => 0,
                    'bagi_hasil' => 0,
                ];
            }

            $pengembalianTerakhir = PengembalianPinjamanFinlog::whereHas('peminjamanFinlog', function ($query) use ($id_debitur) {
                    $query->where('id_debitur', $id_debitur);
                })
                ->whereDate('created_at', '<=', $endOfMonth)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalBayarPokok = 0;
            $totalBayarBagiHasil = 0;

            foreach ($pengembalianTerakhir->groupBy('id_pinjaman_finlog') as $pinjamanId => $pengembalians) {
                $terakhir = $pengembalians->first();
                $pinjaman = PeminjamanFinlog::find($pinjamanId);
                
                if ($pinjaman && $terakhir) {
                    $totalBayarPokok += max(0, ($pinjaman->nilai_pinjaman - $terakhir->sisa_pinjaman));
                    $totalBayarBagiHasil += max(0, ($pinjaman->nilai_bagi_hasil - $terakhir->sisa_bagi_hasil));
                }
            }

            return [
                'pokok' => $totalBayarPokok,
                'bagi_hasil' => $totalBayarBagiHasil,
            ];
        } catch (\Exception $e) {
            Log::error("ArPerbulanFinlogService: Error calculate pengembalian", [
                'error' => $e->getMessage(),
            ]);
            return [
                'pokok' => 0,
                'bagi_hasil' => 0,
            ];
        }
    }

    /**
     * Tentukan status AR berdasarkan sisa total
     */
    private function determineStatus(float $sisaArTotal): string
    {
        return $sisaArTotal <= 0 ? 'lunas' : 'active';
    }

    /**
     * Update AR saat peminjaman status berubah ke "Selesai"
     */
    public function updateAROnSelesai(string $id_debitur, Carbon $tanggal): void
    {
        $bulan = $tanggal->format('Y-m');
        $this->updateOrCreateAR($id_debitur, $bulan);
    }

    /**
     * Update AR saat pengembalian pinjaman
     */
    public function updateAROnPengembalian(string $id_peminjaman_finlog, Carbon $tanggalPengembalian): void
    {
        $peminjaman = PeminjamanFinlog::find($id_peminjaman_finlog);
        
        if (!$peminjaman) {
            Log::warning("ArPerbulanFinlogService: Peminjaman tidak ditemukan", [
                'id_peminjaman_finlog' => $id_peminjaman_finlog,
            ]);
            return;
        }

        $bulan = $tanggalPengembalian->format('Y-m');
        $this->updateOrCreateAR($peminjaman->id_debitur, $bulan);
    }

    /**
     * Archive AR yang sudah lebih dari X bulan
     */
    public function archiveOldAR(int $monthsAgo = 6): int
    {
        $cutoffDate = Carbon::now()->subMonths($monthsAgo)->startOfMonth();

        $count = ArPerbulanFinlog::where('periode', '<', $cutoffDate)
            ->where('status', '!=', 'archived')
            ->update(['status' => 'archived']);

        Log::info("ArPerbulanFinlogService: AR archived", [
            'count' => $count,
            'cutoff_date' => $cutoffDate->toDateString(),
        ]);

        return $count;
    }
}
