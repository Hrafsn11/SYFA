<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PengembalianInvestasiFinlog extends Model
{
    use HasUlids;

    protected $table = 'pengembalian_investasi_finlog';
    protected $primaryKey = 'id_pengembalian_investasi_finlog';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengajuan_investasi_finlog',
        'dana_pokok_dibayar',
        'bagi_hasil_dibayar',
        'total_dibayar',
        'bukti_transfer',
        'tanggal_pengembalian',
        'created_by',
    ];

    protected $casts = [
        'dana_pokok_dibayar' => 'decimal:2',
        'bagi_hasil_dibayar' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'tanggal_pengembalian' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto hitung total
            $model->total_dibayar = ($model->dana_pokok_dibayar ?? 0) + ($model->bagi_hasil_dibayar ?? 0);
        });

        static::updating(function ($model) {
            $model->total_dibayar = ($model->dana_pokok_dibayar ?? 0) + ($model->bagi_hasil_dibayar ?? 0);
        });
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanInvestasiFinlog::class, 'id_pengajuan_investasi_finlog', 'id_pengajuan_investasi_finlog');
    }

    /**
     * Hitung total pengembalian (pokok & bagi hasil) untuk satu pengajuan investasi finlog.
     */
    public static function getTotalDikembalikan(string $idPengajuanInvestasiFinlog)
    {
        return self::where('id_pengajuan_investasi_finlog', $idPengajuanInvestasiFinlog)
            ->selectRaw('
                COALESCE(SUM(dana_pokok_dibayar), 0) as total_pokok,
                COALESCE(SUM(bagi_hasil_dibayar), 0) as total_bunga,
                COALESCE(SUM(total_dibayar), 0) as total_semua,
                COUNT(*) as jumlah_transaksi
            ')
            ->first();
    }
}


