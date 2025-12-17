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
        if (!Schema::hasTable('penyaluran_deposito_sfinlog')) {
            Schema::create('penyaluran_deposito_sfinlog', function (Blueprint $table) {
                $table->ulid('id_penyaluran_deposito_sfinlog')->primary();
                $table->foreignUlid('id_pengajuan_investasi_finlog')->constrained('pengajuan_investasi_finlog', 'id_pengajuan_investasi_finlog')->onDelete('cascade');
                $table->decimal('nominal_yang_disalurkan', 20, 2);
                $table->date('tanggal_pengiriman_dana');
                $table->date('tanggal_pengembalian');
                $table->string('bukti_pengembalian')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyaluran_deposito_sfinlog');
    }
};
