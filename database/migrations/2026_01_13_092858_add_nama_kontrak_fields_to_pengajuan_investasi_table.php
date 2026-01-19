<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan_investasi', function (Blueprint $table) {

            $table->string('nama_pic_kontrak')->nullable()->after('nama_investor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_investasi', function (Blueprint $table) {
            $table->dropColumn('nama_pic_kontrak');
        });
    }
};
