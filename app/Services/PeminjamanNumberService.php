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
     * Generate nomor peminjaman by reading from PengajuanPeminjaman model.
     * Format: PMJ-YYYYMM-PREFIX-XX
     * Example: PMJ-202510-INV-01, PMJ-202510-INV-02, etc.
     *
     * @param int $id
     * @param string $prefix
     * @param string|null $period (YYYYMM) optional
     * @return string
     */
    public function generateNumber(string $prefix = '', ?string $period = null): string
    {
        $period = $period ? preg_replace('/[^0-9]/', '', $period) : date('Ym');
        $pad = intval(env('PEMINJAMAN_SEQ_LENGTH', 2)); // Default to 2 digits (01, 02, etc.)
        
        // Build the pattern to search for: PMJ-202510-INV-%
        $pattern = 'PMJ-' . $period . '-' . strtoupper($prefix) . '-%';
        
        // Query PengajuanPeminjaman to find existing nomor_peminjaman with same pattern
        $existingNumbers = DB::table('pengajuan_peminjaman')
            ->where('nomor_peminjaman', 'LIKE', $pattern)
            ->pluck('nomor_peminjaman')
            ->filter() // Remove null values
            ->toArray();
        
        if (empty($existingNumbers)) {
            // No existing numbers found, start from 01
            $sequence_num = 1;
        } else {
            // Extract the last number from existing records
            $lastNumber = 0;
            foreach ($existingNumbers as $nomor) {
                // Extract the last part after the last hyphen
                $parts = explode('-', $nomor);
                if (count($parts) >= 4) {
                    $numberPart = (int) end($parts);
                    if ($numberPart > $lastNumber) {
                        $lastNumber = $numberPart;
                    }
                }
            }
            // Increment by 1
            $sequence_num = $lastNumber + 1;
        }
        
        $sequence = str_pad((string)$sequence_num, $pad, '0', STR_PAD_LEFT);

        $parts = [];
        $parts[] = 'PMJ';
        $parts[] = $period;
        if (!empty($prefix)) $parts[] = strtoupper($prefix);
        $parts[] = $sequence;

        return implode('-', $parts);
    }
}
