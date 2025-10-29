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
        Schema::create('bukti_peminjaman', function (Blueprint $table) {
            $table->ulid('id_bukti_peminjaman')->primary();
            $table->ulid('id_pengajuan_peminjaman');
            $table->string('no_invoice')->nullable();
            $table->string('no_kontrak')->nullable();
            $table->string('nama_client')->nullable();
            $table->decimal('nilai_invoice', 15, 2)->nullable();
            $table->decimal('nilai_pinjaman', 15, 2)->nullable();
            $table->decimal('nilai_bagi_hasil', 15, 2)->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('dokumen_invoice')->nullable();
            $table->string('dokumen_kontrak')->nullable();
            $table->string('dokumen_so')->nullable();
            $table->string('dokumen_bast')->nullable();
            $table->date('kontrak_date')->nullable();
            $table->string('dokumen_lainnya')->nullable();
            $table->string('nama_barang')->nullable();
            $table->timestamps();


            $table->foreign('id_pengajuan_peminjaman')->references('id_pengajuan_peminjaman')->on('pengajuan_peminjaman')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_peminjaman');
    }
};
