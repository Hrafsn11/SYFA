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
        // Add foreign keys after all tables are created to avoid ordering issues
        if (Schema::hasTable('evaluasi_analisa_restrukturisasi') && Schema::hasTable('evaluasi_pengajuan_restrukturisasi')) {
            try {
                Schema::table('evaluasi_analisa_restrukturisasi', function (Blueprint $table) {
                    $table->foreign('id_evaluasi_restrukturisasi', 'fk_ea_to_eval')
                        ->references('id_evaluasi_restrukturisasi')
                        ->on('evaluasi_pengajuan_restrukturisasi')
                        ->onDelete('cascade');
                });
            } catch (\Throwable $e) {
                // ignore if constraint cannot be added
            }
        }

        if (Schema::hasTable('evaluasi_kelayakan_debitur') && Schema::hasTable('evaluasi_pengajuan_restrukturisasi')) {
            try {
                Schema::table('evaluasi_kelayakan_debitur', function (Blueprint $table) {
                    $table->foreign('id_evaluasi_restrukturisasi', 'fk_ek_to_eval')
                        ->references('id_evaluasi_restrukturisasi')
                        ->on('evaluasi_pengajuan_restrukturisasi')
                        ->onDelete('cascade');
                });
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasTable('evaluasi_kelengkapan_dokumen') && Schema::hasTable('evaluasi_pengajuan_restrukturisasi')) {
            try {
                Schema::table('evaluasi_kelengkapan_dokumen', function (Blueprint $table) {
                    $table->foreign('id_evaluasi_restrukturisasi', 'fk_ekel_to_eval')
                        ->references('id_evaluasi_restrukturisasi')
                        ->on('evaluasi_pengajuan_restrukturisasi')
                        ->onDelete('cascade');
                });
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasTable('persetujuan_komite_restrukturisasi') && Schema::hasTable('evaluasi_pengajuan_restrukturisasi')) {
            try {
                Schema::table('persetujuan_komite_restrukturisasi', function (Blueprint $table) {
                    $table->foreign('id_evaluasi_restrukturisasi', 'fk_persetujuan_to_eval')
                        ->references('id_evaluasi_restrukturisasi')
                        ->on('evaluasi_pengajuan_restrukturisasi')
                        ->onDelete('cascade');
                });
            } catch (\Throwable $e) {
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            if (Schema::hasTable('evaluasi_analisa_restrukturisasi')) {
                Schema::table('evaluasi_analisa_restrukturisasi', function (Blueprint $table) {
                    $table->dropForeign('fk_ea_to_eval');
                });
            }
        } catch (\Throwable $e) {
        }

        try {
            if (Schema::hasTable('evaluasi_kelayakan_debitur')) {
                Schema::table('evaluasi_kelayakan_debitur', function (Blueprint $table) {
                    $table->dropForeign('fk_ek_to_eval');
                });
            }
        } catch (\Throwable $e) {
        }

        try {
            if (Schema::hasTable('evaluasi_kelengkapan_dokumen')) {
                Schema::table('evaluasi_kelengkapan_dokumen', function (Blueprint $table) {
                    $table->dropForeign('fk_ekel_to_eval');
                });
            }
        } catch (\Throwable $e) {
        }

        try {
            if (Schema::hasTable('persetujuan_komite_restrukturisasi')) {
                Schema::table('persetujuan_komite_restrukturisasi', function (Blueprint $table) {
                    $table->dropForeign('fk_persetujuan_to_eval');
                });
            }
        } catch (\Throwable $e) {
        }
    }
};
