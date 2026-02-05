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
            $table->integer('jumlah_bulan_keterlambatan')->default(0)->after('sisa_bagi_hasil');
            $table->decimal('denda_keterlambatan', 20, 2)->default(0)->after('jumlah_bulan_keterlambatan');
            $table->decimal('total_bagi_hasil_saat_ini', 20, 2)->nullable()->after('denda_keterlambatan');
            $table->timestamp('last_penalty_calculation')->nullable()->after('total_bagi_hasil_saat_ini');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_bulan_keterlambatan',
                'denda_keterlambatan',
                'total_bagi_hasil_saat_ini',
                'last_penalty_calculation',
            ]);
        });
    }
};
