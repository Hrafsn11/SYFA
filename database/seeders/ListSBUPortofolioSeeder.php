<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListSBUPortofolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list_sbu = \App\Enums\NamaSBUEnum::getConstants();

        foreach ($list_sbu as $sbu) {
            \App\Models\LaporanInvestasi::firstOrCreate([
                'nama_sbu' => $sbu,
                'tahun' => date('Y'),
                'edit_by' => 'seeder',
            ]);
        }
    }
}
