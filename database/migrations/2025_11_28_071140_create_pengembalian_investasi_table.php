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
        Schema::create('pengembalian_investasi', function (Blueprint $table) {
            $table->string('id_pengembalian_investasi', 26)->primary();
            $table->string('id_pengajuan_investasi', 26);
            
            $table->decimal('dana_pokok_dibayar', 15, 2);
            $table->decimal('bagi_hasil_dibayar', 15, 2);
            $table->decimal('total_dibayar', 15, 2);
            
            $table->string('bukti_transfer')->nullable();
            $table->date('tanggal_pengembalian');
            $table->text('keterangan')->nullable();
            
            $table->string('created_by', 26)->nullable();
            $table->timestamps();
            
            $table->foreign('id_pengajuan_investasi')
                ->references('id_pengajuan_investasi')
                ->on('pengajuan_investasi')
                ->onDelete('cascade');
            
            $table->index('id_pengajuan_investasi');
            $table->index('tanggal_pengembalian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_investasi');
    }
};
