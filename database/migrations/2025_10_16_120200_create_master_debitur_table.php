<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_debitur', function (Blueprint $table) {
            $table->increments('id_debitur');

            $table->unsignedInteger('id_kol');
            $table->unsignedInteger('id_instansi')->nullable();

            $table->string('nama_debitur', 255);
            $table->string('alamat', 255)->nullable();
            $table->string('email', 255)->nullable();

            $table->string('nama_ceo', 255)->nullable();
            $table->string('nama_bank', 255)->nullable();
            $table->string('no_rek', 100)->nullable();

            $table->timestamps();

            $table->foreign('id_kol')
                  ->references('id_kol')->on('master_kol')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('id_instansi')
                  ->references('id_instansi')->on('master_sumber_pendanaan_eksternal')
                  ->onDelete('set null')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::table('master_debitur', function (Blueprint $table) {
            $table->dropForeign(['id_kol']);
            $table->dropForeign(['id_instansi']);
        });
        Schema::dropIfExists('master_debitur');
    }
};
