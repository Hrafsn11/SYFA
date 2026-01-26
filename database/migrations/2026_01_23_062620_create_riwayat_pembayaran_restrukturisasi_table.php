<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_pembayaran_restrukturisasi', function (Blueprint $table) {
            $table->ulid('id_riwayat_pembayaran')->primary();

            // Foreign key ke jadwal angsuran
            $table->ulid('id_jadwal_angsuran');
            $table->foreign('id_jadwal_angsuran')
                ->references('id_jadwal_angsuran')
                ->on('jadwal_angsuran')
                ->onDelete('cascade');

            // Data pembayaran
            $table->decimal('nominal_bayar', 15, 2)->default(0);
            $table->string('bukti_pembayaran')->nullable();
            $table->date('tanggal_bayar')->nullable();

            // Status pembayaran: Tertunda (menunggu konfirmasi), Dikonfirmasi, Ditolak
            $table->enum('status', ['Tertunda', 'Dikonfirmasi', 'Ditolak'])->default('Tertunda');
            $table->text('catatan')->nullable();

            // Data konfirmasi oleh SKI
            $table->ulid('dikonfirmasi_oleh')->nullable();
            $table->foreign('dikonfirmasi_oleh')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->timestamp('dikonfirmasi_at')->nullable();

            $table->timestamps();
        });

        // Tambah kolom total_terbayar di jadwal_angsuran untuk menyimpan akumulasi pembayaran yang sudah dikonfirmasi
        Schema::table('jadwal_angsuran', function (Blueprint $table) {
            $table->decimal('total_terbayar', 15, 2)->default(0)->after('nominal_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_angsuran', function (Blueprint $table) {
            $table->dropColumn('total_terbayar');
        });

        Schema::dropIfExists('riwayat_pembayaran_restrukturisasi');
    }
};
