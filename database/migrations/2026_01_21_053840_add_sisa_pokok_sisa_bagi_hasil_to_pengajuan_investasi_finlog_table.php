<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan_investasi_finlog', function (Blueprint $table) {
            $table->decimal('sisa_pokok', 20, 2)->default(0)->after('nominal_bagi_hasil_yang_didapat');
            $table->decimal('sisa_bagi_hasil', 20, 2)->default(0)->after('sisa_pokok');
        });

        // Only populate existing data if there are records and related table exists
        // This is safe for fresh migrations (empty database)
        try {
            // Check if we have any data to update
            $hasData = DB::table('pengajuan_investasi_finlog')->exists();
            $hasPengembalianTable = Schema::hasTable('pengembalian_investasi_finlog');

            if ($hasData) {
                if ($hasPengembalianTable) {
                    // Populate existing data: Calculate initial values
                    // sisa_pokok = nominal_investasi - total_pokok_dikembalikan
                    // sisa_bagi_hasil = bagi_hasil_per_nominal - total_bagi_hasil_dibayar
                    DB::statement("
                        UPDATE pengajuan_investasi_finlog pif
                        SET 
                            sisa_pokok = COALESCE(pif.nominal_investasi, 0) - COALESCE(
                                (SELECT SUM(dana_pokok_dibayar) FROM pengembalian_investasi_finlog WHERE id_pengajuan_investasi_finlog = pif.id_pengajuan_investasi_finlog),
                                0
                            ),
                            sisa_bagi_hasil = (COALESCE(pif.persentase_bagi_hasil, 0) * COALESCE(pif.nominal_investasi, 0) / 100 / 12 * COALESCE(pif.lama_investasi, 0)) - COALESCE(
                                (SELECT SUM(bagi_hasil_dibayar) FROM pengembalian_investasi_finlog WHERE id_pengajuan_investasi_finlog = pif.id_pengajuan_investasi_finlog),
                                0
                            )
                    ");
                } else {
                    // If no pengembalian table yet, just set sisa_pokok = nominal_investasi
                    DB::statement("
                        UPDATE pengajuan_investasi_finlog 
                        SET 
                            sisa_pokok = COALESCE(nominal_investasi, 0),
                            sisa_bagi_hasil = (COALESCE(persentase_bagi_hasil, 0) * COALESCE(nominal_investasi, 0) / 100 / 12 * COALESCE(lama_investasi, 0))
                    ");
                }

                // Ensure no negative values
                DB::statement("
                    UPDATE pengajuan_investasi_finlog 
                    SET sisa_pokok = GREATEST(sisa_pokok, 0),
                        sisa_bagi_hasil = GREATEST(sisa_bagi_hasil, 0)
                ");
            }
        } catch (\Exception $e) {
            // Log but don't fail migration - data can be populated later
            \Illuminate\Support\Facades\Log::warning('Could not populate sisa_pokok/sisa_bagi_hasil in migration: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_investasi_finlog', function (Blueprint $table) {
            $table->dropColumn(['sisa_pokok', 'sisa_bagi_hasil']);
        });
    }
};
