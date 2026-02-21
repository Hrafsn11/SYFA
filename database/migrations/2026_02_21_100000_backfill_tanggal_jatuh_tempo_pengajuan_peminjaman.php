<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill tanggal_jatuh_tempo untuk record pengajuan_peminjaman yang sudah
     * berstatus "Dana Sudah Dicairkan" tetapi tanggal_jatuh_tempo masih NULL.
     *
     * Rumus:
     * - Non-Installment : tanggal_pencairan + 30 hari
     * - Installment     : tanggal_pencairan + (tenor_pembayaran × 30) hari
     *
     * tanggal_pencairan diambil dari history_status_pengajuan_pinjaman.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE pengajuan_peminjaman pp
            JOIN (
                SELECT id_pengajuan_peminjaman,
                       MAX(tanggal_pencairan) AS tanggal_pencairan
                FROM history_status_pengajuan_pinjaman
                WHERE tanggal_pencairan IS NOT NULL
                GROUP BY id_pengajuan_peminjaman
            ) h ON pp.id_pengajuan_peminjaman = h.id_pengajuan_peminjaman
            SET
                pp.tanggal_jatuh_tempo = CASE
                    WHEN pp.jenis_pembiayaan = 'Installment' AND pp.tenor_pembayaran IS NOT NULL AND pp.tenor_pembayaran > 0
                        THEN DATE_ADD(h.tanggal_pencairan, INTERVAL (pp.tenor_pembayaran * 30) DAY)
                    ELSE
                        DATE_ADD(h.tanggal_pencairan, INTERVAL 30 DAY)
                END,
                pp.sisa_bayar_pokok = COALESCE(pp.sisa_bayar_pokok, pp.total_pinjaman),
                pp.sisa_bunga       = COALESCE(pp.sisa_bunga, pp.total_bunga)
            WHERE pp.tanggal_jatuh_tempo IS NULL
              AND pp.status = 'Dana Sudah Dicairkan'
        ");
    }

    public function down(): void
    {
        // Tidak ada rollback yang aman — data sudah di-set NULL sebelumnya
        // tidak bisa di-restore tanpa snapshot.
    }
};
