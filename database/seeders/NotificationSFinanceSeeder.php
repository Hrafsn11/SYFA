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

        $submit_review_peminjaman = NotificationFeature::firstOrCreate([
            'name' => 'submit_review_peminjaman',
            'module' => 's_finance',
        ]);

        NotificationFeatureDetail::firstOrCreate([
            'notification_feature_id' => $submit_review_peminjaman->id_notification_feature,
            'role_assigned' => json_encode([$finance->id]),
            'message' => 'Pengajuan pinjaman baru telah diterima dari debitur [[nama.debitur]]. Silakan lakukan proses verifikasi.',
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
