<?php

namespace Database\Seeders;

use App\Models\NotificationFeature;
use App\Models\NotificationFeatureDetail;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSFinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $debitur = Role::firstOrCreate(['name' => 'Debitur', 'guard_name' => 'web'], ['restriction' => 0]);
        $finance = Role::firstOrCreate(['name' => 'Finance SKI', 'guard_name' => 'web'], ['restriction' => 0]);
        $ceo = Role::firstOrCreate(['name' => 'CEO SKI', 'guard_name' => 'web'], ['restriction' => 0]);
        $direktur = Role::firstOrCreate(['name' => 'Direktur SKI', 'guard_name' => 'web'], ['restriction' => 0]);

        #Menu Peminjaman
        $submit_review_peminjaman = NotificationFeature::firstOrCreate([
            'name' => 'submit_review_peminjaman',
            'module' => 's_finance',
        ]);

        $pengajuan_ditolak_finance_ski = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_ditolak_finance_ski',
            'module' => 's_finance',
        ]);

        $pengajuan_disetujui_finance_ski = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_disetujui_finance_ski',
            'module' => 's_finance',
        ]);

        $nominal_disetujui_debitur = NotificationFeature::firstOrCreate([
            'name' => 'nominal_disetujui_debitur',
            'module' => 's_finance',
        ]);

        $nominal_ditolak_debitur = NotificationFeature::firstOrCreate([
            'name' => 'nominal_ditolak_debitur',
            'module' => 's_finance',
        ]);

        $pengajuan_disetujui_oleh_ceo_ski = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_disetujui_oleh_ceo_ski',
            'module' => 's_finance',
        ]);

        $pengajuan_ditolak_oleh_ceo_ski = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_ditolak_oleh_ceo_ski',
            'module' => 's_finance',
        ]);

        $pengajuan_disetujui_oleh_direktur_ski = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_disetujui_oleh_direktur_ski',
            'module' => 's_finance',
        ]);

        $pengajuan_ditolak_oleh_direktur_ski = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_ditolak_oleh_direktur_ski',
            'module' => 's_finance',
        ]);

        $generate_kontrak = NotificationFeature::firstOrCreate([
            'name' => 'generate_kontrak',
            'module' => 's_finance',
        ]);

        $sk_finance_upload_bukti_transfer = NotificationFeature::firstOrCreate([
            'name' => 'sk_finance_upload_bukti_transfer',
            'module' => 's_finance',
        ]);

        $sk_finance_konfirmasi_ditolak_debitur = NotificationFeature::firstOrCreate([
            'name' => 'sk_finance_konfirmasi_ditolak_debitur',
            'module' => 's_finance',
        ]);

        $pencairan_dana = NotificationFeature::firstOrCreate([
            'name' => 'pencairan_dana',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $submit_review_peminjaman->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan pinjaman baru telah diterima dari debitur [[nama.debitur]]. Silakan lakukan proses verifikasi.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_ditolak_finance_ski->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh SKI Finance.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_disetujui_finance_ski->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh SKI Finance.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $nominal_disetujui_debitur->id_notification_feature,
            'role_assigned' => json_encode([$finance->id, $ceo->id]),
            'message' => 'Debitur [[nama.debitur]] telah menyetujui nominal pencairan sebesar Rp [[nominal]].',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $nominal_ditolak_debitur->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] telah menolak nominal pencairan.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_disetujui_oleh_ceo_ski->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh CEO SKI.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_ditolak_oleh_ceo_ski->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh CEO SKI.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_disetujui_oleh_direktur_ski->id_notification_feature,
            'role_assigned' => json_encode([$finance->id, $ceo->id, $debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh Direktur SKI.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_ditolak_oleh_direktur_ski->id_notification_feature,
            'role_assigned' => json_encode([$finance->id, $ceo->id, $debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh Direktur SKI.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $generate_kontrak->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Kontrak pinjaman debitur [[nama.debitur]] telah berhasil dibuat.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $sk_finance_upload_bukti_transfer->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pinjaman debitur [[nama.debitur]] sebesar Rp [[nominal]] telah berhasil dicairkan. Status pinjaman: Selesai.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pencairan_dana->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] telah mengonfirmasi penerimaan dana pinjaman.',
        ]);
        
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $sk_finance_konfirmasi_ditolak_debitur->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] telah menolak konfirmasi pencairan dana pinjaman.',
        ]);

        // ============================================
        // NOTIFICATION FEATURES UNTUK PENGEMBALIAN DANA
        // ============================================

        // 1. Pengembalian Dana - SKI Finance
        $pengembalian_dana = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_dana_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_dana->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] telah melakukan pengembalian dana sebesar [[nominal]].',
        ]);

        // 2. Pengembalian Dana Jatuh Tempo - Debitur dan SKI Finance
        $pengembalian_dana_jatuh_tempo = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_dana_jatuh_tempo_sfinance',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_dana_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengembalian dana pinjaman debitur [[nama.debitur]] akan jatuh tempo pada [[tanggal]].',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_dana_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengembalian dana pinjaman debitur [[nama.debitur]] akan jatuh tempo pada [[tanggal]].',
        ]);

        // 3. Pengembalian Dana Telat - Debitur dan SKI Finance
        $pengembalian_dana_telat = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_dana_telat_sfinance',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_dana_telat->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Debitur [[nama.debitur]] belum melakukan pembayaran meskipun telah melewati tanggal jatuh tempo [[tanggal]].',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_dana_telat->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] belum melakukan pembayaran meskipun telah melewati tanggal jatuh tempo [[tanggal]].',
        ]);
    }
}
