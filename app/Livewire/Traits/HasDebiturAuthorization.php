<?php

namespace App\Livewire\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait HasDebiturAuthorization
{
    protected function applyDebiturAuthorization(Builder $query): Builder
    {
        $user = auth()->user();

        if (!$user || $user->hasRole(['super-admin', 'admin', 'sfinance'])) {
            return $query;
        }

        return $query->where('master_debitur_dan_investor.user_id', $user->id);
    }

    protected function getLatestPengembalianSubquery()
    {
        return DB::raw('(
            SELECT p1.* 
            FROM pengembalian_pinjaman p1
            INNER JOIN (
                SELECT id_pengajuan_peminjaman, MAX(updated_at) as max_updated
                FROM pengembalian_pinjaman
                GROUP BY id_pengajuan_peminjaman
            ) p2 ON p1.id_pengajuan_peminjaman = p2.id_pengajuan_peminjaman 
                AND p1.updated_at = p2.max_updated
        ) as pengembalian_pinjaman');
    }
}
