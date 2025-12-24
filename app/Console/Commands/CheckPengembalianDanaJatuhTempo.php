<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use App\Models\PenyaluranDepositoSfinlog;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\PengembalianInvestasiFinlog;
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

        // ============================================
        // CEK PENYALURAN INVESTASI JATUH TEMPO
        // ============================================
        $this->info('Memulai pengecekan penyaluran investasi jatuh tempo...');

        $countInvestasiJatuhTempo = 0;

        // Ambil semua penyaluran investasi yang belum lunas (belum ada bukti pengembalian)
        $penyaluranInvestasiList = PenyaluranDepositoSfinlog::with(['cellsProject', 'project', 'pengajuanInvestasiFinlog'])
            ->whereNull('bukti_pengembalian')
            ->whereNotNull('tanggal_pengembalian')
            ->get();

        foreach ($penyaluranInvestasiList as $penyaluran) {
            $jatuhTempo = Carbon::parse($penyaluran->tanggal_pengembalian);
            
            // Cek apakah mendekati jatuh tempo (3 hari sebelum jatuh tempo)
            if ($today->diffInDays($jatuhTempo) <= $daysBeforeDue && $today->lte($jatuhTempo)) {
                // Mendekati jatuh tempo - kirim notifikasi
                $this->info("Penyaluran investasi ID {$penyaluran->id_penyaluran_deposito_sfinlog} mendekati jatuh tempo (Jatuh tempo: {$jatuhTempo->format('d/m/Y')})");
                ListNotifSFinlog::pengembalianInvestasiJatuhTempo($penyaluran, $penyaluran->tanggal_pengembalian);
                $countInvestasiJatuhTempo++;
            }
        }

        // ============================================
        // CEK PENGEMBALIAN INVESTASI KE INVESTOR JATUH TEMPO
        // ============================================
        $this->info('Memulai pengecekan pengembalian investasi ke investor jatuh tempo...');

        $countInvestasiKeInvestorJatuhTempo = 0;

        // Ambil semua pengajuan investasi yang sudah memiliki kontrak dan belum lunas
        $pengajuanInvestasiList = PengajuanInvestasiFinlog::with(['investor'])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->whereNotNull('tanggal_berakhir_investasi')
            ->get();

        foreach ($pengajuanInvestasiList as $pengajuan) {
            $jatuhTempo = Carbon::parse($pengajuan->tanggal_berakhir_investasi);
            
            // Cek apakah sudah lunas (total pengembalian >= nominal investasi + bagi hasil)
            $totalPengembalian = PengembalianInvestasiFinlog::getTotalDikembalikan($pengajuan->id_pengajuan_investasi_finlog);
            $totalDibayar = $totalPengembalian->total_semua ?? 0;
            $totalHarusDibayar = ($pengajuan->nominal_investasi ?? 0) + ($pengajuan->nominal_bagi_hasil_yang_didapat ?? 0);
            
            if ($totalDibayar >= $totalHarusDibayar) {
                // Sudah lunas, skip
                continue;
            }

            // Cek apakah mendekati jatuh tempo (3 hari sebelum jatuh tempo)
            if ($today->diffInDays($jatuhTempo) <= $daysBeforeDue && $today->lte($jatuhTempo)) {
                // Mendekati jatuh tempo - kirim notifikasi
                $this->info("Pengajuan investasi {$pengajuan->nomor_kontrak} mendekati jatuh tempo (Jatuh tempo: {$jatuhTempo->format('d/m/Y')})");
                ListNotifSFinlog::pengembalianInvestasiKeInvestorJatuhTempo($pengajuan, $pengajuan->tanggal_berakhir_investasi);
                $countInvestasiKeInvestorJatuhTempo++;
            }
        }

        $this->info("Pengecekan selesai. Notifikasi jatuh tempo pinjaman: {$countJatuhTempo}, Notifikasi telat pinjaman: {$countTelat}, Notifikasi jatuh tempo investasi: {$countInvestasiJatuhTempo}, Notifikasi jatuh tempo investasi ke investor: {$countInvestasiKeInvestorJatuhTempo}");

        return Command::SUCCESS;
    }
}

