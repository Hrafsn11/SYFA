<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\MasterKolSeeder::class,
            \Database\Seeders\MasterDebiturSeeder::class,
            \Database\Seeders\MasterSumberPendanaanEksternalSeeder::class,
        ]);
    }
}
