<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPengembalianDanaInvestasiSfinlog extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'riwayat_pengembalian_dana_investasi_sfinlog';

    protected $primaryKey = 'id_riwayat_pengembalian_dana_investasi_sfinlog';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_penyaluran_dana_investasi_sfinlog',
        'nominal_dikembalikan',
        'tanggal_pengembalian',
        'bukti_pengembalian',
        'catatan',
    ];

    protected $casts = [
        'nominal_dikembalikan' => 'decimal:2',
        'tanggal_pengembalian' => 'date',
    ];

    public function penyaluranDanaInvestasiSfinlog(): BelongsTo
    {
        return $this->belongsTo(PenyaluranDanaInvestasiSfinlog::class, 'id_penyaluran_dana_investasi_sfinlog', 'id_penyaluran_dana_investasi_sfinlog');
    }
}
