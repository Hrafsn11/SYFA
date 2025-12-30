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
        Schema::table('penyaluran_deposito_sfinlog', function (Blueprint $table) {
            $table->foreignUlid('id_cells_project')->nullable()->after('id_pengajuan_investasi_finlog')
                ->constrained('cells_projects', 'id_cells_project')->onDelete('set null');
            $table->foreignUlid('id_project')->nullable()->after('id_cells_project')
                ->constrained('projects', 'id_project')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyaluran_deposito_sfinlog', function (Blueprint $table) {
            $table->dropForeign(['id_cells_project']);
            $table->dropForeign(['id_project']);
            $table->dropColumn(['id_cells_project', 'id_project']);
        });
    }
};
