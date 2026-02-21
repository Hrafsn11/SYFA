<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalculateLatePenaltyFinlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sfinlog:calculate-late-penalty {--force : Force recalculation for all records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghitung dan menyimpan jumlah minggu keterlambatan, denda keterlambatan, dan nilai bagi hasil saat ini untuk peminjaman Finlog';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=================================================');
        $this->info('Memulai perhitungan denda keterlambatan SFinlog...');
        $this->info('Waktu: ' . now()->format('Y-m-d H:i:s'));
        $this->info('=================================================');

        $today = Carbon::today();
        $force = $this->option('force');

        // Ambil semua peminjaman yang statusnya "Selesai" dan belum lunas
        $query = PeminjamanFinlog::with(['pengembalianPinjaman'])
            ->where('status', 'Selesai')
            ->whereNotNull('rencana_tgl_pengembalian');

        $peminjamanList = $query->get();

        $countUpdated = 0;
        $countTerlambat = 0;
        $countSkipped = 0;
        $totalDendaAdded = 0;

        foreach ($peminjamanList as $peminjaman) {
            $jatuhTempo = Carbon::parse($peminjaman->rencana_tgl_pengembalian);

            // Cek apakah sudah lunas berdasarkan sisa pokok dan bagi hasil
            $sisaPokok = $peminjaman->nilai_pokok_saat_ini ?? $peminjaman->nilai_pinjaman;
            $sisaBagiHasil = $peminjaman->nilai_bunga_saat_ini ?? $peminjaman->nilai_bunga;
            $totalSisa = $sisaPokok + $sisaBagiHasil;

            // Jika sudah lunas (sisa = 0), reset nilai keterlambatan
            if ($totalSisa <= 0) {
                if ($peminjaman->jumlah_minggu_keterlambatan > 0 || $peminjaman->denda_keterlambatan > 0) {
                    $peminjaman->update([
                        'jumlah_minggu_keterlambatan' => 0,
                        'denda_keterlambatan' => 0,
                        'last_penalty_calculation' => now(),
                    ]);
                    $this->line("  [LUNAS] {$peminjaman->nomor_peminjaman} - Reset denda keterlambatan");
                }
                $countSkipped++;
                continue;
            }

            // Cek apakah sudah melewati jatuh tempo
            if ($today->gt($jatuhTempo)) {
                // Hitung jumlah minggu keterlambatan
                $mingguKeterlambatan = abs($today->diffInWeeks($jatuhTempo));

                // Hitung pembagi berdasarkan TOP
                $pembagi = $this->getPembagiBerdasarkanTOP($peminjaman->top ?? 0);

                // Hitung bagi hasil per minggu (berdasarkan nilai bagi hasil awal)
                $bagiHasilPerMinggu = $peminjaman->nilai_bunga / $pembagi;

                // Hitung denda keterlambatan (bagi hasil tambahan)
                $dendaKeterlambatan = $bagiHasilPerMinggu * $mingguKeterlambatan;

                // Hitung nilai bagi hasil saat ini:
                // = sisa bagi hasil yang belum dibayar + denda keterlambatan
                // Jika belum ada pembayaran, gunakan nilai_bunga awal
                $sisaBagiHasilSebelumDenda = $sisaBagiHasil - ($peminjaman->denda_keterlambatan ?? 0);
                $nilaiBagiHasilSaatIni = $sisaBagiHasilSebelumDenda + $dendaKeterlambatan;

                // Cek apakah ada perubahan
                $hasChanged = $force ||
                    $peminjaman->jumlah_minggu_keterlambatan !== $mingguKeterlambatan ||
                    abs(($peminjaman->denda_keterlambatan ?? 0) - $dendaKeterlambatan) > 0.01;

                if ($hasChanged) {
                    $oldDenda = $peminjaman->denda_keterlambatan ?? 0;

                    $peminjaman->update([
                        'jumlah_minggu_keterlambatan' => $mingguKeterlambatan,
                        'denda_keterlambatan' => $dendaKeterlambatan,
                        'nilai_bunga_saat_ini' => $nilaiBagiHasilSaatIni,
                        'last_penalty_calculation' => now(),
                    ]);

                    $dendaDiff = $dendaKeterlambatan - $oldDenda;
                    $totalDendaAdded += $dendaDiff;
                    $countUpdated++;
                    $countTerlambat++;

                    $this->line("  [TERLAMBAT] {$peminjaman->nomor_peminjaman}");
                    $this->line("    - Minggu Keterlambatan: {$mingguKeterlambatan}");
                    $this->line("    - Denda: Rp " . number_format($dendaKeterlambatan, 0, ',', '.'));
                    $this->line("    - Bagi Hasil Saat Ini: Rp " . number_format($nilaiBagiHasilSaatIni, 0, ',', '.'));

                    // Log untuk audit trail
                    Log::channel('daily')->info('Late Penalty Calculated', [
                        'peminjaman_id' => $peminjaman->id_peminjaman_finlog,
                        'nomor_peminjaman' => $peminjaman->nomor_peminjaman,
                        'minggu_keterlambatan' => $mingguKeterlambatan,
                        'denda_keterlambatan' => $dendaKeterlambatan,
                        'nilai_bunga_saat_ini' => $nilaiBagiHasilSaatIni,
                        'calculated_at' => now()->toDateTimeString(),
                    ]);
                } else {
                    $countSkipped++;
                }
            } else {
                // Belum jatuh tempo, pastikan tidak ada denda
                if ($peminjaman->jumlah_minggu_keterlambatan > 0 || $peminjaman->denda_keterlambatan > 0) {
                    $peminjaman->update([
                        'jumlah_minggu_keterlambatan' => 0,
                        'denda_keterlambatan' => 0,
                        'nilai_bunga_saat_ini' => $peminjaman->nilai_bunga,
                        'last_penalty_calculation' => now(),
                    ]);
                    $this->line("  [RESET] {$peminjaman->nomor_peminjaman} - Belum jatuh tempo, reset denda");
                    $countUpdated++;
                } else {
                    $countSkipped++;
                }
            }
        }

        $this->newLine();
        $this->info('=================================================');
        $this->info('Perhitungan denda keterlambatan selesai!');
        $this->info('=================================================');
        $this->table(
            ['Metrik', 'Nilai'],
            [
                ['Total Peminjaman Diproses', $peminjamanList->count()],
                ['Peminjaman Terlambat', $countTerlambat],
                ['Data Diupdate', $countUpdated],
                ['Data Tidak Berubah/Lunas', $countSkipped],
                ['Total Denda Ditambahkan', 'Rp ' . number_format($totalDendaAdded, 0, ',', '.')],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Menghitung pembagi bagi hasil berdasarkan TOP (hari)
     * Konsisten dengan logika di DebiturPiutangFinlogTable
     * 
     * @param int $top TOP dalam hari
     * @return int Pembagi untuk perhitungan bagi hasil per minggu
     */
    private function getPembagiBerdasarkanTOP(int $top): int
    {
        if ($top >= 1 && $top <= 7) {
            return 1;
        } elseif ($top >= 8 && $top <= 14) {
            return 2;
        } elseif ($top >= 15 && $top <= 21) {
            return 3;
        } else {
            // TOP 22 dan seterusnya (termasuk 30 hari = 4 minggu)
            return 4;
        }
    }
}
