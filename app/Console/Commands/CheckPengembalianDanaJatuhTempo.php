<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use App\Helpers\ListNotifSFinlog;
use Carbon\Carbon;

class CheckPengembalianDanaJatuhTempo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sfinlog:check-pengembalian-jatuh-tempo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek dan kirim notifikasi untuk pengembalian dana yang mendekati jatuh tempo atau sudah telat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan pengembalian dana jatuh tempo...');

        $today = Carbon::today();
        $daysBeforeDue = 3; // Kirim notifikasi 3 hari sebelum jatuh tempo

        // Ambil semua peminjaman yang statusnya "Selesai" dan belum lunas
        $peminjamanList = PeminjamanFinlog::with(['debitur', 'pengembalianPinjaman'])
            ->where('status', 'Selesai')
            ->whereNotNull('rencana_tgl_pengembalian')
            ->get();

        $countJatuhTempo = 0;
        $countTelat = 0;

        foreach ($peminjamanList as $peminjaman) {
            $jatuhTempo = Carbon::parse($peminjaman->rencana_tgl_pengembalian);
            
            // Cek apakah sudah lunas (total pengembalian >= total pinjaman)
            $totalPengembalian = $peminjaman->pengembalianPinjaman->sum('jumlah_pengembalian') ?? 0;
            $totalPinjaman = $peminjaman->total_pinjaman ?? 0;
            
            if ($totalPengembalian >= $totalPinjaman) {
                // Sudah lunas, skip
                continue;
            }

            // Cek apakah sudah melewati jatuh tempo (telat)
            if ($today->gt($jatuhTempo)) {
                // Sudah telat - kirim notifikasi telat
                $this->info("Peminjaman {$peminjaman->nomor_peminjaman} sudah telat (Jatuh tempo: {$jatuhTempo->format('d/m/Y')})");
                ListNotifSFinlog::pengembalianDanaTelat($peminjaman);
                $countTelat++;
            } 
            // Cek apakah mendekati jatuh tempo (3 hari sebelum jatuh tempo)
            elseif ($today->diffInDays($jatuhTempo) <= $daysBeforeDue && $today->lte($jatuhTempo)) {
                // Mendekati jatuh tempo - kirim notifikasi
                $this->info("Peminjaman {$peminjaman->nomor_peminjaman} mendekati jatuh tempo (Jatuh tempo: {$jatuhTempo->format('d/m/Y')})");
                ListNotifSFinlog::pengembalianDanaJatuhTempo($peminjaman, $peminjaman->rencana_tgl_pengembalian);
                $countJatuhTempo++;
            }
        }

        $this->info("Pengecekan selesai. Notifikasi jatuh tempo: {$countJatuhTempo}, Notifikasi telat: {$countTelat}");

        return Command::SUCCESS;
    }
}

