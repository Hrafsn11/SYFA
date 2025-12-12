<?php

namespace Database\Seeders;

use App\Models\CellsProject;
use App\Models\Project;
use Illuminate\Database\Seeder;

class CellsProjectSeeder extends Seeder
{
    public function run(): void
    {
        $cellsProjects = [
            [
                'nama_cells_bisnis' => 'Loghos – Horeca',
                'nama_pic' => 'John Doe',
                'alamat' => 'Jakarta',
                'deskripsi_bidang' => 'Horeca Business',
                'projects' => ['Project Loghos A', 'Project Loghos B']
            ],
            [
                'nama_cells_bisnis' => 'Fresh Hub – MBG',
                'nama_pic' => 'Jane Smith',
                'alamat' => 'Surabaya',
                'deskripsi_bidang' => 'Food & Beverage',
                'projects' => ['Project Fresh Hub A']
            ],
            [
                'nama_cells_bisnis' => 'Cocorich – Arang',
                'nama_pic' => 'Robert Johnson',
                'alamat' => 'Bandung',
                'deskripsi_bidang' => 'Charcoal Production',
                'projects' => ['Project Cocorich A', 'Project Cocorich B', 'Project Cocorich C']
            ],
            [
                'nama_cells_bisnis' => 'PGO – Manpower Outsource',
                'nama_pic' => 'Sarah Williams',
                'alamat' => 'Medan',
                'deskripsi_bidang' => 'Manpower Services',
                'projects' => ['Project PGO A']
            ],
        ];

        foreach ($cellsProjects as $cellsData) {
            $projectNames = $cellsData['projects'];
            unset($cellsData['projects']);
            
            $cellsProject = CellsProject::firstOrCreate(
                ['nama_cells_bisnis' => $cellsData['nama_cells_bisnis']],
                $cellsData
            );
            
            // Create projects for this cells project
            foreach ($projectNames as $projectName) {
                Project::firstOrCreate(
                    [
                        'id_cells_project' => $cellsProject->id_cells_project,
                        'nama_project' => $projectName
                    ]
                );
            }
        }
    }
}
