<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_investasi', function (Blueprint $table) {
            $table->decimal('sisa_pokok', 20, 2)->default(0)->after('jumlah_investasi');
            $table->decimal('sisa_bagi_hasil', 20, 2)->default(0)->after('nominal_bagi_hasil_yang_didapatkan');
            
            $table->index('sisa_pokok');
            $table->index('sisa_bagi_hasil');
        });

        DB::statement('
            UPDATE pengajuan_investasi 
            SET sisa_pokok = jumlah_investasi,
                sisa_bagi_hasil = nominal_bagi_hasil_yang_didapatkan
            WHERE sisa_pokok = 0
        ');
    }

    public function down(): void
    {
        Schema::table('pengajuan_investasi', function (Blueprint $table) {
            $table->dropIndex(['sisa_pokok']);
            $table->dropIndex(['sisa_bagi_hasil']);
            $table->dropColumn(['sisa_pokok', 'sisa_bagi_hasil']);
        });
    }
};
