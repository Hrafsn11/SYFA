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
        // 1. Rename Table: penyaluran_deposito -> penyaluran_dana_investasi
        if (Schema::hasTable('penyaluran_deposito')) {
            Schema::rename('penyaluran_deposito', 'penyaluran_dana_investasi');
        }

        // 2. Rename Table: riwayat_pengembalian_deposito -> riwayat_pengembalian_dana_investasi
        if (Schema::hasTable('riwayat_pengembalian_deposito')) {
            Schema::rename('riwayat_pengembalian_deposito', 'riwayat_pengembalian_dana_investasi');
        }

        // 3. Rename Column: id_penyaluran_deposito -> id_penyaluran_dana_investasi (Primary Key)
        if (Schema::hasTable('penyaluran_dana_investasi') && Schema::hasColumn('penyaluran_dana_investasi', 'id_penyaluran_deposito')) {
            Schema::table('penyaluran_dana_investasi', function (Blueprint ) {
                ->renameColumn('id_penyaluran_deposito', 'id_penyaluran_dana_investasi');
            });
        }

        // 4. Rename Column: id_penyaluran_deposito -> id_penyaluran_dana_investasi (Foreign Key)
        if (Schema::hasTable('riwayat_pengembalian_dana_investasi') && Schema::hasColumn('riwayat_pengembalian_dana_investasi', 'id_penyaluran_deposito')) {
            Schema::table('riwayat_pengembalian_dana_investasi', function (Blueprint ) {
                ->renameColumn('id_penyaluran_deposito', 'id_penyaluran_dana_investasi');
            });
        }

        // 5. Rename Column: deposito -> jenis_investasi (in pengajuan_investasi)
        if (Schema::hasTable('pengajuan_investasi') && Schema::hasColumn('pengajuan_investasi', 'deposito')) {
            Schema::table('pengajuan_investasi', function (Blueprint ) {
                ->renameColumn('deposito', 'jenis_investasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert Column: jenis_investasi -> deposito
        if (Schema::hasTable('pengajuan_investasi') && Schema::hasColumn('pengajuan_investasi', 'jenis_investasi')) {
            Schema::table('pengajuan_investasi', function (Blueprint ) {
                ->renameColumn('jenis_investasi', 'deposito');
            });
        }

        // 2. Revert Column: id_penyaluran_dana_investasi -> id_penyaluran_deposito (Foreign Key)
        if (Schema::hasTable('riwayat_pengembalian_dana_investasi') && Schema::hasColumn('riwayat_pengembalian_dana_investasi', 'id_penyaluran_dana_investasi')) {
            Schema::table('riwayat_pengembalian_dana_investasi', function (Blueprint ) {
                ->renameColumn('id_penyaluran_dana_investasi', 'id_penyaluran_deposito');
            });
        }

        // 3. Revert Column: id_penyaluran_dana_investasi -> id_penyaluran_deposito (Primary Key)
        if (Schema::hasTable('penyaluran_dana_investasi') && Schema::hasColumn('penyaluran_dana_investasi', 'id_penyaluran_dana_investasi')) {
            Schema::table('penyaluran_dana_investasi', function (Blueprint ) {
                ->renameColumn('id_penyaluran_dana_investasi', 'id_penyaluran_deposito');
            });
        }

        // 4. Revert Table: riwayat_pengembalian_dana_investasi -> riwayat_pengembalian_deposito
        if (Schema::hasTable('riwayat_pengembalian_dana_investasi')) {
            Schema::rename('riwayat_pengembalian_dana_investasi', 'riwayat_pengembalian_deposito');
        }

        // 5. Revert Table: penyaluran_dana_investasi -> penyaluran_deposito
        if (Schema::hasTable('penyaluran_dana_investasi')) {
            Schema::rename('penyaluran_dana_investasi', 'penyaluran_deposito');
        }
    }
};
