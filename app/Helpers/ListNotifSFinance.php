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
            '[[nama.debitur]]' => $peminjaman->debitur->nama ?? null,
        ];

        $link = route('peminjaman.detail', $peminjaman->id_pengajuan_peminjaman);

        if($notif) {
            $data = [
                'notif_variable' => $notif_variable,
                'link' => $link,
                'notif' => $notif,
            ];
            sendNotification($data);
        }
    }
}