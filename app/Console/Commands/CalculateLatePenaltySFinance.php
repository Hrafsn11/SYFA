<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalculateLatePenaltySFinance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sfinance:calculate-late-penalty {--force : Force recalculation for all records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghitung dan menyimpan jumlah bulan keterlambatan, denda keterlambatan, dan nilai bagi hasil saat ini untuk peminjaman SFinance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=================================================');
        $this->info('Memulai perhitungan denda keterlambatan SFinance...');
        $this->info('Waktu: ' . now()->format('Y-m-d H:i:s'));
        $this->info('=================================================');

        $today = Carbon::today();
        $force = $this->option('force');

        $pengajuanList = PengajuanPeminjaman::with(['historyStatus', 'debitur'])
            ->where('status', 'Dana Sudah Dicairkan')
            ->get();

        $countUpdated = 0;
        $countTerlambat = 0;
        $countSkipped = 0;
        $totalDendaAdded = 0;

        foreach ($pengajuanList as $pengajuan) {
            // Ambil tanggal pencairan dari history
            $historyPencairan = $pengajuan->historyStatus
                ->whereNotNull('tanggal_pencairan')
                ->sortByDesc('created_at')
                ->first();

            if (!$historyPencairan || !$historyPencairan->tanggal_pencairan) {
                $countSkipped++;
                continue;
            }

            // Hitung jatuh tempo: tanggal pencairan + 30 hari (atau dari tanggal_jatuh_tempo jika ada)
            $tanggalPencairan = Carbon::parse($historyPencairan->tanggal_pencairan);
            $jatuhTempo = $pengajuan->tanggal_jatuh_tempo 
                ? Carbon::parse($pengajuan->tanggal_jatuh_tempo)
                : $tanggalPencairan->copy()->addDays(30);

            // Ambil sisa pokok dan sisa bagi hasil dari pengembalian terakhir atau nilai awal
            $pengembalianTerakhir = PengembalianPinjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                ->orderBy('created_at', 'desc')
                ->first();

            // Jika sudah lunas, skip
            if ($pengembalianTerakhir && $pengembalianTerakhir->status === 'Lunas') {
                // Reset denda jika ada
                if ($pengajuan->jumlah_bulan_keterlambatan > 0 || $pengajuan->denda_keterlambatan > 0) {
                    $pengajuan->update([
                        'jumlah_bulan_keterlambatan' => 0,
                        'denda_keterlambatan' => 0,
                        'total_bunga_saat_ini' => $pengajuan->total_bunga,
                        'last_penalty_calculation' => now(),
                    ]);
                    $this->line("  [LUNAS] {$pengajuan->nomor_peminjaman} - Reset denda keterlambatan");
                }
                $countSkipped++;
                continue;
            }

            // Hitung sisa pokok dan bagi hasil
            $sisaPokok = $pengembalianTerakhir 
                ? $pengembalianTerakhir->sisa_bayar_pokok 
                : $pengajuan->total_pinjaman;
            
            // Untuk bagi hasil, ambil dari pengembalian terakhir TANPA denda yang sudah dihitung
            $sisaBagiHasilAwal = $pengembalianTerakhir 
                ? $pengembalianTerakhir->sisa_bunga 
                : $pengajuan->total_bunga;
            
            // Kurangi denda yang sudah ada sebelumnya untuk mendapat sisa bagi hasil murni
            $dendaSebelumnya = $pengajuan->denda_keterlambatan ?? 0;
            $sisaBagiHasilTanpaDenda = max(0, $sisaBagiHasilAwal - $dendaSebelumnya);

            $totalSisa = $sisaPokok + $sisaBagiHasilTanpaDenda;

            // Jika total sisa = 0, sudah lunas
            if ($totalSisa <= 0) {
                if ($pengajuan->jumlah_bulan_keterlambatan > 0 || $pengajuan->denda_keterlambatan > 0) {
                    $pengajuan->update([
                        'jumlah_bulan_keterlambatan' => 0,
                        'denda_keterlambatan' => 0,
                        'total_bunga_saat_ini' => 0,
                        'last_penalty_calculation' => now(),
                    ]);
                    $this->line("  [LUNAS] {$pengajuan->nomor_peminjaman} - Reset denda keterlambatan");
                }
                $countSkipped++;
                continue;
            }

            // Cek apakah sudah melewati jatuh tempo
            if ($today->gt($jatuhTempo)) {
                // Hitung jumlah bulan keterlambatan total dari jatuh tempo
                $hariKeterlambatan = $jatuhTempo->diffInDays($today);
                $bulanKeterlambatanTotal = (int) ceil($hariKeterlambatan / 30);

                // Cek apakah ada pengembalian setelah jatuh tempo (sudah dihitung denda sebelumnya)
                $pengembalianSetelahJatuhTempo = PengembalianPinjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                    ->where('created_at', '>', $jatuhTempo)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $bulanKeterlambatanYangSudahDihitung = 0;
                if ($pengembalianSetelahJatuhTempo) {
                    // Hitung berapa bulan keterlambatan yang sudah dihitung pada pengembalian terakhir
                    $hariTerlambatSaatPengembalian = $jatuhTempo->diffInDays($pengembalianSetelahJatuhTempo->created_at);
                    $bulanKeterlambatanYangSudahDihitung = (int) ceil($hariTerlambatSaatPengembalian / 30);
                }

                // Selisih bulan = bulan total - bulan yang sudah dihitung sebelumnya
                $selisihBulan = max(0, $bulanKeterlambatanTotal - $bulanKeterlambatanYangSudahDihitung);

                // Hitung denda keterlambatan baru
                // Rumus: Sisa Pokok × (Persentase Bagi Hasil / 100) × Selisih Bulan
                $persentaseBagiHasil = (float) ($pengajuan->persentase_bunga ?? 0);
                $dendaKeterlambatanBaru = round($sisaPokok * ($persentaseBagiHasil / 100) * $selisihBulan);

                // Total denda = denda sebelumnya + denda baru
                $totalDendaKeterlambatan = $dendaSebelumnya + $dendaKeterlambatanBaru;

                // Hitung total bagi hasil saat ini = sisa bagi hasil tanpa denda + total denda
                $totalBagiHasilSaatIni = $sisaBagiHasilTanpaDenda + $totalDendaKeterlambatan;

                // Cek apakah ada perubahan
                $hasChanged = $force ||
                    $pengajuan->jumlah_bulan_keterlambatan !== $bulanKeterlambatanTotal ||
                    abs(($pengajuan->denda_keterlambatan ?? 0) - $totalDendaKeterlambatan) > 0.01;

                if ($hasChanged && $dendaKeterlambatanBaru > 0) {
                    $oldDenda = $pengajuan->denda_keterlambatan ?? 0;

                    // Update ke pengajuan_peminjaman
                    $pengajuan->update([
                        'jumlah_bulan_keterlambatan' => $bulanKeterlambatanTotal,
                        'denda_keterlambatan' => $totalDendaKeterlambatan,
                        'total_bunga_saat_ini' => $totalBagiHasilSaatIni,
                        'sisa_bunga' => $totalBagiHasilSaatIni, 
                        'last_penalty_calculation' => now(),
                    ]);

                    $totalDendaAdded += $dendaKeterlambatanBaru;
                    $countUpdated++;
                    $countTerlambat++;

                    $this->line("  [TERLAMBAT] {$pengajuan->nomor_peminjaman}");
                    $this->line("    - Debitur: " . ($pengajuan->debitur->nama ?? '-'));
                    $this->line("    - Tanggal Pencairan: {$tanggalPencairan->format('d/m/Y')}");
                    $this->line("    - Jatuh Tempo: {$jatuhTempo->format('d/m/Y')}");
                    $this->line("    - Hari Keterlambatan: {$hariKeterlambatan} hari");
                    $this->line("    - Bulan Keterlambatan: {$bulanKeterlambatanTotal} bulan");
                    $this->line("    - Selisih Bulan (baru): {$selisihBulan}");
                    $this->line("    - Persentase Bagi Hasil: {$persentaseBagiHasil}%");
                    $this->line("    - Sisa Pokok: Rp " . number_format($sisaPokok, 0, ',', '.'));
                    $this->line("    - Denda Baru: Rp " . number_format($dendaKeterlambatanBaru, 0, ',', '.'));
                    $this->line("    - Total Denda: Rp " . number_format($totalDendaKeterlambatan, 0, ',', '.'));
                    $this->line("    - Total Bagi Hasil Saat Ini: Rp " . number_format($totalBagiHasilSaatIni, 0, ',', '.'));

                    // Log untuk audit trail
                    Log::channel('daily')->info('SFinance Late Penalty Calculated', [
                        'pengajuan_id' => $pengajuan->id_pengajuan_peminjaman,
                        'nomor_peminjaman' => $pengajuan->nomor_peminjaman,
                        'debitur' => $pengajuan->debitur->nama ?? '-',
                        'tanggal_pencairan' => $tanggalPencairan->toDateString(),
                        'jatuh_tempo' => $jatuhTempo->toDateString(),
                        'hari_keterlambatan' => $hariKeterlambatan,
                        'bulan_keterlambatan' => $bulanKeterlambatanTotal,
                        'selisih_bulan' => $selisihBulan,
                        'persentase_bunga' => $persentaseBagiHasil,
                        'sisa_pokok' => $sisaPokok,
                        'denda_baru' => $dendaKeterlambatanBaru,
                        'total_denda' => $totalDendaKeterlambatan,
                        'total_bunga_saat_ini' => $totalBagiHasilSaatIni,
                        'calculated_at' => now()->toDateTimeString(),
                    ]);
                } elseif ($bulanKeterlambatanTotal > 0 && !$hasChanged) {
                    $countSkipped++;
                } else {
                    $countSkipped++;
                }
            } else {
                // Belum jatuh tempo, pastikan tidak ada denda
                if ($pengajuan->jumlah_bulan_keterlambatan > 0 || $pengajuan->denda_keterlambatan > 0) {
                    $pengajuan->update([
                        'jumlah_bulan_keterlambatan' => 0,
                        'denda_keterlambatan' => 0,
                        'total_bunga_saat_ini' => $pengajuan->total_bunga,
                        'sisa_bunga' => $sisaBagiHasilAwal,
                        'last_penalty_calculation' => now(),
                    ]);
                    $this->line("  [RESET] {$pengajuan->nomor_peminjaman} - Belum jatuh tempo, reset denda");
                    $countUpdated++;
                } else {
                    $countSkipped++;
                }
            }
        }

        $this->newLine();
        $this->info('=================================================');
        $this->info('Perhitungan denda keterlambatan SFinance selesai!');
        $this->info('=================================================');
        $this->table(
            ['Metrik', 'Nilai'],
            [
                ['Total Pengajuan Diproses', $pengajuanList->count()],
                ['Pengajuan Terlambat', $countTerlambat],
                ['Data Diupdate', $countUpdated],
                ['Data Tidak Berubah/Lunas', $countSkipped],
                ['Total Denda Ditambahkan', 'Rp ' . number_format($totalDendaAdded, 0, ',', '.')],
            ]
        );

        return Command::SUCCESS;
    }
}
