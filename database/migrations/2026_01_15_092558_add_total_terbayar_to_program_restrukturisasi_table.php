<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->decimal('total_terbayar', 15, 2)->default(0)->after('total_cicilan');
        });
    }

    public function down(): void
    {
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->dropColumn('total_terbayar');
        });
    }
};
