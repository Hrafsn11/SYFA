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
        Schema::create('summary_laporans', function (Blueprint $table) {
            $table->ulid('id_summary_laporan')->primary();
            $table->ulid('id_laporan_investasi')->nullable();
            $table->foreign('id_laporan_investasi')->references('id_laporan_investasi')->on('laporan_investasi')->onDelete('cascade');

            $table->year('tahun')->nullable();
            $table->string('komponen')->nullable();
            $table->double('sum')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summary_laporans');
    }
};
