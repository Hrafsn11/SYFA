<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\NotificationFeature;

class ListNotifSFinance
{
    public static function menuPeminjaman($status, $peminjaman, $nominal = 0)
    {
        if($status === 'Submit Dokumen') {
            $notif = NotificationFeature::where('name', 'submit_review_peminjaman')->first();
        } else if($status === 'Dokumen Tervalidasi') {
            $notif = NotificationFeature::where('name', 'pengajuan_disetujui_finance_ski')->first();
        } else if($status === 'Validasi Ditolak') {
            $notif = NotificationFeature::where('name', 'pengajuan_ditolak_finance_ski')->first();
        } else if($status === 'Dana Sudah Dicairkan') {
            $notif = NotificationFeature::where('name', 'pencairan_dana')->first();
        } else if($status === 'Debitur Setuju') {
            $notif = NotificationFeature::where('name', 'nominal_disetujui_debitur')->first();
        } else if($status === 'Pengajuan Ditolak Debitur') {
            $notif = NotificationFeature::where('name', 'nominal_ditolak_debitur')->first();
        } else if($status === 'Disetujui oleh CEO SKI') {
            $notif = NotificationFeature::where('name', 'pengajuan_disetujui_oleh_ceo_ski')->first();
        } else if($status === 'Ditolak oleh CEO SKI') {
            $notif = NotificationFeature::where('name', 'pengajuan_ditolak_oleh_ceo_ski')->first();
        } else if($status === 'Disetujui oleh Direktur SKI') {
            $notif = NotificationFeature::where('name', 'pengajuan_disetujui_oleh_direktur_ski')->first();
        } else if($status === 'Ditolak oleh Direktur SKI') {
            $notif = NotificationFeature::where('name', 'pengajuan_ditolak_oleh_direktur_ski')->first();
        } else if($status === 'Generate Kontrak') {
            $notif = NotificationFeature::where('name', 'generate_kontrak')->first();
        } else if($status === 'Menunggu Konfirmasi Debitur') {
            $notif = NotificationFeature::where('name', 'sk_finance_upload_bukti_transfer')->first();
        } else if($status === 'Konfirmasi Ditolak Debitur') {
            $notif = NotificationFeature::where('name', 'sk_finance_konfirmasi_ditolak_debitur')->first();
        }


        $notif_variable = [
            '[[nama.debitur]]' => $peminjaman->debitur->nama ?? null,
            '[[nominal]]' => number_format($nominal, 0, ',', '.'),
        ];

        $link = route('peminjaman.detail', $peminjaman->id_pengajuan_peminjaman);

        if($notif) {
            $data = [
                'notif_variable' => $notif_variable,
                'link' => $link,
                'notif' => $notif,
                'id_debitur' => $peminjaman->id_debitur,
                'id_investor' => null,
            ];
            sendNotification($data);
        }
    }

    public static function pengembalianDana($pengembalian)
    {
        // Notifikasi saat debitur melakukan pengembalian dana
        $notif = NotificationFeature::where('name', 'pengembalian_dana_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $pengembalian->load('pengajuanPeminjaman.debitur');

        // Hitung total nominal yang dibayar
        $totalDibayar = ($pengembalian->pengembalianInvoices->sum('nominal_yg_dibayarkan') ?? 0);
        $nominalFormatted = 'Rp ' . number_format($totalDibayar, 0, ',', '.');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $pengembalian->pengajuanPeminjaman->debitur->nama_debitur ?? $pengembalian->pengajuanPeminjaman->debitur->nama ?? 'N/A',
            '[[nominal]]' => $nominalFormatted,
        ];

        // Generate link ke pengembalian pinjaman
        $link = route('pengembalian.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $pengembalian->pengajuanPeminjaman->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianDanaJatuhTempo($pengajuan, $tanggalJatuhTempo)
    {
        // Notifikasi saat tanggal pengembalian dana mendekati jatuh tempo
        $notif = NotificationFeature::where('name', 'pengembalian_dana_jatuh_tempo_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $pengajuan->load('debitur');

        // Format tanggal jatuh tempo
        $tanggalFormatted = \Carbon\Carbon::parse($tanggalJatuhTempo)->format('d F Y');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $pengajuan->debitur->nama_debitur ?? $pengajuan->debitur->nama ?? 'N/A',
            '[[tanggal]]' => $tanggalFormatted,
        ];

        // Generate link ke pengembalian pinjaman
        $link = route('pengembalian.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $pengajuan->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianDanaTelat($pengajuan, $tanggalJatuhTempo)
    {
        // Notifikasi saat debitur telat dalam pengembalian dana
        $notif = NotificationFeature::where('name', 'pengembalian_dana_telat_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $pengajuan->load('debitur');

        // Format tanggal jatuh tempo
        $tanggalFormatted = \Carbon\Carbon::parse($tanggalJatuhTempo)->format('d F Y');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $pengajuan->debitur->nama_debitur ?? $pengajuan->debitur->nama ?? 'N/A',
            '[[tanggal]]' => $tanggalFormatted,
        ];

        // Generate link ke pengembalian pinjaman
        $link = route('pengembalian.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $pengajuan->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function menuRestrukturisasi($status, $pengajuan, $step = null)
    {
        // Mapping status dan step ke notification feature
        $notif = null;
        
        // Saat pengajuan baru di-submit (step 1 -> step 2, status menjadi 'Submit Dokumen')
        if ($step == 1 && ($status === 'Submit Dokumen' || $status === 'Dalam Proses')) {
            $notif = NotificationFeature::where('name', 'pengajuan_restrukturisasi_baru_sfinance')->first();
        } 
        // Saat disetujui/ditolak oleh SKI Finance (step 2)
        else if ($step == 2) {
            // Cek dari history apakah approve atau reject
            $history = \App\Models\HistoryStatusPengajuanRestrukturisasi::where('id_pengajuan_restrukturisasi', $pengajuan->id_pengajuan_restrukturisasi)
                ->where('current_step', 2)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($history) {
                if ($history->validasi_dokumen === 'disetujui') {
                    $notif = NotificationFeature::where('name', 'pengajuan_restrukturisasi_disetujui_finance_ski')->first();
                } else if ($history->validasi_dokumen === 'ditolak') {
                    $notif = NotificationFeature::where('name', 'pengajuan_restrukturisasi_ditolak_finance_ski')->first();
                }
            }
        }
        // Saat disetujui/ditolak oleh CEO SKI (step 3)
        else if ($step == 3) {
            $history = \App\Models\HistoryStatusPengajuanRestrukturisasi::where('id_pengajuan_restrukturisasi', $pengajuan->id_pengajuan_restrukturisasi)
                ->where('current_step', 3)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($history) {
                if ($history->validasi_dokumen === 'disetujui') {
                    $notif = NotificationFeature::where('name', 'pengajuan_restrukturisasi_disetujui_ceo_ski')->first();
                } else if ($history->validasi_dokumen === 'ditolak') {
                    $notif = NotificationFeature::where('name', 'pengajuan_restrukturisasi_ditolak_ceo_ski')->first();
                }
            }
        }
        // Saat disetujui/ditolak oleh Direktur SKI (step 4)
        else if ($step == 4) {
            $history = \App\Models\HistoryStatusPengajuanRestrukturisasi::where('id_pengajuan_restrukturisasi', $pengajuan->id_pengajuan_restrukturisasi)
                ->where('current_step', 4)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($history) {
                if ($history->validasi_dokumen === 'disetujui') {
                    $notif = NotificationFeature::where('name', 'pengajuan_restrukturisasi_disetujui_direktur_ski')->first();
                } else if ($history->validasi_dokumen === 'ditolak') {
                    $notif = NotificationFeature::where('name', 'pengajuan_restrukturisasi_ditolak_direktur_ski')->first();
                }
            }
        }

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $pengajuan->load('debitur');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $pengajuan->debitur->nama ?? $pengajuan->debitur->nama_debitur ?? 'N/A',
        ];

        // Generate link ke detail pengajuan restrukturisasi
        $link = route('pengajuan-restrukturisasi.show', $pengajuan->id_pengajuan_restrukturisasi);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $pengajuan->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pembayaranRestrukturisasi($jadwalAngsuran)
    {
        // Notifikasi saat debitur melakukan pembayaran restrukturisasi
        $notif = NotificationFeature::where('name', 'pembayaran_restrukturisasi_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $jadwalAngsuran->load('programRestrukturisasi.pengajuanRestrukturisasi.debitur');

        // Format nominal
        $nominalFormatted = 'Rp ' . number_format($jadwalAngsuran->nominal_bayar ?? $jadwalAngsuran->total_cicilan, 0, ',', '.');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->debitur->nama ?? 
                                  $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->debitur->nama_debitur ?? 
                                  $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->nama_perusahaan ?? 'N/A',
            '[[nominal]]' => $nominalFormatted,
        ];

        // Generate link ke program restrukturisasi
        $link = route('program-restrukturisasi.show', $jadwalAngsuran->programRestrukturisasi->id_program_restrukturisasi);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pembayaranRestrukturisasiJatuhTempo($jadwalAngsuran, $tanggalJatuhTempo)
    {
        // Notifikasi saat tanggal pembayaran restrukturisasi mendekati jatuh tempo
        $notif = NotificationFeature::where('name', 'pembayaran_restrukturisasi_jatuh_tempo_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $jadwalAngsuran->load('programRestrukturisasi.pengajuanRestrukturisasi.debitur');

        // Format tanggal jatuh tempo
        $tanggalFormatted = \Carbon\Carbon::parse($tanggalJatuhTempo)->format('d F Y');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->debitur->nama ?? 
                                  $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->debitur->nama_debitur ?? 
                                  $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->nama_perusahaan ?? 'N/A',
            '[[tanggal]]' => $tanggalFormatted,
        ];

        // Generate link ke program restrukturisasi
        $link = route('program-restrukturisasi.show', $jadwalAngsuran->programRestrukturisasi->id_program_restrukturisasi);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pembayaranRestrukturisasiTelat($jadwalAngsuran, $tanggalJatuhTempo)
    {
        // Notifikasi saat debitur telat dalam pembayaran restrukturisasi
        $notif = NotificationFeature::where('name', 'pembayaran_restrukturisasi_telat_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $jadwalAngsuran->load('programRestrukturisasi.pengajuanRestrukturisasi.debitur');

        // Format tanggal jatuh tempo
        $tanggalFormatted = \Carbon\Carbon::parse($tanggalJatuhTempo)->format('d F Y');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->debitur->nama ?? 
                                  $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->debitur->nama_debitur ?? 
                                  $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->nama_perusahaan ?? 'N/A',
            '[[tanggal]]' => $tanggalFormatted,
        ];

        // Generate link ke program restrukturisasi
        $link = route('program-restrukturisasi.show', $jadwalAngsuran->programRestrukturisasi->id_program_restrukturisasi);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $jadwalAngsuran->programRestrukturisasi->pengajuanRestrukturisasi->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function menuPengajuanInvestasi($status, $pengajuan, $nominal = 0)
    {
        // Mapping status ke notification feature
        $notif = null;
        
        if ($status === 'Submit Dokumen') {
            $notif = NotificationFeature::where('name', 'pengajuan_investasi_baru_sfinance')->first();
        } else if ($status === 'Dokumen Tervalidasi') {
            $notif = NotificationFeature::where('name', 'pengajuan_investasi_disetujui_finance_ski')->first();
        } else if ($status === 'Ditolak' || str_contains($status, 'Ditolak')) {
            // Cek dari history apakah ditolak di step 2 (SKI Finance) atau step 3 (CEO SKI)
            $history = \App\Models\HistoryStatusPengajuanInvestor::where('id_pengajuan_investasi', $pengajuan->id_pengajuan_investasi)
                ->where('validasi_bagi_hasil', 'ditolak')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($history) {
                if ($history->current_step == 2) {
                    $notif = NotificationFeature::where('name', 'pengajuan_investasi_ditolak_finance_ski')->first();
                } else if ($history->current_step == 3) {
                    $notif = NotificationFeature::where('name', 'pengajuan_investasi_ditolak_ceo_ski')->first();
                }
            }
        } else if ($status === 'Disetujui oleh CEO SKI') {
            $notif = NotificationFeature::where('name', 'pengajuan_investasi_disetujui_ceo_ski')->first();
        } else if ($status === 'Generate Kontrak') {
            $notif = NotificationFeature::where('name', 'kontrak_investasi_dibuat_sfinance')->first();
        } else if ($status === 'Selesai') {
            // Cek apakah sudah ada nomor kontrak (berarti sudah generate kontrak sebelumnya)
            if ($pengajuan->nomor_kontrak) {
                $notif = NotificationFeature::where('name', 'investasi_berhasil_ditransfer_sfinance')->first();
            }
        }

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $pengajuan->load('investor');

        // Format nominal jika ada
        $nominalFormatted = $nominal > 0 ? 'Rp ' . number_format($nominal, 0, ',', '.') : '';

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.investor]]' => $pengajuan->nama_investor ?? $pengajuan->investor->nama ?? 'N/A',
        ];

        // Tambahkan nominal jika ada
        if ($nominalFormatted) {
            $notif_variable['[[nominal]]'] = $nominalFormatted;
        }

        // Generate link ke detail pengajuan investasi
        $link = route('pengajuan-investasi.show', $pengajuan->id_pengajuan_investasi);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => null,
            'id_investor' => $pengajuan->id_debitur_dan_investor,
        ];

        sendNotification($data);
    }

    public static function penyaluranInvestasi($penyaluran)
    {
        // Notifikasi saat debitur menerima dana investasi
        $notif = NotificationFeature::where('name', 'debitur_menerima_dana_investasi_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $penyaluran->load('debitur', 'pengajuanInvestasi');

        // Format nominal
        $nominalFormatted = 'Rp ' . number_format($penyaluran->nominal_yang_disalurkan, 0, ',', '.');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $penyaluran->debitur->nama ?? $penyaluran->debitur->nama_debitur ?? 'N/A',
            '[[nominal]]' => $nominalFormatted,
        ];

        // Generate link ke penyaluran deposito (atau bisa ke pengajuan investasi)
        $link = route('penyaluran-deposito.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $penyaluran->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianInvestasi($penyaluran)
    {
        // Notifikasi saat debitur mengembalikan dana investasi
        $notif = NotificationFeature::where('name', 'debitur_mengembalikan_dana_investasi_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $penyaluran->load('debitur', 'pengajuanInvestasi');

        // Format nominal
        $nominalFormatted = 'Rp ' . number_format($penyaluran->nominal_yang_disalurkan, 0, ',', '.');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $penyaluran->debitur->nama ?? $penyaluran->debitur->nama_debitur ?? 'N/A',
            '[[nominal]]' => $nominalFormatted,
        ];

        // Generate link ke penyaluran deposito
        $link = route('penyaluran-deposito.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $penyaluran->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianInvestasiJatuhTempo($penyaluran, $tanggalJatuhTempo)
    {
        // Notifikasi saat tanggal pengembalian investasi mendekati jatuh tempo
        $notif = NotificationFeature::where('name', 'pengembalian_investasi_jatuh_tempo_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $penyaluran->load('debitur', 'pengajuanInvestasi');

        // Format tanggal jatuh tempo
        $tanggalFormatted = \Carbon\Carbon::parse($tanggalJatuhTempo)->format('d F Y');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $penyaluran->debitur->nama ?? $penyaluran->debitur->nama_debitur ?? 'N/A',
            '[[tanggal]]' => $tanggalFormatted,
        ];

        // Generate link ke penyaluran deposito
        $link = route('penyaluran-deposito.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $penyaluran->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianInvestasiKeInvestorJatuhTempo($pengajuan, $tanggalJatuhTempo)
    {
        // Notifikasi saat tanggal pengembalian investasi ke investor mendekati jatuh tempo
        $notif = NotificationFeature::where('name', 'pengembalian_investasi_ke_investor_jatuh_tempo_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $pengajuan->load('investor');

        // Format tanggal jatuh tempo
        $tanggalFormatted = \Carbon\Carbon::parse($tanggalJatuhTempo)->format('d F Y');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.investor]]' => $pengajuan->nama_investor ?? $pengajuan->investor->nama ?? 'N/A',
            '[[tanggal]]' => $tanggalFormatted,
        ];

        // Generate link ke pengembalian investasi
        $link = route('pengembalian-investasi.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => null,
            'id_investor' => $pengajuan->id_debitur_dan_investor,
        ];

        sendNotification($data);
    }

    public static function transferPengembalianInvestasiKeInvestor($pengembalian)
    {
        // Notifikasi saat SKI Finance melakukan transfer pengembalian investasi ke investor
        $notif = NotificationFeature::where('name', 'transfer_pengembalian_investasi_ke_investor_sfinance')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $pengembalian->load('pengajuanInvestasi.investor');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.investor]]' => $pengembalian->pengajuanInvestasi->nama_investor ?? 
                                   $pengembalian->pengajuanInvestasi->investor->nama ?? 'N/A',
        ];

        // Generate link ke pengembalian investasi
        $link = route('pengembalian-investasi.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => null,
            'id_investor' => $pengembalian->pengajuanInvestasi->id_debitur_dan_investor ?? null,
        ];

        sendNotification($data);
    }
}