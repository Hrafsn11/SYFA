<?php

namespace App\Console\Commands;

use App\Models\PengajuanPeminjaman;
use App\Models\HistoryStatusPengajuanPinjaman;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateLamaPemakaianSFinance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sfinance:update-lama-pemakaian {--force : Force update semua data meskipun sudah diupdate hari ini}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update lama pemakaian (masa penggunaan) untuk semua pengajuan peminjaman SFinance yang aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=================================================');
        $this->info('  Update Lama Pemakaian SFinance');
        $this->info('  Tanggal: ' . now()->format('d/m/Y H:i:s'));
        $this->info('=================================================');
        $this->newLine();

        $force = $this->option('force');
        $today = Carbon::now()->startOfDay();

        // Ambil semua pengajuan dengan status Dana Sudah Dicairkan (belum lunas)
        $pengajuanList = PengajuanPeminjaman::whereIn('status', ['Dana Sudah Dicairkan', 'Aktif'])
            ->get();

        if ($pengajuanList->isEmpty()) {
            $this->warn('Tidak ada pengajuan yang perlu diupdate.');
            return Command::SUCCESS;
        }

        $this->info("Total pengajuan ditemukan: {$pengajuanList->count()}");
        $this->newLine();

        $countProcessed = 0;
        $countUpdated = 0;
        $countSkipped = 0;
        $countNoHistory = 0;

        foreach ($pengajuanList as $pengajuan) {
            $countProcessed++;

            // Ambil history pencairan
            $historyPencairan = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                ->whereNotNull('tanggal_pencairan')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$historyPencairan || !$historyPencairan->tanggal_pencairan) {
                $countNoHistory++;
                $this->line("  [SKIP] {$pengajuan->nomor_peminjaman} - Tidak ada tanggal pencairan");
                continue;
            }

            $tanggalPencairan = Carbon::parse($historyPencairan->tanggal_pencairan)->startOfDay();

            // Hitung lama pemakaian
            if ($today->lte($tanggalPencairan)) {
                $lamaPemakaian = 0;
            } else {
                $lamaPemakaian = $tanggalPencairan->diffInDays($today);
            }

            // Cek apakah perlu update
            $currentLamaPemakaian = $pengajuan->lama_pemakaian ?? 0;
            $lastUpdate = $pengajuan->last_lama_pemakaian_update 
                ? Carbon::parse($pengajuan->last_lama_pemakaian_update)->startOfDay()
                : null;

            $needsUpdate = $force || 
                $currentLamaPemakaian !== $lamaPemakaian ||
                !$lastUpdate ||
                $lastUpdate->lt($today);

            if ($needsUpdate) {
                $pengajuan->update([
                    'lama_pemakaian' => $lamaPemakaian,
                    'last_lama_pemakaian_update' => now(),
                ]);

                $countUpdated++;

                $this->line("  [UPDATED] {$pengajuan->nomor_peminjaman}");
                $this->line("    - Debitur: " . ($pengajuan->debitur->nama ?? '-'));
                $this->line("    - Tanggal Pencairan: {$tanggalPencairan->format('d/m/Y')}");
                $this->line("    - Lama Pemakaian: {$currentLamaPemakaian} -> {$lamaPemakaian} hari");
            } else {
                $countSkipped++;
            }
        }

        $this->newLine();
        $this->info('=================================================');
        $this->info('  Update Lama Pemakaian SFinance Selesai!');
        $this->info('=================================================');
        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Total Pengajuan Diproses', $countProcessed],
                ['Data Diupdate', $countUpdated],
                ['Data Tidak Berubah', $countSkipped],
                ['Tanpa Tanggal Pencairan', $countNoHistory],
            ]
        );

        // Log untuk monitoring
        Log::channel('daily')->info('SFinance Update Lama Pemakaian', [
            'total_processed' => $countProcessed,
            'updated' => $countUpdated,
            'skipped' => $countSkipped,
            'no_history' => $countNoHistory,
            'force_mode' => $force,
        ]);

        return Command::SUCCESS;
    }
}
