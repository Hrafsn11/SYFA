<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nilai_laporan', function (Blueprint $table) {
            $table->ulid('id_nilai_laporan')->primary();

            $table->ulid('id_detail_laporan')->nullable();
            $table->ulid('id_laporan_investasi')->nullable();

            $table->foreign('id_detail_laporan')->references('id_detail_laporan')->on('detail_laporan')->onDelete('cascade');
            $table->foreign('id_laporan_investasi')->references('id_laporan_investasi')->on('laporan_investasi')->onDelete('cascade');

            $table->integer('bulan')->nullable();
            $table->double('nilai', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_laporan');
    }
};
