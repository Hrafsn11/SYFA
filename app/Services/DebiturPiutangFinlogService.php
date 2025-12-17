<?php

namespace App\Services;

use App\Models\PeminjamanFinlog;
use Illuminate\Database\Eloquent\Builder;

class DebiturPiutangFinlogService
{
    public function getQuery(): Builder
    {
        return PeminjamanFinlog::query()
            ->with([
                'debitur',           
                'cellsProject',     
                'latestPengembalian' 
            ])
            ->select('peminjaman_finlog.*');
    }
}
