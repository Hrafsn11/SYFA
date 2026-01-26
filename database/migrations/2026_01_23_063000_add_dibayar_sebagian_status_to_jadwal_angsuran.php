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
        DB::statement("ALTER TABLE jadwal_angsuran MODIFY COLUMN status ENUM('Belum Jatuh Tempo', 'Jatuh Tempo', 'Lunas', 'Tertunda', 'Dibayar Sebagian') NULL DEFAULT 'Belum Jatuh Tempo'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE jadwal_angsuran MODIFY COLUMN status ENUM('Belum Jatuh Tempo', 'Jatuh Tempo', 'Lunas', 'Tertunda') NULL DEFAULT 'Belum Jatuh Tempo'");
    }
};
