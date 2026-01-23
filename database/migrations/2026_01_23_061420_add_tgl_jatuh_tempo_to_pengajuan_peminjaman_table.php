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
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->date('tanggal_jatuh_tempo')->nullable()->after('harapan_tanggal_pencairan')->comment('Tanggal jatuh tempo pembayaran');
            $table->decimal('sisa_bayar_pokok', 15, 2)->nullable()->after('total_pinjaman')->comment('Sisa pinjaman pokok yang belum dibayar');
            $table->decimal('sisa_bagi_hasil', 15, 2)->nullable()->after('total_bagi_hasil')->comment('Sisa bagi hasil yang belum dibayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->dropColumn(['tanggal_jatuh_tempo', 'sisa_bayar_pokok', 'sisa_bagi_hasil']);
        });
    }
};
