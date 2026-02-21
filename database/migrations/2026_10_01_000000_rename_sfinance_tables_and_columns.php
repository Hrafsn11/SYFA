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
        // 1. Rename Tables
        if (Schema::hasTable('ar_perbulan')) {
            Schema::rename('ar_perbulan', 'laporan_tagihan_bulanan');
        }
        if (Schema::hasTable('pengajuan_restrukturisasi')) {
            Schema::rename('pengajuan_restrukturisasi', 'pengajuan_cicilan');
        }
        if (Schema::hasTable('program_restrukturisasi')) {
            Schema::rename('program_restrukturisasi', 'penyesuaian_cicilan');
        }
        if (Schema::hasTable('report_pengembalian')) {
            Schema::rename('report_pengembalian', 'laporan_pengembalian');
        }
        if (Schema::hasTable('penyaluran_deposito')) {
            Schema::rename('penyaluran_deposito', 'jenis_investasi');
        }
        // Assuming 'peminjaman' table exists and represents 'piutang' context
        if (Schema::hasTable('peminjamans')) { // Checked list_files, Peminjaman model uses 'peminjamans'
            Schema::rename('peminjamans', 'tagihan_pinjaman');
        } elseif (Schema::hasTable('peminjaman')) {
            Schema::rename('peminjaman', 'tagihan_pinjaman');
        }

        // Related tables
        if (Schema::hasTable('pengajuan_peminjaman')) {
            Schema::rename('pengajuan_peminjaman', 'pengajuan_tagihan_pinjaman');
        }
        if (Schema::hasTable('pengembalian_pinjaman')) {
            Schema::rename('pengembalian_pinjaman', 'pengembalian_tagihan_pinjaman');
        }

        // 2. Rename Columns
        $tablesWithBagiHasil = [
            'laporan_tagihan_bulanan',
            'tagihan_pinjaman',
            'pengajuan_tagihan_pinjaman',
            'pengembalian_tagihan_pinjaman',
            'pengajuan_investasi',
            'pengembalian_investasi'
        ];

        foreach ($tablesWithBagiHasil as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    // Common columns
                    if (Schema::hasColumn($table->getTable(), 'bagi_hasil')) {
                        $table->renameColumn('bagi_hasil', 'bunga');
                    }
                    if (Schema::hasColumn($table->getTable(), 'total_bagi_hasil')) {
                        $table->renameColumn('total_bagi_hasil', 'total_bunga');
                    }
                    if (Schema::hasColumn($table->getTable(), 'nilai_bagi_hasil')) {
                        $table->renameColumn('nilai_bagi_hasil', 'nilai_bunga');
                    }
                    if (Schema::hasColumn($table->getTable(), 'persentase_bagi_hasil')) {
                        $table->renameColumn('persentase_bagi_hasil', 'persentase_bunga');
                    }
                    if (Schema::hasColumn($table->getTable(), 'sisa_bagi_hasil')) {
                        $table->renameColumn('sisa_bagi_hasil', 'sisa_bunga');
                    }

                    // Specific columns
                    if (Schema::hasColumn($table->getTable(), 'bagi_hasil_pertahun')) {
                        $table->renameColumn('bagi_hasil_pertahun', 'bunga_pertahun');
                    }
                    if (Schema::hasColumn($table->getTable(), 'nominal_bagi_hasil_yang_didapatkan')) {
                        $table->renameColumn('nominal_bagi_hasil_yang_didapatkan', 'nominal_bunga_yang_didapatkan');
                    }
                    if (Schema::hasColumn($table->getTable(), 'bagi_hasil_dibayar')) {
                        $table->renameColumn('bagi_hasil_dibayar', 'bunga_dibayar');
                    }
                    if (Schema::hasColumn($table->getTable(), 'total_bagi_hasil_saat_ini')) {
                         $table->renameColumn('total_bagi_hasil_saat_ini', 'total_bunga_saat_ini');
                    }
                });
            }
        }

        $tablesWithTotalOutstanding = [
            'laporan_tagihan_bulanan',
            'tagihan_pinjaman'
        ];

        foreach ($tablesWithTotalOutstanding as $table) {
             if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'total_outstanding')) {
                        $table->renameColumn('total_outstanding', 'total_yang_belum_dibayarkan');
                    }
                });
             }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse Column Renames
        $tablesWithBagiHasil = [
            'laporan_tagihan_bulanan',
            'tagihan_pinjaman',
            'pengajuan_tagihan_pinjaman',
            'pengembalian_tagihan_pinjaman',
            'pengajuan_investasi',
            'pengembalian_investasi'
        ];

        foreach ($tablesWithBagiHasil as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'bunga')) {
                        $table->renameColumn('bunga', 'bagi_hasil');
                    }
                    if (Schema::hasColumn($table->getTable(), 'total_bunga')) {
                        $table->renameColumn('total_bunga', 'total_bagi_hasil');
                    }
                    if (Schema::hasColumn($table->getTable(), 'nilai_bunga')) {
                        $table->renameColumn('nilai_bunga', 'nilai_bagi_hasil');
                    }
                    if (Schema::hasColumn($table->getTable(), 'persentase_bunga')) {
                        $table->renameColumn('persentase_bunga', 'persentase_bagi_hasil');
                    }
                    if (Schema::hasColumn($table->getTable(), 'sisa_bunga')) {
                        $table->renameColumn('sisa_bunga', 'sisa_bagi_hasil');
                    }
                    if (Schema::hasColumn($table->getTable(), 'bunga_pertahun')) {
                        $table->renameColumn('bunga_pertahun', 'bagi_hasil_pertahun');
                    }
                    if (Schema::hasColumn($table->getTable(), 'nominal_bunga_yang_didapatkan')) {
                        $table->renameColumn('nominal_bunga_yang_didapatkan', 'nominal_bagi_hasil_yang_didapatkan');
                    }
                    if (Schema::hasColumn($table->getTable(), 'bunga_dibayar')) {
                        $table->renameColumn('bunga_dibayar', 'bagi_hasil_dibayar');
                    }
                    if (Schema::hasColumn($table->getTable(), 'total_bunga_saat_ini')) {
                        $table->renameColumn('total_bunga_saat_ini', 'total_bagi_hasil_saat_ini');
                    }
                });
            }
        }

        $tablesWithTotalOutstanding = [
            'laporan_tagihan_bulanan',
            'tagihan_pinjaman'
        ];

        foreach ($tablesWithTotalOutstanding as $table) {
             if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'total_yang_belum_dibayarkan')) {
                        $table->renameColumn('total_yang_belum_dibayarkan', 'total_outstanding');
                    }
                });
             }
        }

        // Reverse Table Renames
        if (Schema::hasTable('laporan_tagihan_bulanan')) {
            Schema::rename('laporan_tagihan_bulanan', 'ar_perbulan');
        }
        if (Schema::hasTable('pengajuan_cicilan')) {
            Schema::rename('pengajuan_cicilan', 'pengajuan_restrukturisasi');
        }
        if (Schema::hasTable('penyesuaian_cicilan')) {
            Schema::rename('penyesuaian_cicilan', 'program_restrukturisasi');
        }
        if (Schema::hasTable('laporan_pengembalian')) {
            Schema::rename('laporan_pengembalian', 'report_pengembalian');
        }
        if (Schema::hasTable('jenis_investasi')) {
            Schema::rename('jenis_investasi', 'penyaluran_deposito');
        }
        if (Schema::hasTable('tagihan_pinjaman')) {
            Schema::rename('tagihan_pinjaman', 'peminjamans');
        }
        if (Schema::hasTable('pengajuan_tagihan_pinjaman')) {
            Schema::rename('pengajuan_tagihan_pinjaman', 'pengajuan_peminjaman');
        }
        if (Schema::hasTable('pengembalian_tagihan_pinjaman')) {
            Schema::rename('pengembalian_tagihan_pinjaman', 'pengembalian_pinjaman');
        }
    }
};
