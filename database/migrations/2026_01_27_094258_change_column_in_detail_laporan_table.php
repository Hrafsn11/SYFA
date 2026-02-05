<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_laporan', function (Blueprint $table) {
            $table->dropColumn('bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_laporan', function (Blueprint $table) {
            $table->integer('bulan')->nullable()->after('tahun');
        });
    }
};
