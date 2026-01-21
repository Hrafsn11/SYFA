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
        Schema::create('laporan_investasi', function (Blueprint $table) {
            $table->ulid('id_laporan_investasi')->primary();

            $table->string('nama_sbu')->nullable();
            $table->year('tahun')->nullable();
            $table->string('edit_by')->nullable();
            $table->integer('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_investasi');
    }
};
