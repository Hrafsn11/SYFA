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
        Schema::create('detail_laporan', function (Blueprint $table) {
            $table->ulid('id_detail_laporan')->primary();
            $table->ulid('id_laporan_investasi')->nullable();
            $table->foreign('id_laporan_investasi')->references('id_laporan_investasi')->on('laporan_investasi')->onDelete('cascade');
            $table->ulid('parent_id')->nullable()->comment('refer to id_detail_laporan for hierarchical structure');

            $table->year('tahun')->nullable();
            $table->integer('bulan')->nullable();
            $table->string('komponen')->nullable();
            $table->double('nilai')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_laporan');
    }
};
