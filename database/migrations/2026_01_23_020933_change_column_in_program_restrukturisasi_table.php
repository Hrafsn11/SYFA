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
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->double('nominal_yg_disetujui', 15, 2)->nullable()->after('jangka_waktu_total')->comment('khusus untuk pengurangan margin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->dropColumn('nominal_yg_disetujui');
        });
    }
};
