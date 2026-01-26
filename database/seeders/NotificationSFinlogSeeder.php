<?php

namespace Database\Seeders;

use App\Models\NotificationFeature;
use App\Models\NotificationFeatureDetail;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSFinlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get atau create roles yang diperlukan
        $debitur = Role::firstOrCreate(['name' => 'Debitur', 'guard_name' => 'web'], ['restriction' => 0]);
        $investmentOfficer = Role::firstOrCreate(['name' => 'Investment Officer', 'guard_name' => 'web'], ['restriction' => 0]);
        $finance = Role::firstOrCreate(['name' => 'Finance SKI', 'guard_name' => 'web'], ['restriction' => 0]);
        $ceo = Role::firstOrCreate(['name' => 'CEO SKI', 'guard_name' => 'web'], ['restriction' => 0]);
        $direktur = Role::firstOrCreate(['name' => 'Direktur SKI', 'guard_name' => 'web'], ['restriction' => 0]);
        $investmentOfficer = Role::firstOrCreate(['name' => 'IO (Investment Officer)', 'guard_name' => 'web'], ['restriction' => 0]);
        // Notification Features untuk Peminjaman SFinlog
        // User akan mengatur message dan role_assigned sendiri karena sangat panjang
        
        // 1. Pengajuan Disubmit - SKI Finance
        $pengajuan_disubmit = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_disubmit_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengajuan_disubmit->id_notification_feature,
            ], [
                'role_assigned' => json_encode([$investmentOfficer->id]),
                'message' => 'Pengajuan pinjaman baru telah diterima dari debitur [[nama.debitur]]. Silakan lakukan proses verifikasi.',
        ]);

        // 2. Disetujui Investment Officer
        $disetujui_io = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_investment_officer_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $disetujui_io->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh Investment Officer.',
        ]);

        // 3. Ditolak Investment Officer
        $ditolak_io = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_investment_officer_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $ditolak_io->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh Investment Officer.',
        ]);

        // 4. Disetujui Debitur - SKI Finance (persetujuan nominal pencairan)
        $disetujui_debitur = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_debitur_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $disetujui_debitur->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$finance->id, $investmentOfficer->id]),
            'message' => 'Debitur [[nama.debitur]] telah menyetujui nominal pencairan sebesar Rp [[nominal]].',
        ]);

        // 5. Ditolak Debitur
        $ditolak_debitur = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_debitur_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $ditolak_debitur->id_notification_feature,
            'role_assigned' => json_encode([$finance->id, $investmentOfficer->id]),
            'message' => 'Debitur [[nama.debitur]] telah menolak nominal pencairan yang diajukan.',
        ]);

        // 6. Disetujui SKI Finance - Debitur
        $disetujui_ski_finance = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_ski_finance_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $disetujui_ski_finance->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$investmentOfficer->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh SKI Finance.',
        ]);

        // 7. Ditolak SKI Finance - Debitur
        $ditolak_ski_finance = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_ski_finance_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $ditolak_ski_finance->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$investmentOfficer->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh SKI Finance.',
        ]);

        // 8. Disetujui CEO SKI - SKI Finance
        $disetujui_ceo_finlog = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_ceo_finlog_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $disetujui_ceo_finlog->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$finance->id, $investmentOfficer->id, $debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh CEO SKI.',
        ]);

        // 9. Ditolak CEO SKI - SKI Finance
        $ditolak_ceo_finlog = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_ceo_finlog_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $ditolak_ceo_finlog->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$finance->id, $investmentOfficer->id, $debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh CEO SKI.',
        ]);

        // 10. Kontrak Digenerate - Debitur
        $kontrak_digenerate = NotificationFeature::firstOrCreate([
            'name' => 'kontrak_digenerate_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $kontrak_digenerate->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$debitur->id, $investmentOfficer->id, $ceo->id]),
            'message' => 'Kontrak pinjaman debitur [[nama.debitur]] telah berhasil dibuat.',
        ]);

        // 11. Bukti Transfer Diupload (Status Selesai) - Debitur dan SKI Finance
        $bukti_transfer_diupload = NotificationFeature::firstOrCreate([
            'name' => 'bukti_transfer_diupload_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        // Notifikasi untuk Debitur - Pinjaman berhasil dicairkan
        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $bukti_transfer_diupload->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$debitur->id, $investmentOfficer->id, $ceo->id]),
            'message' => 'Pinjaman debitur [[nama.debitur]] sebesar Rp [[nominal]] telah berhasil dicairkan. Status pinjaman: Selesai.',
        ]);

        // Notifikasi untuk SKI Finance - Konfirmasi penerimaan dana
        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $bukti_transfer_diupload->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$ceo->id, $investmentOfficer->id, $debitur->id]),
            'message' => 'Debitur [[nama.debitur]] telah mengonfirmasi penerimaan dana pinjaman.',
        ]);

        // =============================================
        // NOTIFIKATION UNTUK SURAT PERINGATAN 
        // =============================================

        $sp1 = NotificationFeature::firstOrCreate([
            'name' => 'surat_peringatan_2_pengembalian_dana_sfinlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $sp1->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Sehubungan dengan hasil evaluasi dan pemantauan yang telah kami lakukan, bersama email ini kami sampaikan bahwa perusahaan akan mengirimkan Surat Peringatan Pertama (SP 1) sebagai bentuk tindak lanjut atas hal-hal yang telah menjadi perhatian bersama. Adapun surat resmi terlampir pada email ini untuk dapat dipelajari dan ditindaklanjuti sebagaimana mestinya.',
        ]);

        $sp2 = NotificationFeature::firstOrCreate([
            'name' => 'surat_peringatan_2_pengembalian_dana_sfinlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $sp2->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Sehubungan dengan hasil evaluasi dan pemantauan yang telah kami lakukan, bersama email ini kami sampaikan bahwa perusahaan akan mengirimkan Surat Peringatan Kedua (SP 2) sebagai bentuk tindak lanjut atas hal-hal yang telah menjadi perhatian bersama. Adapun surat resmi terlampir pada email ini untuk dapat dipelajari dan ditindaklanjuti sebagaimana mestinya.',
        ]);

        $sp3 = NotificationFeature::firstOrCreate([
            'name' => 'surat_peringatan_3_pengembalian_dana_sfinlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $sp3->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Sehubungan dengan hasil evaluasi dan pemantauan yang telah kami lakukan, bersama email ini kami sampaikan bahwa perusahaan akan mengirimkan Surat Peringatan Ketiga (SP 3) sebagai bentuk tindak lanjut atas hal-hal yang telah menjadi perhatian bersama. Adapun surat resmi terlampir pada email ini untuk dapat dipelajari dan ditindaklanjuti sebagaimana mestinya.',
        ]);

        // ============================================
        // NOTIFICATION FEATURES UNTUK PENGEMBALIAN DANA
        // ============================================

        // 12. Pengembalian Dana - SKI Finance
        $pengembalian_dana = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_dana_pinjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengembalian_dana->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$ceo->id, $investmentOfficer->id, $debitur->id]),
            'message' => 'Debitur [[nama.debitur]] telah melakukan pembayaran pengembalian dana pinjaman.',
        ]);

        // 13. Pengembalian Dana Jatuh Tempo - Debitur dan SKI Finance
        $pengembalian_jatuh_tempo = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_dana_jatuh_tempo_finlog',
            'module' => 's_finlog',
        ]);

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengembalian_jatuh_tempo->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$debitur->id, $investmentOfficer->id, $ceo->id]),
            'message' => 'Pengembalian dana pinjaman debitur [[nama.debitur]] akan segera jatuh tempo pada [[tanggal.jatuh.tempo]].',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $pengembalian_jatuh_tempo->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$ceo->id, $investmentOfficer->id, $debitur->id]),
            'message' => 'Pengembalian dana pinjaman debitur [[nama.debitur]] akan segera jatuh tempo pada [[tanggal.jatuh.tempo]].',
        ]);

        // 14. Pengembalian Dana Telat - Debitur dan SKI Finance
        $pengembalian_telat = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_dana_telat_finlog',
            'module' => 's_finlog',
        ]);

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_telat->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Debitur [[nama.debitur]] telah melewati tanggal jatuh tempo dan belum melakukan pembayaran pengembalian dana.',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_telat->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] telah melewati tanggal jatuh tempo dan belum melakukan pembayaran pengembalian dana.',
        ]);

        // ============================================
        // NOTIFICATION FEATURES UNTUK PENGAJUAN INVESTASI
        // ============================================

        // Get atau create role Investor
        $investor = Role::firstOrCreate(['name' => 'Investor', 'guard_name' => 'web'], ['restriction' => 0]);

        // 15. Pengajuan Investasi Baru - SKI Finance
        $pengajuan_investasi_baru = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_investasi_baru_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_investasi_baru->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan investasi baru dari investor [[nama.investor]] telah diterima dan menunggu proses persetujuan.',
        ]);

        // 16. Disetujui SKI Finance - Investor
        $disetujui_ski_finance_investasi = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_ski_finance_investasi_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $disetujui_ski_finance_investasi->id_notification_feature,
            ], [
            'role_assigned' => json_encode([$investor->id, $ceo->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah disetujui oleh SKI Finance.',
        ]);

        // 17. Ditolak SKI Finance - Investor
        $ditolak_ski_finance_investasi = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_ski_finance_investasi_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $ditolak_ski_finance_investasi->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$investor->id, $ceo->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah ditolak oleh SKI Finance.',
        ]);

        // 18. Disetujui CEO SKI - SKI Finance
        $disetujui_ceo_ski_investasi = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_ceo_ski_investasi_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $disetujui_ceo_ski_investasi->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$finance->id, $ceo->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah disetujui oleh CEO SKI.',
        ]);

        // 19. Ditolak CEO SKI - SKI Finance
        $ditolak_ceo_ski_investasi = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_ceo_ski_investasi_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $ditolak_ceo_ski_investasi->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$finance->id, $ceo->id]),
            'message' => 'Pengajuan investasi dari investor [[nama.investor]] telah ditolak oleh CEO SKI.',
        ]);

        // 20. Kontrak Investasi Dibuat - Investor
        $kontrak_investasi_dibuat = NotificationFeature::firstOrCreate([
            'name' => 'kontrak_investasi_dibuat_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $kontrak_investasi_dibuat->id_notification_feature,
            'role_assigned' => json_encode([$investor->id]),
            'message' => 'Kontrak investasi untuk investor [[nama.investor]] telah berhasil dibuat.',
        ]);

        // 21. Investasi Berhasil Ditransfer - SKI Finance
        $investasi_berhasil_ditransfer = NotificationFeature::firstOrCreate([
            'name' => 'investasi_berhasil_ditransfer_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $investasi_berhasil_ditransfer->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$finance->id, $ceo->id]),
            'message' => 'Dana investasi dari investor [[nama.investor]] telah berhasil diterima. Status investasi: Selesai.',
        ]);

        // ============================================
        // NOTIFICATION FEATURES UNTUK PENYALURAN INVESTASI
        // ============================================

        // 22. Debitur Menerima Dana Investasi - Debitur
        $debitur_menerima_dana_investasi = NotificationFeature::firstOrCreate([
            'name' => 'debitur_menerima_dana_investasi_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $debitur_menerima_dana_investasi->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Debitur [[nama.debitur]] telah menerima dana dari investasi untuk project [[nama.project]].',
        ]);

        // 23. Debitur Pengembalian Dana Investasi - SKI Finance
        $debitur_pengembalian_dana_investasi = NotificationFeature::firstOrCreate([
            'name' => 'debitur_pengembalian_dana_investasi_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $debitur_pengembalian_dana_investasi->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] telah melakukan pengembalian dana investasi untuk project [[nama.project]].',
        ]);

        // 24. Pengembalian Investasi Jatuh Tempo - Debitur dan SKI Finance
        $pengembalian_investasi_jatuh_tempo = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_investasi_jatuh_tempo_finlog',
            'module' => 's_finlog',
        ]);

        // Notifikasi untuk Debitur
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_investasi_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengembalian dana investasi akan jatuh tempo pada [[tanggal.jatuh.tempo]]. Mohon memastikan kesiapan pembayaran.',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_investasi_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengembalian dana investasi akan jatuh tempo pada [[tanggal.jatuh.tempo]]. Mohon memastikan kesiapan pembayaran.',
        ]);

        // ============================================
        // NOTIFICATION FEATURES UNTUK PENGEMBALIAN INVESTASI KE INVESTOR
        // ============================================

        // 25. Pengembalian Investasi Ke Investor Jatuh Tempo - SKI Finance dan Investor
        $pengembalian_investasi_ke_investor_jatuh_tempo = NotificationFeature::firstOrCreate([
            'name' => 'pengembalian_investasi_ke_investor_jatuh_tempo_finlog',
            'module' => 's_finlog',
        ]);

        // Notifikasi untuk SKI Finance
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_investasi_ke_investor_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengembalian dana investasi akan jatuh tempo pada [[tanggal.jatuh.tempo]]. Mohon memastikan kesiapan pembayaran.',
        ]);

        // Notifikasi untuk Investor
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengembalian_investasi_ke_investor_jatuh_tempo->id_notification_feature,
            'role_assigned' => json_encode([$investor->id]),
            'message' => 'Pengembalian dana investasi akan jatuh tempo pada [[tanggal.jatuh.tempo]]. Mohon memastikan kesiapan pembayaran.',
        ]);

        // 26. Transfer Pengembalian Investasi Ke Investor - Investor
        $transfer_pengembalian_investasi_ke_investor = NotificationFeature::firstOrCreate([
            'name' => 'transfer_pengembalian_investasi_ke_investor_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::updateOrCreate([
            'notification_feature_id' => $transfer_pengembalian_investasi_ke_investor->id_notification_feature,
        ], [
            'role_assigned' => json_encode([$investor->id, $ceo->id]),
            'message' => 'SKI Finance telah melakukan transfer pengembalian dana investasi kepada investor [[nama.investor]].',
        ]);
    }
}

