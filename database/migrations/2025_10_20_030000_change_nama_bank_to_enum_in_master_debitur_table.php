<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $banks = [
            'BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'
        ];

        $enumList = implode("','", array_map(function($s){ return str_replace("'","\\'", $s); }, $banks));

        DB::statement("ALTER TABLE `master_debitur_dan_investor` MODIFY `nama_bank` ENUM('".$enumList."') NULL");
    }

    public function down()
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->string('nama_bank', 255)->nullable()->change();
        });
    }
};
