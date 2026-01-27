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
        Schema::create('riwayat_pengembalian_deposito_sfinlog', function (Blueprint $table) {
            $table->ulid('id_riwayat_pengembalian_deposito_sfinlog')->primary();
            $table->string('id_penyaluran_deposito_sfinlog');
            $table->decimal('nominal_dikembalikan', 20, 2);
            $table->date('tanggal_pengembalian');
            $table->string('bukti_pengembalian')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_penyaluran_deposito_sfinlog', 'fk_riwayat_penyaluran_sfinlog')
                ->references('id_penyaluran_deposito_sfinlog')
                ->on('penyaluran_deposito_sfinlog')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengembalian_deposito_sfinlog');
    }
};
