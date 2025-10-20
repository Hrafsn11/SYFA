<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfigMatrixPinjaman;

class ConfigMatrixPinjamanSeeder extends Seeder
{
    public function run()
    {
        $samples = [
            ['nominal'=>1000000,'approve_oleh'=>'Admin'],
            ['nominal'=>5000000,'approve_oleh'=>'Manager'],
            ['nominal'=>10000000,'approve_oleh'=>'Director'],
        ];
        foreach($samples as $s) ConfigMatrixPinjaman::create($s);
    }
}
