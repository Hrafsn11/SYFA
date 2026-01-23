<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE program_restrukturisasi MODIFY COLUMN status ENUM('Menunggu Generate Kontrak', 'Berjalan', 'Lunas', 'Tertunda') DEFAULT 'Menunggu Generate Kontrak'");

        DB::table('program_restrukturisasi')
            ->whereNull('kontrak_generated_at')
            ->update(['status' => 'Menunggu Generate Kontrak']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('program_restrukturisasi')
            ->where('status', 'Menunggu Generate Kontrak')
            ->update(['status' => 'Berjalan']);

        DB::statement("ALTER TABLE program_restrukturisasi MODIFY COLUMN status ENUM('Berjalan', 'Lunas', 'Tertunda') DEFAULT 'Berjalan'");
    }
};
