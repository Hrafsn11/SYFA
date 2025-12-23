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
            ];
            sendNotification($data);
        }
    }
}