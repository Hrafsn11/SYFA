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
        Schema::create('evaluasi_kelayakan_debitur', function (Blueprint $table) {
            $table->ulid('id_kelayakan_debitur')->primary();
            
            // Foreign key ke evaluasi restrukturisasi (ditambahkan di migration terpisah)
            $table->ulid('id_evaluasi_restrukturisasi');
            
            // ========================================
            // SECTION B: KELAYAKAN DEBITUR
            // ========================================
            $table->text('kriteria'); // ex: "Riwayat pembayaran sebelumnya baik (DPD ≤ 30 hari)"
            $table->enum('status', ['Ya', 'Tidak']); // Apakah memenuhi kriteria?
            $table->text('catatan')->nullable(); // Catatan evaluator
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_kelayakan_debitur');
    }
};
