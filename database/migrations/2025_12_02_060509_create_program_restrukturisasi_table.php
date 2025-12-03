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
        Schema::create('program_restrukturisasi', function (Blueprint $table) {
            $table->ulid('id_program_restrukturisasi')->primary();
            
            // Foreign key ke pengajuan restrukturisasi
            $table->ulid('id_pengajuan_restrukturisasi');
            $table->foreign('id_pengajuan_restrukturisasi')
                ->references('id_pengajuan_restrukturisasi')
                ->on('pengajuan_restrukturisasi')
                ->onDelete('cascade');
            
            // Parameter perhitungan
            $table->enum('metode_perhitungan', ['Flat', 'Anuitas'])->default('Flat');
            $table->decimal('plafon_pembiayaan', 15, 2);
            $table->decimal('suku_bunga_per_tahun', 5, 2); // percentage
            $table->integer('jangka_waktu_total'); // dalam bulan
            $table->integer('masa_tenggang'); // dalam bulan
            $table->date('tanggal_mulai_cicilan');
            
            // Summary
            $table->decimal('total_pokok', 15, 2)->default(0);
            $table->decimal('total_margin', 15, 2)->default(0);
            $table->decimal('total_cicilan', 15, 2)->default(0);
            
            // Metadata
            $table->ulid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->ulid('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_restrukturisasi');
    }
};
