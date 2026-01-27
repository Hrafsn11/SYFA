<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPengembalianDeposito extends Model
{
    use HasUlids;

    protected $table = 'riwayat_pengembalian_deposito';
    protected $primaryKey = 'id_riwayat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_penyaluran_deposito',
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

    public function penyaluranDeposito(): BelongsTo
    {
        return $this->belongsTo(PenyaluranDeposito::class, 'id_penyaluran_deposito', 'id_penyaluran_deposito');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diinput_oleh', 'id');
    }
}
