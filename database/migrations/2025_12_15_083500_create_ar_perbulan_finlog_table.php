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
        Schema::create('ar_perbulan_finlog', function (Blueprint $table) {
            $table->ulid('id_ar_perbulan_finlog')->primary();
            
            $table->foreignUlid('id_debitur')
                ->constrained('master_debitur_dan_investor', 'id_debitur')
                ->onDelete('cascade');
            
            $table->string('nama_perusahaan')->nullable();
            $table->date('periode');
            $table->string('bulan', 7);
            
            $table->decimal('total_pinjaman_pokok', 15, 2)->default(0);
            $table->decimal('total_bagi_hasil', 15, 2)->default(0);
            $table->decimal('total_pengembalian_pokok', 15, 2)->default(0);
            $table->decimal('total_pengembalian_bagi_hasil', 15, 2)->default(0);
            
            $table->decimal('sisa_ar_pokok', 15, 2)->default(0);
            $table->decimal('sisa_bagi_hasil', 15, 2)->default(0);
            $table->decimal('sisa_ar_total', 15, 2)->default(0);
            
            $table->integer('jumlah_pinjaman')->default(0);
            $table->enum('status', ['active', 'lunas', 'archived'])->default('active');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['id_debitur', 'bulan'], 'idx_debitur_bulan');
            $table->unique(['id_debitur', 'bulan'], 'unique_debitur_bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ar_perbulan_finlog');
    }
};
