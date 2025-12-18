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
        Schema::create('pengembalian_pinjaman_finlog', function (Blueprint $table) {
            $table->ulid('id_pengembalian_pinjaman_finlog')->primary();
            $table->ulid('id_pinjaman_finlog');
            $table->ulid('id_cells_project');
            $table->ulid('id_project');
            $table->decimal('jumlah_pengembalian', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->decimal('sisa_bagi_hasil', 15, 2);
            $table->decimal('total_sisa_pinjaman', 15, 2);
            $table->date('tanggal_pengembalian');
            $table->string('bukti_pembayaran')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['Lunas', 'Belum Lunas', 'Terlambat'])->default('Belum Lunas');
            $table->timestamps();

            $table->foreign('id_pinjaman_finlog', 'fk_pengembalian_to_pinjaman_finlog')
                ->references('id_peminjaman_finlog')
                ->on('peminjaman_finlog')
                ->onDelete('cascade');

            $table->foreign('id_cells_project', 'fk_pengembalian_to_cells_project')
                ->references('id_cells_project')
                ->on('cells_projects')
                ->onDelete('cascade');

            $table->foreign('id_project', 'fk_pengembalian_to_project')
                ->references('id_project')
                ->on('projects')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_pinjaman_finlog');
    }
};
