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
        Schema::create('pengajuan_restrukturisasi', function (Blueprint $table) {
            $table->ulid('id_pengajuan_restrukturisasi')->primary();
            
            $table->ulid('id_debitur');
            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onDelete('cascade');
            
            $table->ulid('id_pengajuan_peminjaman');
            $table->foreign('id_pengajuan_peminjaman')->references('id_pengajuan_peminjaman')->on('pengajuan_peminjaman')->onDelete('cascade');
            
            $table->string('nama_perusahaan');
            $table->string('npwp')->nullable();
            $table->text('alamat_kantor')->nullable();
            $table->string('nomor_telepon')->nullable();
            
            $table->string('nama_pic');
            $table->string('jabatan_pic');
            
            $table->string('nomor_kontrak_pembiayaan');
            $table->date('tanggal_akad'); 
            $table->string('jenis_pembiayaan'); 
            $table->decimal('jumlah_plafon_awal', 15, 2)->nullable();
            $table->decimal('sisa_pokok_belum_dibayar', 15, 2)->default(0); 
            
            $table->decimal('tunggakan_margin_bunga', 15, 2)->nullable();
            $table->date('jatuh_tempo_terakhir')->nullable();
            $table->string('status_dpd')->nullable(); 
            $table->text('alasan_restrukturisasi');
            
            $table->json('jenis_restrukturisasi')->nullable();
            $table->string('jenis_restrukturisasi_lainnya')->nullable(); 
            $table->text('rencana_pemulihan_usaha'); 
            
            $table->string('dokumen_ktp_pic')->nullable(); 
            $table->string('dokumen_npwp_perusahaan')->nullable();
            $table->string('dokumen_laporan_keuangan')->nullable(); 
            $table->string('dokumen_arus_kas')->nullable(); 
            $table->string('dokumen_kondisi_eksternal')->nullable(); 
            $table->string('dokumen_kontrak_pembiayaan')->nullable(); 
            $table->string('dokumen_lainnya')->nullable(); 
            $table->string('dokumen_tanda_tangan')->nullable(); 
            $table->string('tempat')->nullable();
            $table->date('tanggal')->nullable();
            
            $table->string('status')->default('Draft'); 
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_restrukturisasi');
    }
};
