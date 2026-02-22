<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPengembalianDanaInvestasi extends Model
{
    use HasUlids;

    protected $table = 'riwayat_pengembalian_dana_investasi';
    protected $primaryKey = 'id_riwayat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_penyaluran_dana_investasi',
        'nominal_dikembalikan',
        'tanggal_pengembalian',
        'bukti_pengembalian',
        'catatan',
        'diinput_oleh',
    ];

    protected $casts = [
        'nominal_dikembalikan' => 'decimal:2',
        'tanggal_pengembalian' => 'date',
    ];

    public function penyaluranDanaInvestasi(): BelongsTo
    {
        return $this->belongsTo(PenyaluranDanaInvestasi::class, 'id_penyaluran_dana_investasi', 'id_penyaluran_dana_investasi');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diinput_oleh', 'id');
    }
}
