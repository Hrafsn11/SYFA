<?php

namespace Database\Seeders;

use App\Models\CellsProject;
use Illuminate\Database\Seeder;

class CellsProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['nama_project' => 'Loghos – Horeca'],
            ['nama_project' => 'Fresh Hub – MBG'],
            ['nama_project' => 'Cocorich – Arang'],
            ['nama_project' => 'PGO – Manpower Outsource'],
        ];

        foreach ($projects as $project) {
            CellsProject::firstOrCreate(
                ['nama_project' => $project['nama_project']],
                $project
            );
        }
    }
}
