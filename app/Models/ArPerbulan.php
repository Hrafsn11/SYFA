<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArPerbulan extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'ar_perbulan';
    protected $primaryKey = 'id_ar_perbulan';

    protected $fillable = [
        'id_debitur',
        'nama_perusahaan',
        'periode',
        'bulan',
        'total_pinjaman_pokok',
        'total_bagi_hasil',
        'total_pengembalian_pokok',
        'total_pengembalian_bagi_hasil',
        'sisa_ar_pokok',
        'sisa_bagi_hasil',
        'sisa_ar_total',
        'jumlah_pinjaman',
        'status',
    ];

    protected $casts = [
        'periode' => 'date',
        'total_pinjaman_pokok' => 'decimal:2',
        'total_bagi_hasil' => 'decimal:2',
        'total_pengembalian_pokok' => 'decimal:2',
        'total_pengembalian_bagi_hasil' => 'decimal:2',
        'sisa_ar_pokok' => 'decimal:2',
        'sisa_bagi_hasil' => 'decimal:2',
        'sisa_ar_total' => 'decimal:2',
        'jumlah_pinjaman' => 'integer',
    ];

    public function debitur()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }
}