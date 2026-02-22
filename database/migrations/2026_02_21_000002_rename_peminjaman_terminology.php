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
        // BAGIAN A: RENAME KOLOM bagi_hasil → bunga
        // ============================================================

        // A1. ar_perbulan: 3 kolom
        Schema::table('ar_perbulan', function (Blueprint $table) {
            $table->renameColumn('total_bagi_hasil', 'total_bunga');
            $table->renameColumn('total_pengembalian_bagi_hasil', 'total_pengembalian_bunga');
            $table->renameColumn('sisa_bagi_hasil', 'sisa_bunga');
        });

        // A2. pengajuan_peminjaman: 4 kolom
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->renameColumn('total_bagi_hasil', 'total_bunga');
            $table->renameColumn('sisa_bagi_hasil', 'sisa_bunga');
            $table->renameColumn('total_bagi_hasil_saat_ini', 'total_bunga_saat_ini');
            $table->renameColumn('persentase_bagi_hasil', 'persentase_bunga');
        });

        // A3. pengembalian_pinjaman: 2 kolom
        Schema::table('pengembalian_pinjaman', function (Blueprint $table) {
            $table->renameColumn('total_bagi_hasil', 'total_bunga');
            $table->renameColumn('sisa_bagi_hasil', 'sisa_bunga');
        });

        // A4. bukti_peminjaman: 1 kolom
        Schema::table('bukti_peminjaman', function (Blueprint $table) {
            $table->renameColumn('nilai_bagi_hasil', 'nilai_bunga');
        });

        // A5. master_sumber_pendanaan_eksternal: 1 kolom
        Schema::table('master_sumber_pendanaan_eksternal', function (Blueprint $table) {
            $table->renameColumn('persentase_bagi_hasil', 'persentase_bunga');
        });

        // ============================================================
        // BAGIAN B: RENAME TABEL restrukturisasi → cicilan
        // Urutan: child tables first, then rename parent tables
        // ============================================================

        // B1. Drop FK dari jadwal_angsuran → program_restrukturisasi
        try {
            Schema::table('jadwal_angsuran', function (Blueprint $table) {
                $table->dropForeign(['id_program_restrukturisasi']);
            });
        } catch (\Exception $e) {
            try {
                DB::statement('ALTER TABLE jadwal_angsuran DROP FOREIGN KEY jadwal_angsuran_id_program_restrukturisasi_foreign');
            } catch (\Exception $e2) {
                // Continue
            }
        }

        // B2. Drop FK dari history_status_pengajuan_restrukturisasi → pengajuan_restrukturisasi
        try {
            Schema::table('history_status_pengajuan_restrukturisasi', function (Blueprint $table) {
                $table->dropForeign('fk_history_to_peng');
            });
        } catch (\Exception $e) {
            try {
                DB::statement('ALTER TABLE history_status_pengajuan_restrukturisasi DROP FOREIGN KEY fk_history_to_peng');
            } catch (\Exception $e2) {
                // Continue
            }
        }

        // B3. Drop FK dari evaluasi_pengajuan_restrukturisasi → pengajuan_restrukturisasi
        try {
            Schema::table('evaluasi_pengajuan_restrukturisasi', function (Blueprint $table) {
                $table->dropForeign('fk_eval_to_pengajuan');
            });
        } catch (\Exception $e) {
            try {
                DB::statement('ALTER TABLE evaluasi_pengajuan_restrukturisasi DROP FOREIGN KEY fk_eval_to_pengajuan');
            } catch (\Exception $e2) {
                // Continue
            }
        }

        // B4. Drop FK dari program_restrukturisasi → pengajuan_restrukturisasi
        try {
            Schema::table('program_restrukturisasi', function (Blueprint $table) {
                $table->dropForeign(['id_pengajuan_restrukturisasi']);
            });
        } catch (\Exception $e) {
            try {
                DB::statement('ALTER TABLE program_restrukturisasi DROP FOREIGN KEY program_restrukturisasi_id_pengajuan_restrukturisasi_foreign');
            } catch (\Exception $e2) {
                // Continue
            }
        }

        // ============================================================
        // B5. Rename: pengajuan_restrukturisasi → pengajuan_cicilan
        //     PK: id_pengajuan_restrukturisasi → id_pengajuan_cicilan
        // ============================================================
        Schema::table('pengajuan_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_pengajuan_restrukturisasi', 'id_pengajuan_cicilan');
        });
        Schema::rename('pengajuan_restrukturisasi', 'pengajuan_cicilan');

        // ============================================================
        // B6. Rename: program_restrukturisasi → penyesuaian_cicilan
        //     PK: id_program_restrukturisasi → id_penyesuaian_cicilan
        //     FK: id_pengajuan_restrukturisasi → id_pengajuan_cicilan
        // ============================================================
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_program_restrukturisasi', 'id_penyesuaian_cicilan');
            $table->renameColumn('id_pengajuan_restrukturisasi', 'id_pengajuan_cicilan');
        });
        Schema::rename('program_restrukturisasi', 'penyesuaian_cicilan');

        // Re-add FK: penyesuaian_cicilan → pengajuan_cicilan
        Schema::table('penyesuaian_cicilan', function (Blueprint $table) {
            $table->foreign('id_pengajuan_cicilan', 'fk_penyesuaian_to_pengajuan_cicilan')
                ->references('id_pengajuan_cicilan')
                ->on('pengajuan_cicilan')
                ->onDelete('cascade');
        });

        // ============================================================
        // B7. Update jadwal_angsuran FK column + re-add FK
        // ============================================================
        Schema::table('jadwal_angsuran', function (Blueprint $table) {
            $table->renameColumn('id_program_restrukturisasi', 'id_penyesuaian_cicilan');
        });
        Schema::table('jadwal_angsuran', function (Blueprint $table) {
            $table->foreign('id_penyesuaian_cicilan', 'jadwal_angsuran_id_penyesuaian_cicilan_foreign')
                ->references('id_penyesuaian_cicilan')
                ->on('penyesuaian_cicilan')
                ->onDelete('cascade');
        });

        // ============================================================
        // B8. Rename: history_status_pengajuan_restrukturisasi → history_status_pengajuan_cicilan
        //     PK: id_history_status_restrukturisasi → id_history_status_cicilan
        //     FK: id_pengajuan_restrukturisasi → id_pengajuan_cicilan
        // ============================================================
        Schema::table('history_status_pengajuan_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_history_status_restrukturisasi', 'id_history_status_cicilan');
            $table->renameColumn('id_pengajuan_restrukturisasi', 'id_pengajuan_cicilan');
        });
        Schema::rename('history_status_pengajuan_restrukturisasi', 'history_status_pengajuan_cicilan');

        // Re-add FK
        Schema::table('history_status_pengajuan_cicilan', function (Blueprint $table) {
            $table->foreign('id_pengajuan_cicilan', 'fk_history_cicilan_to_pengajuan')
                ->references('id_pengajuan_cicilan')
                ->on('pengajuan_cicilan')
                ->onDelete('cascade');
        });

        // ============================================================
        // B9. Rename: evaluasi_pengajuan_restrukturisasi → evaluasi_pengajuan_cicilan
        //     PK: id_evaluasi_restrukturisasi → id_evaluasi_cicilan
        //     FK: id_pengajuan_restrukturisasi → id_pengajuan_cicilan
        // ============================================================
        Schema::table('evaluasi_pengajuan_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_evaluasi_restrukturisasi', 'id_evaluasi_cicilan');
            $table->renameColumn('id_pengajuan_restrukturisasi', 'id_pengajuan_cicilan');
        });
        Schema::rename('evaluasi_pengajuan_restrukturisasi', 'evaluasi_pengajuan_cicilan');

        // Re-add FK
        Schema::table('evaluasi_pengajuan_cicilan', function (Blueprint $table) {
            $table->foreign('id_pengajuan_cicilan', 'fk_evaluasi_cicilan_to_pengajuan')
                ->references('id_pengajuan_cicilan')
                ->on('pengajuan_cicilan')
                ->onDelete('cascade');
        });

        // ============================================================
        // B10. evaluasi_kelengkapan_dokumen: FK rename id_evaluasi_restrukturisasi → id_evaluasi_cicilan
        // ============================================================
        // Drop FK
        try {
            Schema::table('evaluasi_kelengkapan_dokumen', function (Blueprint $table) {
                $table->dropForeign(['id_evaluasi_restrukturisasi']);
            });
        } catch (\Exception $e) {
            try {
                DB::statement('ALTER TABLE evaluasi_kelengkapan_dokumen DROP FOREIGN KEY evaluasi_kelengkapan_dokumen_id_evaluasi_restrukturisasi_foreign');
            } catch (\Exception $e2) {
                // Continue
            }
        }
        Schema::table('evaluasi_kelengkapan_dokumen', function (Blueprint $table) {
            $table->renameColumn('id_evaluasi_restrukturisasi', 'id_evaluasi_cicilan');
        });
        Schema::table('evaluasi_kelengkapan_dokumen', function (Blueprint $table) {
            $table->foreign('id_evaluasi_cicilan', 'fk_kelengkapan_to_evaluasi_cicilan')
                ->references('id_evaluasi_cicilan')
                ->on('evaluasi_pengajuan_cicilan')
                ->onDelete('cascade');
        });

        // ============================================================
        // B11. evaluasi_kelayakan_debitur: FK rename id_evaluasi_restrukturisasi → id_evaluasi_cicilan
        // ============================================================
        try {
            Schema::table('evaluasi_kelayakan_debitur', function (Blueprint $table) {
                $table->dropForeign(['id_evaluasi_restrukturisasi']);
            });
        } catch (\Exception $e) {
            try {
                DB::statement('ALTER TABLE evaluasi_kelayakan_debitur DROP FOREIGN KEY evaluasi_kelayakan_debitur_id_evaluasi_restrukturisasi_foreign');
            } catch (\Exception $e2) {
                // Continue
            }
        }
        Schema::table('evaluasi_kelayakan_debitur', function (Blueprint $table) {
            $table->renameColumn('id_evaluasi_restrukturisasi', 'id_evaluasi_cicilan');
        });
        Schema::table('evaluasi_kelayakan_debitur', function (Blueprint $table) {
            $table->foreign('id_evaluasi_cicilan', 'fk_kelayakan_to_evaluasi_cicilan')
                ->references('id_evaluasi_cicilan')
                ->on('evaluasi_pengajuan_cicilan')
                ->onDelete('cascade');
        });

        // ============================================================
        // B12. Rename: evaluasi_analisa_restrukturisasi → evaluasi_analisa_cicilan
        //     PK: id_analisa_restrukturisasi → id_analisa_cicilan
        //     FK: id_evaluasi_restrukturisasi → id_evaluasi_cicilan
        // ============================================================
        Schema::table('evaluasi_analisa_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_analisa_restrukturisasi', 'id_analisa_cicilan');
            $table->renameColumn('id_evaluasi_restrukturisasi', 'id_evaluasi_cicilan');
        });
        Schema::rename('evaluasi_analisa_restrukturisasi', 'evaluasi_analisa_cicilan');

        // ============================================================
        // B13. Rename: persetujuan_komite_restrukturisasi → persetujuan_komite_cicilan
        //     FK: id_evaluasi_restrukturisasi → id_evaluasi_cicilan
        // ============================================================
        Schema::table('persetujuan_komite_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_evaluasi_restrukturisasi', 'id_evaluasi_cicilan');
        });
        Schema::rename('persetujuan_komite_restrukturisasi', 'persetujuan_komite_cicilan');

        // ============================================================
        // B14. Rename: riwayat_pembayaran_restrukturisasi → riwayat_pembayaran_cicilan
        //     (no restrukturisasi-specific FK columns)
        // ============================================================
        Schema::rename('riwayat_pembayaran_restrukturisasi', 'riwayat_pembayaran_cicilan');

        // ============================================================
        // BAGIAN C: UPDATE DATA — status 'Proses Restrukturisasi' → 'Proses Cicilan'
        // ============================================================
        DB::statement("UPDATE pengajuan_peminjaman SET status = 'Proses Cicilan' WHERE status = 'Proses Restrukturisasi'");
    }

    public function down(): void
    {
        // Reverse order: rename cicilan → restrukturisasi

        // C. Reverse status data
        DB::statement("UPDATE pengajuan_peminjaman SET status = 'Proses Restrukturisasi' WHERE status = 'Proses Cicilan'");

        // B14
        Schema::rename('riwayat_pembayaran_cicilan', 'riwayat_pembayaran_restrukturisasi');

        // B13
        Schema::rename('persetujuan_komite_cicilan', 'persetujuan_komite_restrukturisasi');
        Schema::table('persetujuan_komite_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_evaluasi_cicilan', 'id_evaluasi_restrukturisasi');
        });

        // B12
        Schema::rename('evaluasi_analisa_cicilan', 'evaluasi_analisa_restrukturisasi');
        Schema::table('evaluasi_analisa_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_analisa_cicilan', 'id_analisa_restrukturisasi');
            $table->renameColumn('id_evaluasi_cicilan', 'id_evaluasi_restrukturisasi');
        });

        // B11
        Schema::table('evaluasi_kelayakan_debitur', function (Blueprint $table) {
            $table->dropForeign('fk_kelayakan_to_evaluasi_cicilan');
            $table->renameColumn('id_evaluasi_cicilan', 'id_evaluasi_restrukturisasi');
        });

        // B10
        Schema::table('evaluasi_kelengkapan_dokumen', function (Blueprint $table) {
            $table->dropForeign('fk_kelengkapan_to_evaluasi_cicilan');
            $table->renameColumn('id_evaluasi_cicilan', 'id_evaluasi_restrukturisasi');
        });

        // B9
        Schema::table('evaluasi_pengajuan_cicilan', function (Blueprint $table) {
            $table->dropForeign('fk_evaluasi_cicilan_to_pengajuan');
        });
        Schema::rename('evaluasi_pengajuan_cicilan', 'evaluasi_pengajuan_restrukturisasi');
        Schema::table('evaluasi_pengajuan_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_evaluasi_cicilan', 'id_evaluasi_restrukturisasi');
            $table->renameColumn('id_pengajuan_cicilan', 'id_pengajuan_restrukturisasi');
        });

        // B8
        Schema::table('history_status_pengajuan_cicilan', function (Blueprint $table) {
            $table->dropForeign('fk_history_cicilan_to_pengajuan');
        });
        Schema::rename('history_status_pengajuan_cicilan', 'history_status_pengajuan_restrukturisasi');
        Schema::table('history_status_pengajuan_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_history_status_cicilan', 'id_history_status_restrukturisasi');
            $table->renameColumn('id_pengajuan_cicilan', 'id_pengajuan_restrukturisasi');
        });

        // B7
        Schema::table('jadwal_angsuran', function (Blueprint $table) {
            $table->dropForeign('jadwal_angsuran_id_penyesuaian_cicilan_foreign');
            $table->renameColumn('id_penyesuaian_cicilan', 'id_program_restrukturisasi');
        });

        // B6
        Schema::table('penyesuaian_cicilan', function (Blueprint $table) {
            $table->dropForeign('fk_penyesuaian_to_pengajuan_cicilan');
        });
        Schema::rename('penyesuaian_cicilan', 'program_restrukturisasi');
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_penyesuaian_cicilan', 'id_program_restrukturisasi');
            $table->renameColumn('id_pengajuan_cicilan', 'id_pengajuan_restrukturisasi');
        });

        // B5
        Schema::rename('pengajuan_cicilan', 'pengajuan_restrukturisasi');
        Schema::table('pengajuan_restrukturisasi', function (Blueprint $table) {
            $table->renameColumn('id_pengajuan_cicilan', 'id_pengajuan_restrukturisasi');
        });

        // A4
        Schema::table('bukti_peminjaman', function (Blueprint $table) {
            $table->renameColumn('nilai_bunga', 'nilai_bagi_hasil');
        });

        // A5
        Schema::table('master_sumber_pendanaan_eksternal', function (Blueprint $table) {
            $table->renameColumn('persentase_bunga', 'persentase_bagi_hasil');
        });

        // A3
        Schema::table('pengembalian_pinjaman', function (Blueprint $table) {
            $table->renameColumn('total_bunga', 'total_bagi_hasil');
            $table->renameColumn('sisa_bunga', 'sisa_bagi_hasil');
        });

        // A2
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->renameColumn('total_bunga', 'total_bagi_hasil');
            $table->renameColumn('sisa_bunga', 'sisa_bagi_hasil');
            $table->renameColumn('total_bunga_saat_ini', 'total_bagi_hasil_saat_ini');
            $table->renameColumn('persentase_bunga', 'persentase_bagi_hasil');
        });

        // A1
        Schema::table('ar_perbulan', function (Blueprint $table) {
            $table->renameColumn('total_bunga', 'total_bagi_hasil');
            $table->renameColumn('total_pengembalian_bunga', 'total_pengembalian_bagi_hasil');
            $table->renameColumn('sisa_bunga', 'sisa_bagi_hasil');
        });
    }
};
