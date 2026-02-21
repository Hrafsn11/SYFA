<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyaluranDanaInvestasi extends Model
{
    use HasFactory, HasUlids;

    protected  = 'penyaluran_dana_investasi';

    protected  = 'id_penyaluran_dana_investasi';

    public  = false;

    protected  = 'string';

    protected  = [
        'id_pengajuan_investasi',
        'id_debitur',
        'nominal_yang_disalurkan',
        'nominal_yang_dikembalikan',
        'tanggal_pengiriman_dana',
        'tanggal_pengembalian',
    ];

    protected  = [
        'nominal_yang_disalurkan' => 'decimal:2',
        'nominal_yang_dikembalikan' => 'decimal:2',
        'tanggal_pengiriman_dana' => 'date',
        'tanggal_pengembalian' => 'date',
    ];

    public function pengajuanInvestasi(): BelongsTo
    {
        return ->belongsTo(PengajuanInvestasi::class, 'id_pengajuan_investasi', 'id_pengajuan_investasi');
    }

    public function debitur(): BelongsTo
    {
        return ->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }

    public function riwayatPengembalian()
    {
        return ->hasMany(RiwayatPengembalianDanaInvestasi::class, 'id_penyaluran_dana_investasi', 'id_penyaluran_dana_investasi');
    }

    public function getTotalDikembalikanAttribute()
    {
        return ->riwayatPengembalian()->sum('nominal_dikembalikan');
    }
    public function getSisaBelumDikembalikanAttribute()
    {
        return max(0, (float)->nominal_yang_disalurkan - ->total_dikembalikan);
    }
}
