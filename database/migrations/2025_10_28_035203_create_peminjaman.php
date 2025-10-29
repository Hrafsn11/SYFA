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
        Schema::create('pengajuan_peminjaman', function (Blueprint $table) {
            $table->ulid('id_pengajuan_peminjaman')->primary();
            $table->string('nomor_peminjaman')->index()->nullable();
            $table->unsignedInteger('id_debitur');
            $table->enum('sumber_pembiayaan', ['eksternal', 'internal']);
            $table->unsignedInteger('id_instansi')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nama_rekening')->nullable();
            $table->string('lampiran_sid')->nullable();
            $table->string('nilai_kol')->nullable();
            $table->text('tujuan_pembiayaan')->nullable();
            $table->enum('jenis_pembiayaan', ['PO Financing', 'Invoice Financing', 'Installment', 'Factoring'])->nullable();
            $table->decimal('total_pinjaman', 15, 2)->nullable();
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->nullable();
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->nullable();
            $table->text('catatan_lainnya')->nullable();
            $table->enum('tenor_pembayaran', ['3', '6', '9', '12'])->nullable();
            $table->decimal('persentase_bagi_hasil', 8, 2)->nullable();
            $table->decimal('pps', 15, 2)->nullable();
            $table->decimal('s_finance', 15, 2)->nullable();
            $table->decimal('yang_harus_dibayarkan', 15, 2)->nullable();
            $table->decimal('total_nominal_yang_dialihkan', 15, 2)->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('id_debitur');
            $table->index('status');

            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onDelete('cascade');
            $table->foreign('id_instansi')->references('id_instansi')->on('master_sumber_pendanaan_eksternal')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_peminjaman');
    }
};
