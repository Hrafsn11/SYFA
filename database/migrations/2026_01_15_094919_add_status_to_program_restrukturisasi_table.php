<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->enum('status', ['Berjalan', 'Lunas', 'Tertunda'])->default('Berjalan')->after('total_terbayar');
        });
    }

    public function down(): void
    {
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
