<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';

    protected $fillable = [
        'nomor_peminjaman',
        'type',
        'total_pinjaman',
        'sumber_pembiayaan',
        'created_by',
    ];

    public function peminjamanable()
    {
        return $this->morphTo();
    }
}
