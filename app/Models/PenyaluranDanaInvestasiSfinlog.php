<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyaluranDanaInvestasiSfinlog extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'penyaluran_dana_investasi_sfinlog';

    protected $primaryKey = 'id_penyaluran_dana_investasi_sfinlog';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_pengajuan_investasi_finlog',
        'id_cells_project',
        'id_project',
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

    public function pengajuanInvestasiFinlog(): BelongsTo
    {
        return $this->belongsTo(PengajuanInvestasiFinlog::class, 'id_pengajuan_investasi_finlog', 'id_pengajuan_investasi_finlog');
    }

    public function cellsProject(): BelongsTo
    {
        return $this->belongsTo(CellsProject::class, 'id_cells_project', 'id_cells_project');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'id_project', 'id_project');
    }

    public function riwayatPengembalian()
    {
        return $this->hasMany(RiwayatPengembalianDanaInvestasiSfinlog::class, 'id_penyaluran_dana_investasi_sfinlog', 'id_penyaluran_dana_investasi_sfinlog');
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
