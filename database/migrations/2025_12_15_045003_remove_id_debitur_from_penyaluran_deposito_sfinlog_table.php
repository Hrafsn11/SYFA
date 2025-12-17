<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah kolom id_debitur ada sebelum menghapus
        if (Schema::hasColumn('penyaluran_deposito_sfinlog', 'id_debitur')) {
            // Hapus foreign key menggunakan raw SQL (jika ada)
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'penyaluran_deposito_sfinlog' 
                AND COLUMN_NAME = 'id_debitur' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $foreignKey) {
                DB::statement("ALTER TABLE `penyaluran_deposito_sfinlog` DROP FOREIGN KEY `{$foreignKey->CONSTRAINT_NAME}`");
            }
            
            // Hapus kolom id_debitur
            Schema::table('penyaluran_deposito_sfinlog', function (Blueprint $table) {
                $table->dropColumn('id_debitur');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyaluran_deposito_sfinlog', function (Blueprint $table) {
            // Tambahkan kembali kolom id_debitur
            $table->foreignUlid('id_debitur')->nullable()->after('id_pengajuan_investasi_finlog');
            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onDelete('cascade');
        });
    }
};
