<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use App\Models\BuktiPeminjaman;
use App\Models\JadwalAngsuran;
use App\Helpers\ListNotifSFinance;
use Carbon\Carbon;

class CheckPengembalianDanaJatuhTempoSFinance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sfinance:check-pengembalian-jatuh-tempo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek dan kirim notifikasi untuk pengembalian dana dan pembayaran restrukturisasi SFinance yang mendekati jatuh tempo atau sudah telat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan pengembalian dana SFinance jatuh tempo...');

        $today = Carbon::today();
        $daysBeforeDue = 3; // Kirim notifikasi 3 hari sebelum jatuh tempo

        // Ambil semua pengajuan peminjaman yang statusnya "Dana Sudah Dicairkan" dan belum lunas
        $pengajuanList = PengajuanPeminjaman::with(['debitur', 'buktiPeminjaman'])
            ->where('status', 'Dana Sudah Dicairkan')
            ->get();

        $countJatuhTempo = 0;
        $countTelat = 0;

        foreach ($pengajuanList as $pengajuan) {
            // Cek apakah sudah lunas
            $pengembalianTerakhir = PengembalianPinjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($pengembalianTerakhir && $pengembalianTerakhir->status === 'Lunas') {
                // Sudah lunas, skip
                continue;
            }

            // Ambil semua invoice/kontrak yang belum lunas
            $buktiPeminjamanList = $pengajuan->buktiPeminjaman;
            
            foreach ($buktiPeminjamanList as $bukti) {
                if (!$bukti->due_date) {
                    continue;
                }

                $jatuhTempo = Carbon::parse($bukti->due_date);
                
                // Cek apakah invoice/kontrak ini sudah lunas
                $labelField = $pengajuan->jenis_pembiayaan === 'Invoice Financing' 
                    ? $bukti->no_invoice 
                    : ($bukti->no_kontrak ?? $bukti->no_invoice);
                
                $totalDibayar = \App\Models\PengembalianInvoice::whereHas('pengembalianPinjaman', function ($q) use ($pengajuan, $labelField) {
                    $q->where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                        ->where('invoice_dibayarkan', $labelField);
                })->sum('nominal_yg_dibayarkan');

                $totalHarusDibayar = (float) $bukti->nilai_pinjaman + (float) $bukti->nilai_bagi_hasil;
                
                if ($totalDibayar >= $totalHarusDibayar) {
                    // Sudah lunas, skip
                    continue;
                }

                // Cek apakah sudah melewati jatuh tempo (telat)
                if ($today->gt($jatuhTempo)) {
                    // Sudah telat - kirim notifikasi telat
                    $this->info("Pengajuan peminjaman {$pengajuan->nomor_peminjaman} sudah telat (Jatuh tempo: {$jatuhTempo->format('d/m/Y')})");
                    ListNotifSFinance::pengembalianDanaTelat($pengajuan, $bukti->due_date);
                    $countTelat++;
                } 
                // Cek apakah mendekati jatuh tempo (3 hari sebelum jatuh tempo)
                elseif ($today->diffInDays($jatuhTempo) <= $daysBeforeDue && $today->lte($jatuhTempo)) {
                    // Mendekati jatuh tempo - kirim notifikasi
                    $this->info("Pengajuan peminjaman {$pengajuan->nomor_peminjaman} mendekati jatuh tempo (Jatuh tempo: {$jatuhTempo->format('d/m/Y')})");
                    ListNotifSFinance::pengembalianDanaJatuhTempo($pengajuan, $bukti->due_date);
                    $countJatuhTempo++;
                }
            }
        }

        // ============================================
        // CEK PEMBAYARAN RESTRUKTURISASI JATUH TEMPO
        // ============================================
        $this->info('Memulai pengecekan pembayaran restrukturisasi jatuh tempo...');

        $countRestrukturisasiJatuhTempo = 0;
        $countRestrukturisasiTelat = 0;

        // Ambil semua jadwal angsuran yang belum lunas
        $jadwalAngsuranList = JadwalAngsuran::with(['programRestrukturisasi.pengajuanRestrukturisasi.debitur'])
            ->where('status', '!=', 'Lunas')
            ->whereNull('bukti_pembayaran')
            ->whereNotNull('tanggal_jatuh_tempo')
            ->get();

        foreach ($jadwalAngsuranList as $jadwalAngsuran) {
            $jatuhTempo = Carbon::parse($jadwalAngsuran->tanggal_jatuh_tempo);
            
            // Cek apakah sudah melewati jatuh tempo (telat)
            if ($today->gt($jatuhTempo)) {
                // Sudah telat - kirim notifikasi telat
                $this->info("Jadwal angsuran no {$jadwalAngsuran->no} sudah telat (Jatuh tempo: {$jatuhTempo->format('d/m/Y')})");
                ListNotifSFinance::pembayaranRestrukturisasiTelat($jadwalAngsuran, $jadwalAngsuran->tanggal_jatuh_tempo);
                $countRestrukturisasiTelat++;
            } 
            // Cek apakah mendekati jatuh tempo (3 hari sebelum jatuh tempo)
            elseif ($today->diffInDays($jatuhTempo) <= $daysBeforeDue && $today->lte($jatuhTempo)) {
                // Mendekati jatuh tempo - kirim notifikasi
                $this->info("Jadwal angsuran no {$jadwalAngsuran->no} mendekati jatuh tempo (Jatuh tempo: {$jatuhTempo->format('d/m/Y')})");
                ListNotifSFinance::pembayaranRestrukturisasiJatuhTempo($jadwalAngsuran, $jadwalAngsuran->tanggal_jatuh_tempo);
                $countRestrukturisasiJatuhTempo++;
            }
        }

        $this->info("Pengecekan selesai. Notifikasi jatuh tempo pinjaman: {$countJatuhTempo}, Notifikasi telat pinjaman: {$countTelat}, Notifikasi jatuh tempo restrukturisasi: {$countRestrukturisasiJatuhTempo}, Notifikasi telat restrukturisasi: {$countRestrukturisasiTelat}");

        return Command::SUCCESS;
    }
}

