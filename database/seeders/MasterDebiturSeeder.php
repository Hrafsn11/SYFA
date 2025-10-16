<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterDebiturSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = [
            [
                'id_kol' => 1,
                'nama_debitur' => 'Techno Infinity',
                'alamat' => 'Bandung',
                'email' => 'Techno@gmail.com',
                'nama_ceo' => 'Cahyo',
                'nama_bank' => 'BCA',
                'no_rek' => '12345678',
                    'flagging' => 'ya',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kol' => 2,
                'nama_debitur' => 'Malaka',
                'alamat' => 'Jakarta',
                'email' => 'Malaka@gmail.com',
                'nama_ceo' => 'Budi',
                'nama_bank' => 'BRI',
                'no_rek' => '12345678',
                    'flagging' => 'tidak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($rows as $row) {
            DB::table('master_debitur_dan_investor')->updateOrInsert(
                ['email' => $row['email']],
                $row
            );
        }
    }
}
