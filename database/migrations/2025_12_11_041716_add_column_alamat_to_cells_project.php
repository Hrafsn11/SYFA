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
        Schema::table('cells_projects', function (Blueprint $table) {
            $table->string('alamat')->after('nama_project')->nullable();
            $table->string('deskripsi_bidang')->after('alamat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cells_project', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'deskripsi_bidang']);
        });
    }
};
