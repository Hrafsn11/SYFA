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
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->string('nomor_kontrak_restrukturisasi')->nullable()->after('id_pengajuan_restrukturisasi');

            $table->text('jaminan')->nullable()->after('total_terbayar');

            $table->timestamp('kontrak_generated_at')->nullable()->after('jaminan');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_kontrak_restrukturisasi',
                'jaminan',
                'kontrak_generated_at',
            ]);
        });
    }
};
