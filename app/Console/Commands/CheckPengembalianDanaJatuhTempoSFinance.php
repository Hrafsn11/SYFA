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

        // ============================================
        // CEK PENGEMBALIAN INVESTASI JATUH TEMPO
        // ============================================
        $this->info('Memulai pengecekan pengembalian investasi jatuh tempo...');

        $countInvestasiJatuhTempo = 0;

        // Ambil semua penyaluran deposito yang belum dikembalikan (belum ada bukti_pengembalian)
        $penyaluranList = PenyaluranDeposito::with(['debitur', 'pengajuanInvestasi'])
            ->whereNull('bukti_pengembalian')
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

        $this->info("Pengecekan selesai. Notifikasi jatuh tempo pinjaman: {$countJatuhTempo}, Notifikasi telat pinjaman: {$countTelat}, Notifikasi jatuh tempo restrukturisasi: {$countRestrukturisasiJatuhTempo}, Notifikasi telat restrukturisasi: {$countRestrukturisasiTelat}, Notifikasi jatuh tempo investasi: {$countInvestasiJatuhTempo}, Notifikasi jatuh tempo investasi ke investor: {$countInvestasiKeInvestorJatuhTempo}");

        return Command::SUCCESS;
    }
}

