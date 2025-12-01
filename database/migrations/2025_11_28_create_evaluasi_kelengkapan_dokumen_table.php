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
        Schema::create('evaluasi_kelengkapan_dokumen', function (Blueprint $table) {
            $table->ulid('id_kelengkapan_dokumen')->primary();
            
            // Foreign key ke evaluasi restrukturisasi (ditambahkan di migration terpisah)
            $table->ulid('id_evaluasi_restrukturisasi');
            
            // ========================================
            // SECTION A: KELENGKAPAN DOKUMEN
            // ========================================
            $table->string('nama_dokumen'); // ex: "KTP PIC", "NPWP Perusahaan", dll
            $table->enum('status', ['Ya', 'Tidak']); // Apakah dokumen lengkap?
            $table->text('catatan')->nullable(); // Catatan evaluator
            
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_kelengkapan_dokumen');
    }
};
