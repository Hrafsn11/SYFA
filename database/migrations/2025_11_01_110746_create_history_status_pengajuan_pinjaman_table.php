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
        Schema::create('history_status_pengajuan_pinjaman', function (Blueprint $table) {
            $table->ulid('id_history_status_pengajuan_pinjaman')->primary();

            $table->ulid('id_pengajuan_peminjaman');
            $table->unsignedInteger('id_config_matrix_peminjaman')->nullable();

            // Fields
            $table->string('submit_step1_by')->nullable();
            $table->date('date')->nullable();
            $table->enum('deviasi', ['ya', 'tidak'])->nullable();
            $table->enum('validasi_dokumen', ['ditolak', 'disetujui'])->nullable();
            $table->text('catatan_validasi_dokumen_ditolak')->nullable();
            $table->decimal('nominal_yang_disetujui', 15, 2)->nullable();
            $table->text('catatan_validasi_dokumen_disetujui')->nullable();
            $table->date('tanggal_pencairan')->nullable();
            $table->string('status')->nullable();
            $table->string('reject_by')->nullable();
            $table->string('approve_by')->nullable();
            $table->integer('current_step')->default(1);
            $table->timestamps();
            
            // Foreign Key Constraints with custom names
            $table->foreign('id_pengajuan_peminjaman', 'fk_history_status_pengajuan')
                  ->references('id_pengajuan_peminjaman')
                  ->on('pengajuan_peminjaman')
                  ->onDelete('cascade');
                  
            $table->foreign('id_config_matrix_peminjaman', 'fk_history_status_matrix')
                  ->references('id_matrix_pinjaman')
                  ->on('config_matrix_pinjaman')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_status_pengajuan_pinjaman');
    }
};
