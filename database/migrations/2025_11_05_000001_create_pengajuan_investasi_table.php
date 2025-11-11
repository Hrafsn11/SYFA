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
        Schema::create('pengajuan_investasi', function (Blueprint $table) {
            $table->ulid('id_pengajuan_investasi')->primary();
            
            // Foreign Keys
            $table->ulid('id_debitur_dan_investor');
            
            // Main fields
            $table->string('nama_investor');
            $table->enum('deposito', ['Reguler', 'Khusus'])->default('Reguler');
            $table->date('tanggal_investasi');
            $table->integer('lama_investasi'); // in months
            $table->decimal('jumlah_investasi', 15, 2);
            $table->integer('bagi_hasil_pertahun'); // percentage
            $table->decimal('nominal_bagi_hasil_yang_didapatkan', 15, 2)->nullable();
            
            // Upload
            $table->string('upload_bukti_transfer')->nullable();
            
            // Status & tracking
            $table->string('status')->default('Draft'); // Draft, Submitted, Approved, Rejected, etc.
            $table->integer('current_step')->default(1);
            
            // Audit fields
            $table->ulid('created_by')->nullable();
            $table->ulid('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('id_debitur_dan_investor', 'fk_pengajuan_investasi_debitur')
                  ->references('id_debitur')
                  ->on('master_debitur_dan_investor')
                  ->onDelete('cascade');
                  
            $table->foreign('created_by', 'fk_pengajuan_investasi_created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('updated_by', 'fk_pengajuan_investasi_updated_by')
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
        Schema::dropIfExists('pengajuan_investasi');
    }
};
