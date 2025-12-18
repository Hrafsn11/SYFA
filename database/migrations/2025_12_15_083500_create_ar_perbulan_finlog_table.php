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
            $table->ulid('id_debitur');
            $table->string('nama_perusahaan');
            $table->date('periode');
            $table->string('bulan', 10); // Format: 'Y-m' (2025-12)
            $table->decimal('total_pinjaman_pokok', 20, 2)->default(0);
            $table->decimal('total_bagi_hasil', 20, 2)->default(0);
            $table->decimal('total_pengembalian_pokok', 20, 2)->default(0);
            $table->decimal('total_pengembalian_bagi_hasil', 20, 2)->default(0);
            $table->decimal('sisa_ar_pokok', 20, 2)->default(0);
            $table->decimal('sisa_bagi_hasil', 20, 2)->default(0);
            $table->decimal('sisa_ar_total', 20, 2)->default(0);
            $table->integer('jumlah_pinjaman')->default(0);
            $table->enum('status', ['active', 'lunas', 'archived'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['id_debitur', 'bulan']);
            $table->index('bulan');
            $table->index('status');

            // Foreign key
            $table->foreign('id_debitur')
                ->references('id_debitur')
                ->on('master_debitur_dan_investor')
                ->onDelete('cascade');
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
