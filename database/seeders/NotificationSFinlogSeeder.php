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

        // Notification Features untuk Peminjaman SFinlog
        // User akan mengatur message dan role_assigned sendiri karena sangat panjang
        
        // 1. Pengajuan Disubmit - SKI Finance
        $pengajuan_disubmit = NotificationFeature::firstOrCreate([
            'name' => 'pengajuan_disubmit_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $pengajuan_disubmit->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan pinjaman baru telah diterima dari debitur [[nama.debitur]]. Silakan lakukan proses verifikasi.',
        ]);

        // 2. Disetujui Investment Officer
        $disetujui_io = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_investment_officer_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        // 3. Ditolak Investment Officer
        $ditolak_io = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_investment_officer_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        // 4. Disetujui Debitur - SKI Finance (persetujuan nominal pencairan)
        $disetujui_debitur = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_debitur_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $disetujui_debitur->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] telah menyetujui nominal pencairan sebesar Rp [[nominal]].',
        ]);

        // 5. Ditolak Debitur
        $ditolak_debitur = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_debitur_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        // 6. Disetujui SKI Finance - Debitur
        $disetujui_ski_finance = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_ski_finance_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $disetujui_ski_finance->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh SKI Finance.',
        ]);

        // 7. Ditolak SKI Finance - Debitur
        $ditolak_ski_finance = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_ski_finance_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $ditolak_ski_finance->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh SKI Finance.',
        ]);

        // 8. Disetujui CEO SKI - SKI Finance
        $disetujui_ceo_finlog = NotificationFeature::firstOrCreate([
            'name' => 'disetujui_ceo_finlog_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $disetujui_ceo_finlog->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah disetujui oleh CEO SKI.',
        ]);

        // 9. Ditolak CEO SKI - SKI Finance
        $ditolak_ceo_finlog = NotificationFeature::firstOrCreate([
            'name' => 'ditolak_ceo_finlog_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $ditolak_ceo_finlog->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan pinjaman debitur [[nama.debitur]] telah ditolak oleh CEO SKI.',
        ]);

        // 10. Kontrak Digenerate - Debitur
        $kontrak_digenerate = NotificationFeature::firstOrCreate([
            'name' => 'kontrak_digenerate_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $kontrak_digenerate->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Kontrak pinjaman debitur [[nama.debitur]] telah berhasil dibuat.',
        ]);

        // 11. Bukti Transfer Diupload (Status Selesai) - Debitur dan SKI Finance
        $bukti_transfer_diupload = NotificationFeature::firstOrCreate([
            'name' => 'bukti_transfer_diupload_peminjaman_finlog',
            'module' => 's_finlog',
        ]);

        // Notifikasi untuk Debitur - Pinjaman berhasil dicairkan
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $bukti_transfer_diupload->id_notification_feature,
            'role_assigned' => json_encode([$debitur->id]),
            'message' => 'Pinjaman debitur [[nama.debitur]] sebesar Rp [[nominal]] telah berhasil dicairkan. Status pinjaman: Selesai.',
        ]);

        // Notifikasi untuk SKI Finance - Konfirmasi penerimaan dana
        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $bukti_transfer_diupload->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Debitur [[nama.debitur]] telah mengonfirmasi penerimaan dana pinjaman.',
        ]);
    }
}

