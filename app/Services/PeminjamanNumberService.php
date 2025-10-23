<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class PeminjamanNumberService
{
    /**
     * Allocate a new nomor peminjaman.
     * Format: PMJ-YYYYMM-000001-TYPECODE (TYPECODE: INV/PO/INS/FAC)
     *
     * @param string $typeCode
     * @return string
     */
    public function allocate(string $typeCode): string
    {
        $pad = intval(env('PEMINJAMAN_SEQ_LENGTH', 2));
        if (Schema::hasTable('peminjaman_sequences')) {
            // Use a period key to keep numbers incremental per month. Example: 202510
            $period = Carbon::now()->format('Ym');
            $key = "peminjaman_{$period}";

            return DB::transaction(function () use ($key, $typeCode, $period, $pad) {
                // Select the sequence row FOR UPDATE
                $row = DB::table('peminjaman_sequences')->where('key', $key)->lockForUpdate()->first();

                if (!$row) {
                    DB::table('peminjaman_sequences')->insert([
                        'key' => $key,
                        'last_number' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $number = 1;
                } else {
                    $number = $row->last_number + 1;
                    DB::table('peminjaman_sequences')->where('key', $key)->update([
                        'last_number' => $number,
                        'updated_at' => now(),
                    ]);
                }

                $seq = str_pad((string)$number, $pad, '0', STR_PAD_LEFT);
                $nomor = sprintf('PMJ-%s-%s-%s', $period, $seq, strtoupper($typeCode));

                return $nomor;
            });
        }

        // If sequences table does not exist, fallback to a deterministic format based on time + random
        // but prefer generateFromId($id, $typeCode) which uses header id. Here we return a safe temporary code.
        $period = Carbon::now()->format('Ym');
        // fallback temp seq: use uniqid tail adjusted to pad length
        $uniq = uniqid();
        $seq = substr($uniq, -max(1, $pad));
        return sprintf('PMJ-%s-%s-%s', $period, strtoupper($seq), strtoupper($typeCode));
    }

    /**
     * Generate nomor peminjaman using a header id (fallback when no sequence table exists).
     * Format: PMJ-YYYYMM-000001-TYPE
     *
     * @param int $id
     * @param string $typeCode
     * @param string|null $period (YYYYMM) optional
     * @return string
     */
    public function generateFromId(int $id, string $typeCode, ?string $period = null): string
    {
        $period = $period ?? Carbon::now()->format('Ym');
        $pad = intval(env('PEMINJAMAN_SEQ_LENGTH', 2));
        $seq = str_pad((string)$id, $pad, '0', STR_PAD_LEFT);
        return sprintf('PMJ-%s-%s-%s', $period, $seq, strtoupper($typeCode));
    }
}
