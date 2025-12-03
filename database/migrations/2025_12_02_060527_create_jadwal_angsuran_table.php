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
        Schema::create('jadwal_angsuran', function (Blueprint $table) {
            $table->ulid('id_jadwal_angsuran')->primary();
            
            // Foreign key ke program restrukturisasi
            $table->ulid('id_program_restrukturisasi');
            $table->foreign('id_program_restrukturisasi')
                ->references('id_program_restrukturisasi')
                ->on('program_restrukturisasi')
                ->onDelete('cascade');
            
            // Data angsuran
            $table->integer('no'); // urutan angsuran
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('pokok', 15, 2)->default(0);
            $table->decimal('margin', 15, 2)->default(0);
            $table->decimal('total_cicilan', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->boolean('is_grace_period')->default(false);
            
            // Status pembayaran (untuk tracking nanti)
            $table->enum('status', ['Belum Jatuh Tempo', 'Jatuh Tempo', 'Lunas', 'Tertunda'])->default('Belum Jatuh Tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('nominal_bayar', 15, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_angsuran');
    }
};
