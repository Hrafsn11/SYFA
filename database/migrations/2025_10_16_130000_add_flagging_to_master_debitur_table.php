<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->enum('flagging', ['ya', 'tidak'])->default('tidak')->after('nama_debitur');
        });
    }

    public function down()
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->dropColumn('flagging');
        });
    }
};
