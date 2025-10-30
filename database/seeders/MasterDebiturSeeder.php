<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MasterDebiturDanInvestor;
use App\Models\MasterKol;

class MasterDebiturSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = User::where('email', 'admin@admin.com')->first();
        
        if (!$superAdmin) {
            $this->command->warn('Super Admin not found. Please run RolePermissionSeeder first.');
            return;
        }

        MasterDebiturDanInvestor::updateOrInsert(
            [
                'user_id' => $superAdmin->id,
                'flagging' => 'tidak' 
            ],
            [
                'user_id' => $superAdmin->id,
                'id_kol' => MasterKol::where('kol', 0)->first()?->id_kol ?? 1,
                'nama' => 'Super Admin',
                'alamat' => 'Jakarta',
                'email' => $superAdmin->email,
                'no_telepon' => '081234567890',
                'status' => 'active',
                'deposito' => 'reguler', 
                'nama_ceo' => 'Super Admin',
                'nama_bank' => 'BCA',
                'no_rek' => '1234567890',
                'flagging' => 'tidak', 
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        MasterDebiturDanInvestor::updateOrInsert(
            [
                'user_id' => $superAdmin->id,
                'flagging' => 'ya' 
            ],
            [
                'user_id' => $superAdmin->id,
                'id_kol' => MasterKol::where('kol', 0)->first()?->id_kol ?? 1, 
                'nama' => 'Super Admin',
                'alamat' => 'Jakarta',
                'email' => $superAdmin->email,
                'no_telepon' => '081234567890',
                'status' => 'active',
                'deposito' => 'reguler',
                'nama_ceo' => 'Super Admin', 
                'nama_bank' => 'BCA',
                'no_rek' => '1111222233',
                'flagging' => 'ya',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('✓ Created 1 debitur record for Super Admin');
        $this->command->info('✓ Created 1 investor record for Super Admin');
        $this->command->info('✓ All records linked to: ' . $superAdmin->email);
    }
}
