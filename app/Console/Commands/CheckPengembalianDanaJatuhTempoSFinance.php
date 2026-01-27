<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use App\Models\BuktiPeminjaman;
use App\Models\JadwalAngsuran;
use App\Models\PenyaluranDeposito;
use App\Models\PengajuanInvestasi;
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
    protected $description = 'Cek dan kirim notifikasi untuk pengembalian dana, pembayaran restrukturisasi, pengembalian investasi, dan pengembalian investasi ke investor SFinance yang mendekati jatuh tempo atau sudah telat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan pengembalian dana SFinance jatuh tempo...');

        $today = Carbon::today();
        $daysBeforeDue = 3; // Kirim notifikasi 3 hari sebelum jatuh tempo

        // Ambil semua pengajuan peminjaman yang statusnya "Dana Sudah Dicairkan" dan belum lunas
        $pengajuanList = PengajuanPeminjaman::with(['debitur', 'buktiPeminjaman', 'historyStatus'])
            ->where('status', 'Dana Sudah Dicairkan')
            ->get();

        $countJatuhTempo = 0;
        $countTelat = 0;
        $countBelumDimulai = 0;
        $countSP1 = 0;
        $countSP2 = 0;
        $countSP3 = 0;

        foreach ($pengajuanList as $pengajuan) {
            // Ambil history pencairan
            $history = $pengajuan->historyStatus->whereNotNull('tanggal_pencairan')
                ->sortByDesc('created_at')
                ->first();
            if (!$history || !$history->tanggal_pencairan) {
                continue;
            }

            // Cek apakah sudah lunas
            $pengembalianTerakhir = PengembalianPinjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($pengembalianTerakhir && $pengembalianTerakhir->status === 'Lunas') {
                // Sudah lunas, skip
                continue;
            }

            // Hitung hari sejak pencairan
            $daysSincePencairan = Carbon::parse($history->tanggal_pencairan)->diffInDays($today);
            $jatuhTempo = Carbon::parse($history->tanggal_pencairan)->addDays(30);
            $daySinceJatuhTempo = $today->diffInDays($jatuhTempo); // negatif jika sudah lewat jatuh tempo


            // Jika belum ada pengembalian sama sekali, kirim notifikasi
            if (!$pengembalianTerakhir) {
                if ($daysSincePencairan == 10 || $daysSincePencairan == 20 || $daysSincePencairan == 27) {
                    $this->info("Pengajuan peminjaman {$pengajuan->nomor_peminjaman} reminder pengembalian dana pada hari ke-{$daysSincePencairan}");
                    ListNotifSFinance::pengembalianDanaJatuhTempo($pengajuan, $history->tanggal_pencairan);
                    $countBelumDimulai++;
                } elseif ($daySinceJatuhTempo == 1) {
                    $this->info("Pengajuan peminjaman {$pengajuan->nomor_peminjaman} sudah telat dan belum memulai pengembalian dana");
                    ListNotifSFinance::pengembalianDanaTelat($pengajuan, $history->tanggal_pencairan);
                    $countTelat++;
                }

                $buktiPeminjamanListS = $pengajuan->buktiPeminjaman;

                foreach ($buktiPeminjamanListS as $bukti) {
                    if ($daySinceJatuhTempo == 1) {
                        ListNotifSFinance::suratPeringatanPengembalianDana($pengajuan, $history->tanggal_pencairan, 1, $bukti);
                        $countSP1++;
                    } elseif ($daySinceJatuhTempo == 91) {
                        ListNotifSFinance::suratPeringatanPengembalianDana($pengajuan, $history->tanggal_pencairan, 2, $bukti);
                        $countSP2++;
                    } elseif ($daySinceJatuhTempo == 180) {
                        ListNotifSFinance::suratPeringatanPengembalianDana($pengajuan, $history->tanggal_pencairan, 3, $bukti);
                        $countSP3++;
                    }
                }

                 // hitung hari sejak pencairan untuk SP
                continue;
            }

            // Ambil semua invoice/kontrak yang belum lunas
            $buktiPeminjamanList = $pengajuan->buktiPeminjaman;
            
            foreach ($buktiPeminjamanList as $bukti) {
                
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

                // Kirim notifikasi pada hari ke-10, 20, 3 sejak pencairan
                if ($daysSincePencairan == 10 || $daysSincePencairan == 20 || $daysSincePencairan == 27) {
                    $this->info("Pengajuan peminjaman {$pengajuan->nomor_peminjaman} reminder pengembalian dana pada hari ke-{$daysSincePencairan}");
                    ListNotifSFinance::pengembalianDanaJatuhTempo($pengajuan, $history->tanggal_pencairan);
                    $countJatuhTempo++;
                } 
                // Jika sudah lebih dari 1 hari melewati jatuh tempo, kirim telat dan SP
                elseif ($daySinceJatuhTempo == 1) {
                    $this->info("Pengajuan peminjaman {$pengajuan->nomor_peminjaman} sudah telat (Hari ke-{$daysSincePencairan})");
                    ListNotifSFinance::pengembalianDanaTelat($pengajuan, $history->tanggal_pencairan);
                    $countTelat++;
                }

                if ($daySinceJatuhTempo == 1 ) {
                    ListNotifSFinance::suratPeringatanPengembalianDana($pengajuan, $history->tanggal_pencairan, 1, $bukti);
                    $countSP1++;
                } elseif ($daySinceJatuhTempo == 91) {
                    ListNotifSFinance::suratPeringatanPengembalianDana($pengajuan, $history->tanggal_pencairan, 2, $bukti);
                    $countSP2++;
                } elseif ($daySinceJatuhTempo == 180) {
                    ListNotifSFinance::suratPeringatanPengembalianDana($pengajuan, $history->tanggal_pencairan, 3, $bukti);
                    $countSP3++;
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

        // ============================================
        // CEK PENGEMBALIAN INVESTASI JATUH TEMPO
        // ============================================
        $this->info('Memulai pengecekan pengembalian investasi jatuh tempo...');

        $countInvestasiJatuhTempo = 0;

        // Ambil semua penyaluran deposito yang belum dikembalikan (belum ada nominal_yang_dikembalikan)
        $penyaluranList = PenyaluranDeposito::with(['debitur', 'pengajuanInvestasi'])
            ->whereNull('nominal_yang_dikembalikan')
            ->whereNotNull('tanggal_pengembalian')
            ->get();

        foreach ($penyaluranList as $penyaluran) {
            $jatuhTempo = Carbon::parse($penyaluran->tanggal_pengembalian);
            
            // Cek apakah mendekati jatuh tempo (3 hari sebelum jatuh tempo)
            // Tidak perlu cek telat karena hanya perlu notifikasi sebelum jatuh tempo
            if ($today->diffInDays($jatuhTempo) <= $daysBeforeDue && $today->lte($jatuhTempo)) {
                // Mendekati jatuh tempo - kirim notifikasi
                $this->info("Penyaluran deposito ID {$penyaluran->id_penyaluran_deposito} mendekati jatuh tempo (Jatuh tempo: {$jatuhTempo->format('d/m/Y')})");
                ListNotifSFinance::pengembalianInvestasiJatuhTempo($penyaluran, $penyaluran->tanggal_pengembalian);
                $countInvestasiJatuhTempo++;
            }
        }

        // CEK PENGEMBALIAN INVESTASI KE INVESTOR JATUH TEMPO
        $this->info('Memulai pengecekan pengembalian investasi ke investor jatuh tempo...');

        $countInvestasiKeInvestorJatuhTempo = 0;

        // Ambil semua pengajuan investasi yang statusnya "Selesai" dan belum lunas (masih ada sisa_pokok atau sisa_bagi_hasil)
        $pengajuanInvestasiList = PengajuanInvestasi::with('investor')
            ->where('status', 'Selesai')
            ->where(function ($query) {
                $query->where('sisa_pokok', '>', 0)
                      ->orWhere('sisa_bagi_hasil', '>', 0);
            })
            ->whereNotNull('tanggal_investasi')
            ->get();

        foreach ($pengajuanInvestasiList as $pengajuan) {
            // Hitung tanggal jatuh tempo berdasarkan jenis deposito
            $tanggalInvestasi = Carbon::parse($pengajuan->tanggal_investasi);
            
            if ($pengajuan->deposito === 'Reguler') {
                // Regular: Always 31 December of investment year
                $tanggalJatuhTempo = Carbon::createFromDate($tanggalInvestasi->year, 12, 31);
            } else {
                // Khusus: tanggal_investasi + lama_investasi months
                $tanggalJatuhTempo = $tanggalInvestasi->copy()->addMonths($pengajuan->lama_investasi);
            }

            // Cek apakah mendekati jatuh tempo (3 hari sebelum jatuh tempo)
            if ($today->diffInDays($tanggalJatuhTempo) <= $daysBeforeDue && $today->lte($tanggalJatuhTempo)) {
                // Mendekati jatuh tempo - kirim notifikasi
                $this->info("Pengajuan investasi {$pengajuan->id_pengajuan_investasi} mendekati jatuh tempo (Jatuh tempo: {$tanggalJatuhTempo->format('d/m/Y')})");
                ListNotifSFinance::pengembalianInvestasiKeInvestorJatuhTempo($pengajuan, $tanggalJatuhTempo->toDateString());
                $countInvestasiKeInvestorJatuhTempo++;
            }
        }

        $this->info("Pengecekan selesai. Notifikasi jatuh tempo pinjaman: {$countJatuhTempo}, Notifikasi telat pinjaman: {$countTelat}, Notifikasi SP1: {$countSP1}, SP2: {$countSP2}, SP3: {$countSP3}, Notifikasi belum dimulai: {$countBelumDimulai}, Notifikasi jatuh tempo restrukturisasi: {$countRestrukturisasiJatuhTempo}, Notifikasi telat restrukturisasi: {$countRestrukturisasiTelat}, Notifikasi jatuh tempo investasi: {$countInvestasiJatuhTempo}, Notifikasi jatuh tempo investasi ke investor: {$countInvestasiKeInvestorJatuhTempo}");

        return Command::SUCCESS;
    }
}

