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
    }
}
