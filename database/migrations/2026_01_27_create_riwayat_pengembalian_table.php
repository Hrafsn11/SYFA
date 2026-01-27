<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pengembalian_deposito', function (Blueprint $table) {
            $table->ulid('id_riwayat')->primary();
            $table->ulid('id_penyaluran_deposito');
            $table->decimal('nominal_dikembalikan', 15, 2);
            $table->date('tanggal_pengembalian');
            $table->string('bukti_pengembalian')->nullable();
            $table->text('catatan')->nullable();
            $table->ulid('diinput_oleh')->nullable();
            $table->timestamps();

            $table->foreign('id_penyaluran_deposito')
                ->references('id_penyaluran_deposito')
                ->on('penyaluran_deposito')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengembalian_deposito');
    }
};
