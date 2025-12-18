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
        Schema::create('pengembalian_investasi_finlog', function (Blueprint $table) {
            $table->ulid('id_pengembalian_investasi_finlog')->primary();
            $table->ulid('id_pengajuan_investasi_finlog');

            $table->decimal('dana_pokok_dibayar', 15, 2);
            $table->decimal('bagi_hasil_dibayar', 15, 2)->default(0);
            $table->decimal('total_dibayar', 15, 2);

            $table->string('bukti_transfer')->nullable();
            $table->date('tanggal_pengembalian');

            $table->ulid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('id_pengajuan_investasi_finlog', 'fk_pengembalian_finlog_pengajuan')
                ->references('id_pengajuan_investasi_finlog')
                ->on('pengajuan_investasi_finlog')
                ->onDelete('cascade');

            $table->foreign('created_by', 'fk_pengembalian_finlog_created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index('id_pengajuan_investasi_finlog', 'idx_pif_pengembalian');
            $table->index('tanggal_pengembalian', 'idx_tgl_pengembalian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_investasi_finlog');
    }
};


