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
        Schema::create('evaluasi_pengajuan_restrukturisasi', function (Blueprint $table) {
            $table->ulid('id_evaluasi_restrukturisasi')->primary();
            
            // Foreign key ke pengajuan restrukturisasi
            $table->ulid('id_pengajuan_restrukturisasi');
            $table->foreign('id_pengajuan_restrukturisasi', 'fk_eval_to_pengajuan')
                ->references('id_pengajuan_restrukturisasi')
                ->on('pengajuan_restrukturisasi')
                ->onDelete('cascade');
            
            // ========================================
            // SECTION D: REKOMENDASI TEAM EVALUASI
            // ========================================
            $table->string('rekomendasi')->nullable(); // Setuju, Tolak, Opsi Lain
            $table->text('justifikasi_rekomendasi')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_pengajuan_restrukturisasi');
    }
};
