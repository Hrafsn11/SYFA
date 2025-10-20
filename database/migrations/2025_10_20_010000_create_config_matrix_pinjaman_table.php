<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('config_matrix_pinjaman', function (Blueprint $table) {
            $table->increments('id_matrix_pinjaman');
            $table->decimal('nominal', 15, 2);
            $table->string('approve_oleh', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('config_matrix_pinjaman');
    }
};
