<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JenisInvestasi extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'jenis_investasi';

    protected $primaryKey = 'id_penyaluran_deposito';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_pengajuan_investasi',
        'id_debitur',
        'nominal_yang_disalurkan',
        'nominal_yang_dikembalikan',
        'tanggal_pengiriman_dana',
        'tanggal_pengembalian',
    ];

    protected $casts = [
        'nominal_yang_disalurkan' => 'decimal:2',
        'nominal_yang_dikembalikan' => 'decimal:2',
        'tanggal_pengiriman_dana' => 'date',
        'tanggal_pengembalian' => 'date',
    ];

    public function pengajuanInvestasi(): BelongsTo
    {
        return $this->belongsTo(PengajuanInvestasi::class, 'id_pengajuan_investasi', 'id_pengajuan_investasi');
    }

    public function debitur(): BelongsTo
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }

    public function riwayatPengembalian()
    {
        return $this->hasMany(RiwayatPengembalianDeposito::class, 'id_penyaluran_deposito', 'id_penyaluran_deposito');
    }

    public function getTotalDikembalikanAttribute()
    {
        return $this->riwayatPengembalian()->sum('nominal_dikembalikan');
    }
    public function getSisaBelumDikembalikanAttribute()
    {
        return max(0, (float)$this->nominal_yang_disalurkan - $this->total_dikembalikan);
    }
}
