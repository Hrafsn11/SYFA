<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNomorKontrakToPengajuanInvestasiTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pengajuan_investasi', function (Blueprint $table) {
            $table->string('nomor_kontrak')->unique()->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('pengajuan_investasi', function (Blueprint $table) {
            $table->dropColumn('nomor_kontrak');
        });
    }
}