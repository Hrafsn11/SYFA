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
        Schema::table('peminjaman_finlog', function (Blueprint $table) {
            $table->integer('jumlah_minggu_keterlambatan')->default(0)->after('status');

            $table->decimal('denda_keterlambatan', 15, 2)->default(0)->after('jumlah_minggu_keterlambatan');

            $table->decimal('nilai_bagi_hasil_saat_ini', 15, 2)->nullable()->after('denda_keterlambatan');

            $table->timestamp('last_penalty_calculation')->nullable()->after('nilai_bagi_hasil_saat_ini');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_finlog', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_minggu_keterlambatan',
                'denda_keterlambatan',
                'nilai_bagi_hasil_saat_ini',
                'last_penalty_calculation'
            ]);
        });
    }
};
