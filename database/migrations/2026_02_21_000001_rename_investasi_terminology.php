<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // 1. Rename columns in pengajuan_investasi
        // deposito -> jenis_investasi
        // bagi_hasil_pertahun -> bunga_pertahun
        // nominal_bagi_hasil_yang_didapatkan -> nominal_bunga_yang_didapatkan
        // sisa_bagi_hasil -> sisa_bunga
        // ============================================================
        Schema::table('pengajuan_investasi', function (Blueprint $table) {
            $table->renameColumn('deposito', 'jenis_investasi');
            $table->renameColumn('bagi_hasil_pertahun', 'bunga_pertahun');
            $table->renameColumn('nominal_bagi_hasil_yang_didapatkan', 'nominal_bunga_yang_didapatkan');
            $table->renameColumn('sisa_bagi_hasil', 'sisa_bunga');
        });

        // ============================================================
        // 2. Rename column in pengembalian_investasi
        // bagi_hasil_dibayar -> bunga_dibayar
        // ============================================================
        Schema::table('pengembalian_investasi', function (Blueprint $table) {
            $table->renameColumn('bagi_hasil_dibayar', 'bunga_dibayar');
        });

        // ============================================================
        // 3. Rename penyaluran_deposito -> penyaluran_dana_investasi
        //    PK: id_penyaluran_deposito -> id_penyaluran_dana_investasi
        //    + update riwayat_pengembalian_deposito
        // ============================================================

        // 3a. Drop FK from riwayat_pengembalian_deposito
        try {
            Schema::table('riwayat_pengembalian_deposito', function (Blueprint $table) {
                $table->dropForeign('riwayat_pengembalian_deposito_id_penyaluran_deposito_foreign');
            });
        } catch (\Exception $e) {
            // FK might have a different name or not exist, try raw
            try {
                DB::statement('ALTER TABLE riwayat_pengembalian_deposito DROP FOREIGN KEY riwayat_pengembalian_deposito_id_penyaluran_deposito_foreign');
            } catch (\Exception $e2) {
                // Already dropped or different name - continue
            }
        }

        // 3b. Rename PK column in penyaluran_deposito
        Schema::table('penyaluran_deposito', function (Blueprint $table) {
            $table->renameColumn('id_penyaluran_deposito', 'id_penyaluran_dana_investasi');
        });

        // 3c. Rename the table
        Schema::rename('penyaluran_deposito', 'penyaluran_dana_investasi');

        // 3d. Rename FK column in riwayat_pengembalian_deposito
        Schema::table('riwayat_pengembalian_deposito', function (Blueprint $table) {
            $table->renameColumn('id_penyaluran_deposito', 'id_penyaluran_dana_investasi');
        });

        // 3e. Re-add FK with new names
        Schema::table('riwayat_pengembalian_deposito', function (Blueprint $table) {
            $table->foreign('id_penyaluran_dana_investasi', 'riwayat_pengembalian_dana_investasi_fk')
                ->references('id_penyaluran_dana_investasi')
                ->on('penyaluran_dana_investasi')
                ->onDelete('cascade');
        });

        // 3f. Rename riwayat table
        Schema::rename('riwayat_pengembalian_deposito', 'riwayat_pengembalian_dana_investasi');

        // ============================================================
        // 4. Rename penyaluran_deposito_sfinlog -> penyaluran_dana_investasi_sfinlog
        //    PK: id_penyaluran_deposito_sfinlog -> id_penyaluran_dana_investasi_sfinlog
        //    + update riwayat_pengembalian_deposito_sfinlog
        // ============================================================

        // 4a. Drop FK from riwayat_pengembalian_deposito_sfinlog
        try {
            Schema::table('riwayat_pengembalian_deposito_sfinlog', function (Blueprint $table) {
                $table->dropForeign('fk_riwayat_penyaluran_sfinlog');
            });
        } catch (\Exception $e) {
            try {
                DB::statement('ALTER TABLE riwayat_pengembalian_deposito_sfinlog DROP FOREIGN KEY fk_riwayat_penyaluran_sfinlog');
            } catch (\Exception $e2) {
                // Continue
            }
        }

        // 4b. Rename PK column in penyaluran_deposito_sfinlog
        Schema::table('penyaluran_deposito_sfinlog', function (Blueprint $table) {
            $table->renameColumn('id_penyaluran_deposito_sfinlog', 'id_penyaluran_dana_investasi_sfinlog');
        });

        // 4c. Rename the table
        Schema::rename('penyaluran_deposito_sfinlog', 'penyaluran_dana_investasi_sfinlog');

        // 4d. Rename FK column in riwayat_pengembalian_deposito_sfinlog
        Schema::table('riwayat_pengembalian_deposito_sfinlog', function (Blueprint $table) {
            $table->renameColumn('id_penyaluran_deposito_sfinlog', 'id_penyaluran_dana_investasi_sfinlog');
        });

        // 4e. Re-add FK with new names
        Schema::table('riwayat_pengembalian_deposito_sfinlog', function (Blueprint $table) {
            $table->foreign('id_penyaluran_dana_investasi_sfinlog', 'fk_riwayat_penyaluran_dana_investasi_sfinlog')
                ->references('id_penyaluran_dana_investasi_sfinlog')
                ->on('penyaluran_dana_investasi_sfinlog')
                ->onDelete('cascade');
        });

        // 4f. Rename riwayat table
        Schema::rename('riwayat_pengembalian_deposito_sfinlog', 'riwayat_pengembalian_dana_investasi_sfinlog');

        // ============================================================
        // 5. Rename column in riwayat_pengembalian_dana_investasi_sfinlog
        //    id_riwayat_pengembalian_deposito_sfinlog -> id_riwayat_pengembalian_dana_investasi_sfinlog
        // ============================================================
        Schema::table('riwayat_pengembalian_dana_investasi_sfinlog', function (Blueprint $table) {
            $table->renameColumn('id_riwayat_pengembalian_deposito_sfinlog', 'id_riwayat_pengembalian_dana_investasi_sfinlog');
        });
    }

    public function down(): void
    {
        // Reverse: riwayat_pengembalian_dana_investasi_sfinlog
        Schema::table('riwayat_pengembalian_dana_investasi_sfinlog', function (Blueprint $table) {
            $table->renameColumn('id_riwayat_pengembalian_dana_investasi_sfinlog', 'id_riwayat_pengembalian_deposito_sfinlog');
        });

        // Reverse: penyaluran_dana_investasi_sfinlog
        try {
            Schema::table('riwayat_pengembalian_dana_investasi_sfinlog', function (Blueprint $table) {
                $table->dropForeign('fk_riwayat_penyaluran_dana_investasi_sfinlog');
            });
        } catch (\Exception $e) {}

        Schema::table('riwayat_pengembalian_dana_investasi_sfinlog', function (Blueprint $table) {
            $table->renameColumn('id_penyaluran_dana_investasi_sfinlog', 'id_penyaluran_deposito_sfinlog');
        });

        Schema::rename('riwayat_pengembalian_dana_investasi_sfinlog', 'riwayat_pengembalian_deposito_sfinlog');

        Schema::table('penyaluran_dana_investasi_sfinlog', function (Blueprint $table) {
            $table->renameColumn('id_penyaluran_dana_investasi_sfinlog', 'id_penyaluran_deposito_sfinlog');
        });

        Schema::rename('penyaluran_dana_investasi_sfinlog', 'penyaluran_deposito_sfinlog');

        Schema::table('riwayat_pengembalian_deposito_sfinlog', function (Blueprint $table) {
            $table->foreign('id_penyaluran_deposito_sfinlog', 'fk_riwayat_penyaluran_sfinlog')
                ->references('id_penyaluran_deposito_sfinlog')
                ->on('penyaluran_deposito_sfinlog')
                ->onDelete('cascade');
        });

        // Reverse: penyaluran_dana_investasi
        try {
            Schema::table('riwayat_pengembalian_dana_investasi', function (Blueprint $table) {
                $table->dropForeign('riwayat_pengembalian_dana_investasi_fk');
            });
        } catch (\Exception $e) {}

        Schema::table('riwayat_pengembalian_dana_investasi', function (Blueprint $table) {
            $table->renameColumn('id_penyaluran_dana_investasi', 'id_penyaluran_deposito');
        });

        Schema::rename('riwayat_pengembalian_dana_investasi', 'riwayat_pengembalian_deposito');

        Schema::table('penyaluran_dana_investasi', function (Blueprint $table) {
            $table->renameColumn('id_penyaluran_dana_investasi', 'id_penyaluran_deposito');
        });

        Schema::rename('penyaluran_dana_investasi', 'penyaluran_deposito');

        Schema::table('riwayat_pengembalian_deposito', function (Blueprint $table) {
            $table->foreign('id_penyaluran_deposito', 'riwayat_pengembalian_deposito_id_penyaluran_deposito_foreign')
                ->references('id_penyaluran_deposito')
                ->on('penyaluran_deposito')
                ->onDelete('cascade');
        });

        // Reverse: pengembalian_investasi
        Schema::table('pengembalian_investasi', function (Blueprint $table) {
            $table->renameColumn('bunga_dibayar', 'bagi_hasil_dibayar');
        });

        // Reverse: pengajuan_investasi
        Schema::table('pengajuan_investasi', function (Blueprint $table) {
            $table->renameColumn('sisa_bunga', 'sisa_bagi_hasil');
            $table->renameColumn('nominal_bunga_yang_didapatkan', 'nominal_bagi_hasil_yang_didapatkan');
            $table->renameColumn('bunga_pertahun', 'bagi_hasil_pertahun');
            $table->renameColumn('jenis_investasi', 'deposito');
        });
    }
};
