<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_sumber_pendanaan_eksternal', function (Blueprint $table) {
            $table->increments('id_instansi');
            $table->string('nama_instansi', 255);
            $table->integer('persentase_bagi_hasil')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_sumber_pendanaan_eksternal');
    }
};
