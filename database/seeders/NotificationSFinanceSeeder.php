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

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_ditolak_finance_ski->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh SKI Finance.',
        ]);

        // Notifikasi untuk Direktur SKI
        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengajuan_ditolak_finance_ski->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$direktur->id, $ceo->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh SKI Finance.',
        ]);

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_disetujui_finance_ski->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh SKI Finance.',
        ]);

        // Notifikasi untuk Direktur SKI
        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengajuan_disetujui_finance_ski->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$direktur->id, $ceo->id]),
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
            'role_assigned' => json_encode([$finance->id, $direktur->id, $debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh CEO SKI.',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_ditolak_oleh_ceo_ski->id_notification_feature,
            'role_assigned' => json_encode([$finance->id, $direktur->id, $debitur->id]),
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

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $generate_kontrak->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id, $direktur->id, $ceo->id]),
            'message' => 'Kontrak pinjaman debitur [[nama.debitur]] telah berhasil dibuat.',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $sk_finance_upload_bukti_transfer->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id, $direktur->id, $ceo->id]),
            'message' => 'Pinjaman debitur [[nama.debitur]] sebesar Rp [[nominal]] telah berhasil dicairkan. Status pinjaman: Selesai.',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pencairan_dana->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$finance->id, $ceo->id, $direktur->id]),
            'message' => 'Debitur [[nama.debitur]] telah mengonfirmasi penerimaan dana pinjaman.',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $sk_finance_konfirmasi_ditolak_debitur->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$finance->id, $ceo->id, $direktur->id]),
            'message' => 'Debitur [[nama.debitur]] telah menolak konfirmasi pencairan dana pinjaman.',
        ]);

        // NOTIFICATION FEATURES UNTUK PENGEMBALIAN DANA
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

        $sp1 = NotificationFeature::firstOrCreate([
            'name' => 'surat_peringatan_1_pengembalian_dana_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $sp1->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Sehubungan dengan hasil evaluasi dan pemantauan yang telah kami lakukan, bersama email ini kami sampaikan bahwa perusahaan akan mengirimkan Surat Peringatan Pertama (SP 1) sebagai bentuk tindak lanjut atas hal-hal yang telah menjadi perhatian bersama. Adapun surat resmi terlampir pada email ini untuk dapat dipelajari dan ditindaklanjuti sebagaimana mestinya.',
        ]);

        $sp2 = NotificationFeature::firstOrCreate([
            'name' => 'surat_peringatan_2_pengembalian_dana_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $sp2->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Sehubungan dengan hasil evaluasi dan pemantauan yang telah kami lakukan, bersama email ini kami sampaikan bahwa perusahaan akan mengirimkan Surat Peringatan Kedua (SP 2) sebagai bentuk tindak lanjut atas hal-hal yang telah menjadi perhatian bersama. Adapun surat resmi terlampir pada email ini untuk dapat dipelajari dan ditindaklanjuti sebagaimana mestinya.',
        ]);

        $sp3 = NotificationFeature::firstOrCreate([
            'name' => 'surat_peringatan_3_pengembalian_dana_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $sp3->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Sehubungan dengan hasil evaluasi dan pemantauan yang telah kami lakukan, bersama email ini kami sampaikan bahwa perusahaan akan mengirimkan Surat Peringatan Ketiga (SP 3) sebagai bentuk tindak lanjut atas hal-hal yang telah menjadi perhatian bersama. Adapun surat resmi terlampir pada email ini untuk dapat dipelajari dan ditindaklanjuti sebagaimana mestinya.',
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

        // NOTIFICATION FEATURES UNTUK RESTRUKTURISASI
        // 1. Pengajuan Restrukturisasi Baru - SKI Finance
        $pengajuan_restrukturisasi_baru = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_restrukturisasi_baru_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_restrukturisasi_baru->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan restrukturisasi baru telah diterima dari debitur [[nama.debitur]].',
        ]);

        // 2. Pengajuan Restrukturisasi Disetujui oleh SKI Finance - Debitur
        $pengajuan_restrukturisasi_disetujui_finance = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_restrukturisasi_disetujui_finance_ski',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengajuan_restrukturisasi_disetujui_finance->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id, $ceo->id, $direktur->id]),
            'message' => 'Pengajuan restrukturisasi debitur [[nama.debitur]] telah disetujui oleh SKI Finance.',
        ]);

        // 3. Pengajuan Restrukturisasi Ditolak oleh SKI Finance - Debitur
        $pengajuan_restrukturisasi_ditolak_finance = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_restrukturisasi_ditolak_finance_ski',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengajuan_restrukturisasi_ditolak_finance->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id, $ceo->id, $direktur->id]),
            'message' => 'Pengajuan restrukturisasi debitur [[nama.debitur]] telah ditolak oleh SKI Finance.',
        ]);

        // 4. Pengajuan Restrukturisasi Disetujui oleh CEO SKI - SKI Finance
        $pengajuan_restrukturisasi_disetujui_ceo = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_restrukturisasi_disetujui_ceo_ski',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengajuan_restrukturisasi_disetujui_ceo->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id, $direktur->id, $finance->id]),
            'message' => 'Pengajuan restrukturisasi debitur [[nama.debitur]] telah disetujui oleh CEO SKI.',
        ]);

        // 5. Pengajuan Restrukturisasi Ditolak oleh CEO SKI - SKI Finance
        $pengajuan_restrukturisasi_ditolak_ceo = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_restrukturisasi_ditolak_ceo_ski',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengajuan_restrukturisasi_ditolak_ceo->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id, $direktur->id, $finance->id]),
            'message' => 'Pengajuan restrukturisasi debitur [[nama.debitur]] telah ditolak oleh CEO SKI.',
        ]);

        // 6. Pengajuan Restrukturisasi Disetujui oleh Direktur SKI - SKI Finance, CEO SKI, Debitur
        $pengajuan_restrukturisasi_disetujui_direktur = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_restrukturisasi_disetujui_direktur_ski',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengajuan_restrukturisasi_disetujui_direktur->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$ceo->id, $debitur->id, $finance->id]),
            'message' => 'Pengajuan restrukturisasi debitur [[nama.debitur]] telah disetujui oleh Direktur SKI.',
        ]);

        // 7. Pengajuan Restrukturisasi Ditolak oleh Direktur SKI - SKI Finance, CEO SKI, Debitur
        $pengajuan_restrukturisasi_ditolak_direktur = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_restrukturisasi_ditolak_direktur_ski',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengajuan_restrukturisasi_ditolak_direktur->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$ceo->id, $debitur->id, $finance->id]),
            'message' => 'Pengajuan restrukturisasi debitur [[nama.debitur]] telah ditolak oleh Direktur SKI.',
        ]);

        // NOTIFICATION FEATURES UNTUK PEMBAYARAN RESTRUKTURISASI
        // 1. Pembayaran Restrukturisasi - SKI Finance
        $pembayaran_restrukturisasi = NotificationFeature::firstOrCreate([
            'name' => 'pembayaran_restrukturisasi_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pembayaran_restrukturisasi->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$finance->id, $ceo->id]),
            'message' => 'Debitur [[nama.debitur]] telah melakukan pembayaran restrukturisasi sebesar [[nominal]].',
        ]);

        // 2. Pembayaran Restrukturisasi Jatuh Tempo - Debitur dan SKI Finance
        $pembayaran_restrukturisasi_jatuh_tempo = NotificationFeature::firstOrCreate([
            'name' => 'pembayaran_restrukturisasi_jatuh_tempo_sfinance',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pembayaran_restrukturisasi_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pembayaran restrukturisasi debitur [[nama.debitur]] akan jatuh tempo pada [[tanggal]].',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pembayaran_restrukturisasi_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pembayaran restrukturisasi debitur [[nama.debitur]] akan jatuh tempo pada [[tanggal]].',
        ]);

        // 3. Pembayaran Restrukturisasi Telat - Debitur dan SKI Finance
        $pembayaran_restrukturisasi_telat = NotificationFeature::firstOrCreate([
            'name' => 'pembayaran_restrukturisasi_telat_sfinance',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pembayaran_restrukturisasi_telat->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Debitur [[nama.debitur]] terlambat melakukan pembayaran restrukturisasi setelah tanggal jatuh tempo [[tanggal]].',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pembayaran_restrukturisasi_telat->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] terlambat melakukan pembayaran restrukturisasi setelah tanggal jatuh tempo [[tanggal]].',
        ]);

        // NOTIFICATION FEATURES UNTUK PENGAJUAN INVESTASI
        $investor = Role::firstOrCreate(['name' => 'Investor', 'guard_name' => 'web'], ['restriction' => 0]);

        // 1. Pengajuan Investasi Baru - SKI Finance
        $pengajuan_investasi_baru = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_investasi_baru_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_baru->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan investasi baru telah diterima dari investor [[nama.investor]].',
        ]);

        // 2. Pengajuan Investasi Disetujui oleh SKI Finance - Investor dan CEO SKI
        $pengajuan_investasi_disetujui_finance = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_investasi_disetujui_finance_ski',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk Investor
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_disetujui_finance->id_notification_feature,
            'role_assigned' => json_encode([$investor->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah disetujui oleh SKI Finance.',
        ]);

        // Notifikasi untuk CEO SKI
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_disetujui_finance->id_notification_feature,
            'role_assigned' => json_encode([$ceo->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah disetujui oleh SKI Finance.',
        ]);

        // 3. Pengajuan Investasi Ditolak oleh SKI Finance - Investor dan CEO SKI
        $pengajuan_investasi_ditolak_finance = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_investasi_ditolak_finance_ski',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk Investor
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_ditolak_finance->id_notification_feature,
            'role_assigned' => json_encode([$investor->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah ditolak oleh SKI Finance.',
        ]);

        // Notifikasi untuk CEO SKI
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_ditolak_finance->id_notification_feature,
            'role_assigned' => json_encode([$ceo->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah ditolak oleh SKI Finance.',
        ]);

        // 4. Pengajuan Investasi Disetujui oleh CEO SKI - SKI Finance dan Investor
        $pengajuan_investasi_disetujui_ceo = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_investasi_disetujui_ceo_ski',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_disetujui_ceo->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah disetujui oleh CEO SKI.',
        ]);

        // Notifikasi untuk Investor
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_disetujui_ceo->id_notification_feature,
            'role_assigned' => json_encode([$investor->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah disetujui oleh CEO SKI.',
        ]);

        // 5. Pengajuan Investasi Ditolak oleh CEO SKI - SKI Finance dan Investor
        $pengajuan_investasi_ditolak_ceo = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_investasi_ditolak_ceo_ski',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_ditolak_ceo->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah ditolak oleh CEO SKI.',
        ]);

        // Notifikasi untuk Investor
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_ditolak_ceo->id_notification_feature,
            'role_assigned' => json_encode([$investor->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah ditolak oleh CEO SKI.',
        ]);

        // 6. Kontrak Investasi Dibuat - Investor
        $kontrak_investasi_dibuat = NotificationFeature::firstOrCreate([
            'name' => 'kontrak_investasi_dibuat_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $kontrak_investasi_dibuat->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$investor->id, $ceo->id]),
            'message' => 'Kontrak investasi dengan investor [[nama.investor]] telah berhasil dibuat.',
        ]);

        // 7. Investasi Berhasil Ditransfer (Status Selesai) - SKI Finance
        $investasi_berhasil_ditransfer = NotificationFeature::firstOrCreate([
            'name' => 'investasi_berhasil_ditransfer_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $investasi_berhasil_ditransfer->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$finance->id, $ceo->id]),
            'message' => 'Dana investasi dari investor [[nama.investor]] sebesar [[nominal]] telah diterima. Status investasi: Selesai.',
        ]);
        
        // NOTIFICATION FEATURES UNTUK PENYALURAN INVESTASI

        // 1. Debitur Menerima Dana Investasi - Debitur
        $debitur_menerima_dana_investasi = NotificationFeature::firstOrCreate([
            'name' => 'debitur_menerima_dana_investasi_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $debitur_menerima_dana_investasi->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Debitur [[nama.debitur]] telah menerima dana investasi sebesar [[nominal]].',
        ]);

        // 2. Debitur Mengembalikan Dana Investasi - SKI Finance
        $debitur_mengembalikan_dana_investasi = NotificationFeature::firstOrCreate([
            'name' => 'debitur_mengembalikan_dana_investasi_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $debitur_mengembalikan_dana_investasi->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] telah mengembalikan dana investasi sebesar [[nominal]].',
        ]);

        // 3. Pengembalian Investasi Jatuh Tempo - Debitur dan SKI Finance
        $pengembalian_investasi_jatuh_tempo = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_investasi_jatuh_tempo_sfinance',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_investasi_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengembalian dana investasi debitur [[nama.debitur]] akan jatuh tempo pada [[tanggal]].',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_investasi_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengembalian dana investasi debitur [[nama.debitur]] akan jatuh tempo pada [[tanggal]].',
        ]);

        // NOTIFICATION FEATURES UNTUK PENGEMBALIAN INVESTASI KE INVESTOR
        // 1. Pengembalian Investasi Ke Investor Jatuh Tempo - SKI Finance dan Investor
        $pengembalian_investasi_ke_investor_jatuh_tempo = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_investasi_ke_investor_jatuh_tempo_sfinance',
            'module' => 's_finance',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_investasi_ke_investor_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengembalian dana investasi [[nama.investor]] akan jatuh tempo pada [[tanggal]].',
        ]);

        // Notifikasi untuk Investor
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_investasi_ke_investor_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$investor->id]),
            'message' => 'Pengembalian dana investasi [[nama.investor]] akan jatuh tempo pada [[tanggal]].',
        ]);

        // 2. Transfer Pengembalian Investasi Ke Investor - Investor
        $transfer_pengembalian_investasi_ke_investor = NotificationFeature::firstOrCreate([
            'name' => 'transfer_pengembalian_investasi_ke_investor_sfinance',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $transfer_pengembalian_investasi_ke_investor->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$investor->id, $ceo->id]),
            'message' => 'SKI Finance telah melakukan transfer pengembalian dana investasi kepada investor [[nama.investor]].', 
        ]);

        // 3. Program Restrukturisasi Dibuat - SKI Finance

        $program_restrukturisasi_dibuat = NotificationFeature::firstOrCreate([
            'name' => 'program_restrukturisasi_dibuat',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $program_restrukturisasi_dibuat->id_notification_feature,
            'role_assigned' => json_encode([$ceo->id, $debitur->id]),
            'message' => 'Program Restrukturisasi untuk [[nama.debitur]] Telah berhasil dibuat.',
        ]);
    }
}
