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
        Schema::create('peminjaman_finlog', function (Blueprint $table) {
            $table->ulid('id_peminjaman_finlog')->primary();
            $table->string('nomor_peminjaman')->unique()->nullable();
            $table->ulid('id_debitur');
            $table->ulid('id_cells_project')->nullable();

            $table->string('nama_project')->nullable();
            $table->integer('durasi_project')->nullable();
            $table->string('nib_perusahaan')->nullable();

            $table->decimal('nilai_pinjaman', 15, 2)->nullable();
            $table->decimal('presentase_bagi_hasil', 5, 2)->nullable();
            $table->decimal('nilai_bagi_hasil', 15, 2)->nullable();
            $table->decimal('total_pinjaman', 15, 2)->nullable();

            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->integer('top')->nullable();
            $table->date('rencana_tgl_pengembalian')->nullable();

            $table->string('dokumen_mitra')->nullable();
            $table->string('form_new_customer')->nullable();
            $table->string('dokumen_kerja_sama')->nullable();
            $table->string('dokumen_npa')->nullable();
            $table->string('akta_perusahaan')->nullable();
            $table->string('ktp_owner')->nullable();
            $table->string('ktp_pic')->nullable();
            $table->string('surat_izin_usaha')->nullable();

            $table->text('catatan')->nullable();
            $table->enum('status', ['Draft', 'Menunggu Persetujuan', 'Disetujui', 'Ditolak', 'Dicairkan', 'Selesai'])->default('Draft');
            $table->integer('current_step')->default(1);

            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onDelete('cascade');
            $table->foreign('id_cells_project')->references('id_cells_project')->on('cells_projects')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_finlog');
    }
};
