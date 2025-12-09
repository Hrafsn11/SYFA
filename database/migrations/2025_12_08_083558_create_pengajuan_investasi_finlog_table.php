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
        Schema::create('pengajuan_investasi_finlog', function (Blueprint $table) {
            $table->ulid('id_pengajuan_investasi_finlog')->primary();
            
            // Foreign Keys
            $table->ulid('id_debitur_dan_investor');
            $table->ulid('id_cells_project');
            
            // Main fields
            $table->string('nama_investor');
            $table->date('tanggal_investasi'); // tanggal dana masuk
            $table->integer('lama_investasi'); // in months
            $table->date('tanggal_berakhir_investasi'); // auto calculated
            $table->decimal('nominal_investasi', 15, 2);
            $table->decimal('persentase_bagi_hasil', 5, 2); // 12.00 - 15.00
            $table->decimal('nominal_bagi_hasil_yang_didapat', 15, 2)->nullable();
            
            // Upload
            $table->string('upload_bukti_transfer')->nullable();
            $table->string('nomor_kontrak')->nullable();
            
            // Status & tracking
            $table->string('status')->default('Draft'); // Draft, Submitted, Validasi Fia, Validasi CEO, etc.
            $table->integer('current_step')->default(1); // 1-6
            
            // Audit fields
            $table->ulid('created_by')->nullable();
            $table->ulid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Key Constraints
            $table->foreign('id_debitur_dan_investor', 'fk_pi_finlog_debitur')
                  ->references('id_debitur')
                  ->on('master_debitur_dan_investor')
                  ->onDelete('cascade');
                  
            $table->foreign('id_cells_project', 'fk_pi_finlog_project')
                  ->references('id_cells_project')
                  ->on('cells_projects')
                  ->onDelete('cascade');
                  
            $table->foreign('created_by', 'fk_pi_finlog_created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('updated_by', 'fk_pi_finlog_updated_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_investasi_finlog');
    }
};
