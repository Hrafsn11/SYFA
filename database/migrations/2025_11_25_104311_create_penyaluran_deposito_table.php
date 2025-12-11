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
        Schema::create('penyaluran_deposito', function (Blueprint $table) {
            $table->ulid('id_penyaluran_deposito')->primary();
            $table->foreignUlid('id_pengajuan_investasi')->constrained('pengajuan_investasi', 'id_pengajuan_investasi')->onDelete('cascade');
            $table->foreignUlid('id_debitur')->constrained('master_debitur_dan_investor', 'id_debitur')->onDelete('cascade');
            $table->decimal('nominal_yang_disalurkan', 20, 2);
            $table->date('tanggal_pengiriman_dana');
            $table->date('tanggal_pengembalian');
            $table->string('bukti_pengembalian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyaluran_deposito');
    }
};
