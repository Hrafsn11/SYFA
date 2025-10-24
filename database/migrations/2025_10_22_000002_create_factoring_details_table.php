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
        Schema::create('factoring_details', function (Blueprint $table) {
            $table->bigIncrements('id_factoring_detail');
            $table->unsignedBigInteger('id_factoring');
            $table->string('no_kontrak')->nullable();
            $table->string('nama_client')->nullable();
            $table->decimal('nilai_invoice', 15, 2)->default(0.00);
            $table->decimal('nilai_pinjaman', 15, 2)->default(0.00);
            $table->decimal('nilai_bagi_hasil', 15, 2)->default(0.00);
            $table->date('kontrak_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('dokumen_invoice', 191)->nullable();
            $table->string('dokumen_so', 191)->nullable();
            $table->string('dokumen_bast', 191)->nullable();
            $table->string('dokumen_kontrak', 191)->nullable();
            $table->timestamps();

            $table->foreign('id_factoring')->references('id_factoring')->on('peminjaman_factoring')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factoring_details');
    }
};
