<?php

namespace Database\Seeders;

use App\Models\MasterKol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterKolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('master_kol')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $rows = [
            ['kol' => 0, 'persentase_pencairan' => 0.00, 'jmlh_hari_keterlambatan' => 0], // KOL Default untuk Debitur Baru
            ['kol' => 1, 'persentase_pencairan' => 15.00, 'jmlh_hari_keterlambatan' => 0],
            ['kol' => 2, 'persentase_pencairan' => 45.00, 'jmlh_hari_keterlambatan' => 1],
            ['kol' => 3, 'persentase_pencairan' => 75.00, 'jmlh_hari_keterlambatan' => 30],
            ['kol' => 4, 'persentase_pencairan' => 95.00, 'jmlh_hari_keterlambatan' => 60],
            ['kol' => 5, 'persentase_pencairan' => 100.00, 'jmlh_hari_keterlambatan' => 180],
        ];

        foreach ($rows as $r) {
            MasterKol::create($r);
        }
    }
}
