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
        Schema::create('persetujuan_komite_restrukturisasi', function (Blueprint $table) {
            $table->ulid('id_persetujuan_komite')->primary();
            
            // Foreign key ke evaluasi restrukturisasi (ditambahkan di migration terpisah)
            $table->ulid('id_evaluasi_restrukturisasi');
            
            $table->string('nama_anggota'); // Nama lengkap anggota komite
            $table->string('jabatan'); // Jabatan (ex: Kepala Divisi Kredit, Anggota Komite)
            $table->date('tanggal_persetujuan'); // Tanggal memberikan persetujuan
            $table->string('ttd_digital')->nullable(); // Path ke file tanda tangan digital
            
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan_komite_restrukturisasi');
    }
};
