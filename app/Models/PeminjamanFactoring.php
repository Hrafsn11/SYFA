<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeminjamanFactoring extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'peminjaman_factoring';
    protected $primaryKey = 'id_factoring';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_debitur',
        'no_kontrak',
        'nama_bank',
        'no_rekening',
        'nama_rekening',
        'total_nominal_yang_dialihkan',
        'harapan_tanggal_pencairan',
        'total_bagi_hasil',
        'rencana_tgl_pembayaran',
        'pembayaran_total',
        'catatan_lainnya',
        'status',
        'nomor_peminjaman',
    ];

    public function details()
    {
        return $this->hasMany(FactoringDetail::class, 'id_factoring', 'id_factoring');
    }

    public function debitur()
    {
        return $this->belongsTo(\App\Models\MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }
}
