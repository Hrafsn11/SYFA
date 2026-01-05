<?php

namespace App\Helpers;

use App\Models\NotificationFeature;
use App\Models\PeminjamanFinlog;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\PenyaluranDepositoSfinlog;

class ListNotifSFinlog
{
    public static function menuPeminjaman($status, $peminjaman)
    {
        // Mapping status dari history ke notification feature name
        if($status === 'Pengajuan Disubmit') {
            $notif = NotificationFeature::where('name', 'pengajuan_disubmit_peminjaman_finlog')->first();
        } else if($status === 'Disetujui Investment Officer') {
            $notif = NotificationFeature::where('name', 'disetujui_investment_officer_peminjaman_finlog')->first();
        } else if($status === 'Ditolak Investment Officer') {
            $notif = NotificationFeature::where('name', 'ditolak_investment_officer_peminjaman_finlog')->first();
        } else if($status === 'Disetujui Debitur') {
            $notif = NotificationFeature::where('name', 'disetujui_debitur_peminjaman_finlog')->first();
        } else if($status === 'Ditolak Debitur') {
            $notif = NotificationFeature::where('name', 'ditolak_debitur_peminjaman_finlog')->first();
        } else if($status === 'Disetujui SKI Finance') {
            $notif = NotificationFeature::where('name', 'disetujui_ski_finance_peminjaman_finlog')->first();
        } else if($status === 'Ditolak SKI Finance') {
            $notif = NotificationFeature::where('name', 'ditolak_ski_finance_peminjaman_finlog')->first();
        } else if($status === 'Disetujui CEO Finlog') {
            $notif = NotificationFeature::where('name', 'disetujui_ceo_finlog_peminjaman_finlog')->first();
        } else if($status === 'Ditolak CEO Finlog') {
            $notif = NotificationFeature::where('name', 'ditolak_ceo_finlog_peminjaman_finlog')->first();
        } else if($status === 'Kontrak Digenerate') {
            $notif = NotificationFeature::where('name', 'kontrak_digenerate_peminjaman_finlog')->first();
        } else if($status === 'Bukti Transfer Diupload') {
            $notif = NotificationFeature::where('name', 'bukti_transfer_diupload_peminjaman_finlog')->first();
        }

        // Jika notification feature tidak ditemukan, skip pengiriman notifikasi
        if (!$notif) {
            return;
        }

        // Format nominal untuk notifikasi
        $nominal = $peminjaman->nilai_pinjaman ?? $peminjaman->total_pinjaman ?? 0;
        $nominalFormatted = 'Rp ' . number_format($nominal, 0, ',', '.');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $peminjaman->debitur->nama ?? 'N/A',
            '[[nomor.peminjaman]]' => $peminjaman->nomor_peminjaman ?? 'N/A',
            '[[nama.project]]' => $peminjaman->nama_project ?? 'N/A',
            '[[nominal]]' => $nominalFormatted,
        ];

        // Generate link ke detail peminjaman
        $link = route('sfinlog.peminjaman.detail', $peminjaman->id_peminjaman_finlog);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $peminjaman->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianDana($peminjaman, $pengembalian = null)
    {
        // Notifikasi saat debitur melakukan pengembalian dana
        $notif = NotificationFeature::where('name', 'pengembalian_dana_pinjaman_finlog')->first();

        if (!$notif) {
            return;
        }

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $peminjaman->debitur->nama ?? 'N/A',
            '[[nomor.peminjaman]]' => $peminjaman->nomor_peminjaman ?? 'N/A',
            '[[nama.project]]' => $peminjaman->nama_project ?? 'N/A',
        ];

        // Generate link ke detail peminjaman
        $link = route('sfinlog.peminjaman.detail', $peminjaman->id_peminjaman_finlog);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $peminjaman->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianDanaJatuhTempo($peminjaman, $tanggalJatuhTempo)
    {
        // Notifikasi saat tanggal pengembalian dana mendekati jatuh tempo
        $notif = NotificationFeature::where('name', 'pengembalian_dana_jatuh_tempo_finlog')->first();

        if (!$notif) {
            return;
        }

        // Format tanggal jatuh tempo
        $tanggalFormatted = \Carbon\Carbon::parse($tanggalJatuhTempo)->format('d F Y');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $peminjaman->debitur->nama ?? 'N/A',
            '[[nomor.peminjaman]]' => $peminjaman->nomor_peminjaman ?? 'N/A',
            '[[nama.project]]' => $peminjaman->nama_project ?? 'N/A',
            '[[tanggal.jatuh.tempo]]' => $tanggalFormatted,
        ];

        // Generate link ke detail peminjaman
        $link = route('sfinlog.peminjaman.detail', $peminjaman->id_peminjaman_finlog);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $peminjaman->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianDanaTelat($peminjaman)
    {
        // Notifikasi saat debitur telat dalam pengembalian dana
        $notif = NotificationFeature::where('name', 'pengembalian_dana_telat_finlog')->first();

        if (!$notif) {
            return;
        }

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $peminjaman->debitur->nama ?? 'N/A',
            '[[nomor.peminjaman]]' => $peminjaman->nomor_peminjaman ?? 'N/A',
            '[[nama.project]]' => $peminjaman->nama_project ?? 'N/A',
        ];

        // Generate link ke detail peminjaman
        $link = route('sfinlog.peminjaman.detail', $peminjaman->id_peminjaman_finlog);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $peminjaman->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function menuPengajuanInvestasi($status, $pengajuan)
    {
        // Mapping status dari history ke notification feature name
        if($status === 'Menunggu Validasi Finance SKI') {
            $notif = NotificationFeature::where('name', 'pengajuan_investasi_baru_finlog')->first();
        } else if($status === 'Dokumen Tervalidasi') {
            $notif = NotificationFeature::where('name', 'disetujui_ski_finance_investasi_finlog')->first();
        } else if($status === 'Ditolak' || (str_contains($status, 'Ditolak') && $pengajuan->current_step == 2)) {
            // Ditolak di step 2 (SKI Finance)
            $notif = NotificationFeature::where('name', 'ditolak_ski_finance_investasi_finlog')->first();
        } else if($status === 'Disetujui CEO Finlog' || $status === 'Menunggu Upload Bukti Transfer') {
            $notif = NotificationFeature::where('name', 'disetujui_ceo_ski_investasi_finlog')->first();
        } else if(str_contains($status, 'Ditolak CEO') || (str_contains($status, 'Ditolak') && $pengajuan->current_step == 3)) {
            // Ditolak di step 3 (CEO)
            $notif = NotificationFeature::where('name', 'ditolak_ceo_ski_investasi_finlog')->first();
        } else if($status === 'Bukti Transfer Diupload') {
            // Investasi berhasil ditransfer oleh investor
            $notif = NotificationFeature::where('name', 'investasi_berhasil_ditransfer_finlog')->first();
        } else if($status === 'Selesai' && $pengajuan->nomor_kontrak) {
            // Kontrak sudah dibuat (saat generate kontrak, status menjadi Selesai)
            $notif = NotificationFeature::where('name', 'kontrak_investasi_dibuat_finlog')->first();
        }

        // Jika notification feature tidak ditemukan, skip pengiriman notifikasi
        if (!$notif) {
            return;
        }

        // Format nominal untuk notifikasi
        $nominal = $pengajuan->nominal_investasi ?? 0;
        $nominalFormatted = 'Rp ' . number_format($nominal, 0, ',', '.');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.investor]]' => $pengajuan->nama_investor ?? $pengajuan->investor->nama ?? 'N/A',
            '[[nomor.pengajuan]]' => $pengajuan->nomor_pengajuan ?? 'N/A',
            '[[nominal]]' => $nominalFormatted,
        ];

        // Generate link ke detail pengajuan investasi
        $link = route('sfinlog.pengajuan-investasi.show', $pengajuan->id_pengajuan_investasi_finlog);

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
        $notif = NotificationFeature::where('name', 'debitur_menerima_dana_investasi_finlog')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $penyaluran->load('cellsProject', 'project', 'pengajuanInvestasiFinlog');

        // Ambil nama project
        $namaProject = $penyaluran->project->nama_project ?? $penyaluran->cellsProject->nama_cells_bisnis ?? 'N/A';

        // Cari debitur dari peminjaman yang terkait dengan project yang sama
        $debitur = null;
        if ($penyaluran->id_project) {
            $peminjaman = PeminjamanFinlog::where('nama_project', $penyaluran->id_project)
                ->orWhere('id_cells_project', $penyaluran->id_cells_project)
                ->with('debitur')
                ->first();
            $debitur = $peminjaman->debitur ?? null;
        } elseif ($penyaluran->id_cells_project) {
            $peminjaman = PeminjamanFinlog::where('id_cells_project', $penyaluran->id_cells_project)
                ->with('debitur')
                ->first();
            $debitur = $peminjaman->debitur ?? null;
        }

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $debitur->nama ?? $penyaluran->cellsProject->nama_cells_bisnis ?? 'N/A',
            '[[nama.project]]' => $namaProject,
        ];

        // Generate link ke penyaluran dana investasi
        $link = route('sfinlog.report-penyaluran-dana-investasi.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $peminjaman->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianInvestasi($penyaluran)
    {
        // Notifikasi saat debitur mengembalikan dana investasi
        $notif = NotificationFeature::where('name', 'debitur_pengembalian_dana_investasi_finlog')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $penyaluran->load('cellsProject', 'project', 'pengajuanInvestasiFinlog');

        // Ambil nama project
        $namaProject = $penyaluran->project->nama_project ?? $penyaluran->cellsProject->nama_cells_bisnis ?? 'N/A';

        // Cari debitur dari peminjaman yang terkait dengan project yang sama
        $debitur = null;
        if ($penyaluran->id_project) {
            $peminjaman = PeminjamanFinlog::where('nama_project', $penyaluran->id_project)
                ->orWhere('id_cells_project', $penyaluran->id_cells_project)
                ->with('debitur')
                ->first();
            $debitur = $peminjaman->debitur ?? null;
        } elseif ($penyaluran->id_cells_project) {
            $peminjaman = PeminjamanFinlog::where('id_cells_project', $penyaluran->id_cells_project)
                ->with('debitur')
                ->first();
            $debitur = $peminjaman->debitur ?? null;
        }

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $debitur->nama ?? $penyaluran->cellsProject->nama_cells_bisnis ?? 'N/A',
            '[[nama.project]]' => $namaProject,
        ];

        // Generate link ke penyaluran dana investasi
        $link = route('sfinlog.report-penyaluran-dana-investasi.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $peminjaman->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianInvestasiJatuhTempo($penyaluran, $tanggalJatuhTempo)
    {
        // Notifikasi saat tanggal pengembalian investasi mendekati jatuh tempo
        $notif = NotificationFeature::where('name', 'pengembalian_investasi_jatuh_tempo_finlog')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $penyaluran->load('cellsProject', 'project', 'pengajuanInvestasiFinlog');

        // Format tanggal jatuh tempo
        $tanggalFormatted = \Carbon\Carbon::parse($tanggalJatuhTempo)->format('d F Y');

        // Ambil nama project
        $namaProject = $penyaluran->project->nama_project ?? $penyaluran->cellsProject->nama_cells_bisnis ?? 'N/A';

        // Cari debitur dari peminjaman yang terkait dengan project yang sama
        $debitur = null;
        if ($penyaluran->id_project) {
            $peminjaman = PeminjamanFinlog::where('nama_project', $penyaluran->id_project)
                ->orWhere('id_cells_project', $penyaluran->id_cells_project)
                ->with('debitur')
                ->first();
            $debitur = $peminjaman->debitur ?? null;
        } elseif ($penyaluran->id_cells_project) {
            $peminjaman = PeminjamanFinlog::where('id_cells_project', $penyaluran->id_cells_project)
                ->with('debitur')
                ->first();
            $debitur = $peminjaman->debitur ?? null;
        }

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.debitur]]' => $debitur->nama ?? $penyaluran->cellsProject->nama_cells_bisnis ?? 'N/A',
            '[[nama.project]]' => $namaProject,
            '[[tanggal.jatuh.tempo]]' => $tanggalFormatted,
        ];

        // Generate link ke penyaluran dana investasi
        $link = route('sfinlog.report-penyaluran-dana-investasi.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => $peminjaman->id_debitur,
            'id_investor' => null,
        ];

        sendNotification($data);
    }

    public static function pengembalianInvestasiKeInvestorJatuhTempo($pengajuan, $tanggalJatuhTempo)
    {
        // Notifikasi saat tanggal pengembalian investasi ke investor mendekati jatuh tempo
        $notif = NotificationFeature::where('name', 'pengembalian_investasi_ke_investor_jatuh_tempo_finlog')->first();

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
            '[[tanggal.jatuh.tempo]]' => $tanggalFormatted,
        ];

        // Generate link ke pengembalian investasi
        $link = route('sfinlog.pengembalian-investasi.index');

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
        $notif = NotificationFeature::where('name', 'transfer_pengembalian_investasi_ke_investor_finlog')->first();

        if (!$notif) {
            return;
        }

        // Load relasi yang diperlukan
        $pengembalian->load('pengajuan.investor');

        // Siapkan variable untuk template notifikasi
        $notif_variable = [
            '[[nama.investor]]' => $pengembalian->pengajuan->nama_investor ?? $pengembalian->pengajuan->investor->nama ?? 'N/A',
        ];

        // Generate link ke pengembalian investasi
        $link = route('sfinlog.pengembalian-investasi.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
            'id_debitur' => null,
            'id_investor' => $pengembalian->pengajuan->id_debitur_dan_investor,
        ];

        sendNotification($data);
    }
}

