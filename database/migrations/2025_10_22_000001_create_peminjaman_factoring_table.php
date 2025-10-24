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
        Schema::create('peminjaman_factoring', function (Blueprint $table) {
            $table->bigIncrements('id_factoring');
            $table->unsignedBigInteger('id_debitur');
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->string('nama_rekening')->nullable();
            $table->decimal('total_nominal_yang_dialihkan', 15, 2)->default(0.00);
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->default(0.00);
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->default(0.00);
            $table->string('catatan_lainnya')->nullable();
            $table->string('status', 50)->nullable();
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
        Schema::dropIfExists('peminjaman_factoring');
    }
};
