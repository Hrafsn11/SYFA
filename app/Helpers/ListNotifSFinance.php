<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\NotificationFeature;

class ListNotifSFinance
{
    public static function menuPeminjaman($status, $peminjaman)
    {
        if($status === 'Submit Dokumen') {
            $notif = NotificationFeature::where('name', 'submit_review_peminjaman')->first();
        } else if($status === 'Dokumen Tervalidasi') {
            $notif = NotificationFeature::where('name', 'dokumen_tervalidasi_peminjaman')->first();
        } else if($status === 'Validasi Ditolak') {
            $notif = NotificationFeature::where('name', 'validasi_ditolak_peminjaman')->first();
        } else if($status === 'Dana Sudah Dicairkan') {
            $notif = NotificationFeature::where('name', 'dana_sudah_dicairkan_peminjaman')->first();
        } else if($status === 'Debitur Setuju') {
            $notif = NotificationFeature::where('name', 'debitur_setuju_peminjaman')->first();
        } else if($status === 'Pengajuan Ditolak Debitur') {
            $notif = NotificationFeature::where('name', 'pengajuan_ditolak_debitur_peminjaman')->first();
        } else if($status === 'Disetujui oleh CEO SKI') {
            $notif = NotificationFeature::where('name', 'disetujui_oleh_ceo_ski_peminjaman')->first();
        } else if($status === 'Ditolak oleh CEO SKI') {
            $notif = NotificationFeature::where('name', 'ditolak_oleh_ceo_ski_peminjaman')->first();
        } else if($status === 'Disetujui oleh Direktur SKI') {
            $notif = NotificationFeature::where('name', 'disetujui_oleh_direktur_ski_peminjaman')->first();
        } else if($status === 'Ditolak oleh Direktur SKI') {
            $notif = NotificationFeature::where('name', 'ditolak_oleh_direktur_ski_peminjaman')->first();
        } else if($status === 'Generate Kontrak') {
            $notif = NotificationFeature::where('name', 'generate_kontrak_peminjaman')->first();
        } else if($status === 'Menunggu Konfirmasi Debitur') {
            $notif = NotificationFeature::where('name', 'menunggu_konfirmasi_debitur_peminjaman')->first();
        } else if($status === 'Konfirmasi Ditolak Debitur') {
            $notif = NotificationFeature::where('name', 'konfirmasi_ditolak_debitur_peminjaman')->first();
        }

        $notif_variable = [
            '[[nama.debitur]]' => $peminjaman->debitur->nama_debitur,
        ];

        $link = route('peminjaman.detail', $peminjaman->id_pengajuan_peminjaman);

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
        ];

        sendNotification($data);
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
            '[[nama.debitur]]' => $pengembalian->pengajuanPeminjaman->debitur->nama_debitur ?? 'N/A',
            '[[nominal]]' => $nominalFormatted,
        ];

        // Generate link ke pengembalian pinjaman
        $link = route('pengembalian.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
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
            '[[nama.debitur]]' => $pengajuan->debitur->nama_debitur ?? 'N/A',
            '[[tanggal]]' => $tanggalFormatted,
        ];

        // Generate link ke pengembalian pinjaman
        $link = route('pengembalian.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
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
            '[[nama.debitur]]' => $pengajuan->debitur->nama_debitur ?? 'N/A',
            '[[tanggal]]' => $tanggalFormatted,
        ];

        // Generate link ke pengembalian pinjaman
        $link = route('pengembalian.index');

        $data = [
            'notif_variable' => $notif_variable,
            'link' => $link,
            'notif' => $notif,
        ];

        sendNotification($data);
    }
}