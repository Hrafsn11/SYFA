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
        Schema::create('history_status_pengajuan_restrukturisasi', function (Blueprint $table) {
            $table->ulid('id_history_status_restrukturisasi')->primary();
            
            // Foreign key ke pengajuan restrukturisasi
            $table->ulid('id_pengajuan_restrukturisasi');
            $table->foreign('id_pengajuan_restrukturisasi', 'fk_history_to_peng')
                ->references('id_pengajuan_restrukturisasi')
                ->on('pengajuan_restrukturisasi')
                ->onDelete('cascade');
            
            // Status information
            $table->string('status'); // 'Draft', 'Submit Dokumen', 'Dokumen Tervalidasi', 'Disetujui CEO', 'Ditolak CEO', 'Disetujui Direktur', 'Ditolak Direktur', 'Selesai'
            $table->integer('current_step')->default(1); // 1-5
            $table->date('date');
            $table->time('time');
            
            // User tracking - who performed the action
            $table->ulid('submit_by')->nullable();
            $table->foreign('submit_by', 'fk_history_submit_by')->references('id')->on('users')->onDelete('set null');
            
            $table->ulid('approve_by')->nullable();
            $table->foreign('approve_by', 'fk_history_approve_by')->references('id')->on('users')->onDelete('set null');
            
            $table->ulid('reject_by')->nullable();
            $table->foreign('reject_by', 'fk_history_reject_by')->references('id')->on('users')->onDelete('set null');
            
            // Validation result
            $table->enum('validasi_dokumen', ['disetujui', 'ditolak'])->nullable();
            $table->text('catatan_validasi_dokumen')->nullable(); 
            
            // Notes/comments
            $table->text('catatan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_status_pengajuan_restrukturisasi');
    }
};
