<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE master_debitur_dan_investor MODIFY COLUMN status ENUM('active', 'non active', 'locked') DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::table('master_debitur_dan_investor')
            ->where('status', 'locked')
            ->update(['status' => 'non active']);
            
        DB::statement("ALTER TABLE master_debitur_dan_investor MODIFY COLUMN status ENUM('active', 'non active') DEFAULT 'active'");
    }
};
