<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSumberPendanaanEksternalSeeder extends Seeder
{
    public function run()
    {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('master_sumber_pendanaan_eksternal')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $rows = [
            ['nama_instansi' => 'Bank ABC', 'persentase_bagi_hasil' => 5],
            ['nama_instansi' => 'Lembaga XYZ', 'persentase_bagi_hasil' => 7],
            ['nama_instansi' => 'Investor 123', 'persentase_bagi_hasil' => 6],
        ];

        foreach ($rows as $r) {
            DB::table('master_sumber_pendanaan_eksternal')->insert($r);
        }
    }
}
