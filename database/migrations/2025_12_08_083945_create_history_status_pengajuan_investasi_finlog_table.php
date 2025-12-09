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
        Schema::create('history_status_pengajuan_investasi_finlog', function (Blueprint $table) {
            $table->ulid('id_history_status_pengajuan_investasi_finlog')->primary();
            
            // Foreign Keys
            $table->ulid('id_pengajuan_investasi_finlog');
            
            // Fields
            $table->string('submit_step1_by')->nullable();
            $table->date('date')->nullable();
            $table->enum('validasi_pengajuan', ['ditolak', 'disetujui'])->nullable();
            $table->enum('persetujuan_ceo_finlog', ['ditolak', 'disetujui'])->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->string('status')->nullable();
            $table->string('reject_by')->nullable();
            $table->string('approve_by')->nullable();
            $table->time('time')->nullable();
            $table->integer('current_step')->default(1);
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('id_pengajuan_investasi_finlog', 'fk_history_pi_finlog')
                  ->references('id_pengajuan_investasi_finlog')
                  ->on('pengajuan_investasi_finlog')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_status_pengajuan_investasi_finlog');
    }
};
