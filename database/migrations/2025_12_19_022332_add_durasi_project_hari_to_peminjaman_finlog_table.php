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
        Schema::table('peminjaman_finlog', function (Blueprint $table) {
            $table->integer('durasi_project_hari')->after('durasi_project')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_finlog', function (Blueprint $table) {
            $table->dropColumn('durasi_project_hari');
        });
    }
};
