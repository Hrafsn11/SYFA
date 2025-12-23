<?php

namespace App\Helpers;

use App\Models\NotificationFeature;
use App\Models\PeminjamanFinlog;

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
        ];

        sendNotification($data);
    }
}

