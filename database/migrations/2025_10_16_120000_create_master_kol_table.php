<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_kol', function (Blueprint $table) {
            $table->increments('id_kol');
            $table->integer('kol');
            $table->decimal('persentase_pencairan', 5, 2)->default(0);
            $table->integer('jmlh_hari_keterlambatan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_kol');
    }
};
