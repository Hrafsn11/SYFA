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
        Schema::table('pengembalian_pinjaman', function (Blueprint $table) {
            $table->ulid('id_pengajuan_peminjaman')->after('id');
            $table->foreign('id_pengajuan_peminjaman')
                  ->references('id_pengajuan_peminjaman')
                  ->on('pengajuan_peminjaman')
                  ->onDelete('cascade');
            $table->string('nama_perusahaan', 255)->after('id_pengajuan_peminjaman');
            $table->string('nomor_peminjaman', 255)->after('nama_perusahaan');
            $table->decimal('total_pinjaman', 15, 2)->after('nomor_peminjaman');
            $table->decimal('total_bagi_hasil', 15, 2)->after('total_pinjaman');
            $table->date('tanggal_pencairan')->after('total_bagi_hasil');
            $table->integer('lama_pemakaian')->after('tanggal_pencairan');
            $table->enum('invoice_dibayarkan', ['iya', 'tidak'])->after('lama_pemakaian');
            $table->decimal('nominal_invoice', 15, 2)->after('invoice_dibayarkan');
            $table->decimal('sisa_bayar_pokok', 15, 2)->after('nominal_invoice');
            $table->decimal('sisa_bagi_hasil', 15, 2)->after('sisa_bayar_pokok');
            $table->text('catatan')->nullable()->after('sisa_bagi_hasil');
            $table->string('status')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian_pinjaman', function (Blueprint $table) {
            $table->dropForeign(['id_pengajuan_peminjaman']);
            $table->dropColumn([
                'id_pengajuan_peminjaman',
                'nama_perusahaan',
                'nomor_peminjaman',
                'total_pinjaman',
                'total_bagi_hasil',
                'tanggal_pencairan',
                'lama_pemakaian',
                'invoice_dibayarkan',
                'nominal_invoice',
                'sisa_bayar_pokok',
                'sisa_bagi_hasil',
                'catatan',
                'status'
            ]);
        });
    }
};
