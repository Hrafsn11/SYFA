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
        Schema::create('evaluasi_analisa_restrukturisasi', function (Blueprint $table) {
            $table->ulid('id_analisa_restrukturisasi')->primary();
            
            // Foreign key ke evaluasi restrukturisasi (ditambahkan di migration terpisah)
            $table->ulid('id_evaluasi_restrukturisasi');
            
            // ========================================
            // SECTION C: ANALISA RESTRUKTURISASI
            // ========================================
            $table->text('aspek'); // ex: "Jenis restrukturisasi yang diajukan sesuai kebutuhan"
            $table->string('evaluasi'); // ex: "Sesuai", "Tidak", "Memadai", "Defisit", "Layak", "Rendah", "Sedang", "Tinggi"
            $table->text('catatan')->nullable(); // Catatan evaluator
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_analisa_restrukturisasi');
    }
};
