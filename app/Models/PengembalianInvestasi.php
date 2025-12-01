<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PengembalianInvestasi extends Model
{
    protected $table = 'pengembalian_investasi';
    protected $primaryKey = 'id_pengembalian_investasi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengembalian_investasi',
        'id_pengajuan_investasi',
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
            if (empty($model->id_pengembalian_investasi)) {
                $model->id_pengembalian_investasi = (string) Str::ulid();
            }
            
            // Auto calculate total
            $model->total_dibayar = $model->dana_pokok_dibayar + $model->bagi_hasil_dibayar;
        });

        static::updating(function ($model) {
            // Auto calculate total
            $model->total_dibayar = $model->dana_pokok_dibayar + $model->bagi_hasil_dibayar;
        });
    }

    // Relationships
    public function pengajuanInvestasi()
    {
        return $this->belongsTo(PengajuanInvestasi::class, 'id_pengajuan_investasi', 'id_pengajuan_investasi');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    // Helper Methods
    public static function getTotalDikembalikan($idPengajuanInvestasi)
    {
        return self::where('id_pengajuan_investasi', $idPengajuanInvestasi)
            ->selectRaw('
                COALESCE(SUM(dana_pokok_dibayar), 0) as total_pokok,
                COALESCE(SUM(bagi_hasil_dibayar), 0) as total_bagi_hasil,
                COALESCE(SUM(total_dibayar), 0) as total_semua,
                COUNT(*) as jumlah_transaksi
            ')
            ->first();
    }

    public static function getHistory($idPengajuanInvestasi)
    {
        return self::where('id_pengajuan_investasi', $idPengajuanInvestasi)
            ->with('creator:id,name')
            ->orderBy('tanggal_pengembalian', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Accessor for formatted currency
    public function getDanaPokokDibayarFormattedAttribute()
    {
        return 'Rp ' . number_format($this->dana_pokok_dibayar, 0, ',', '.');
    }

    public function getBagiHasilDibayarFormattedAttribute()
    {
        return 'Rp ' . number_format($this->bagi_hasil_dibayar, 0, ',', '.');
    }

    public function getTotalDibayarFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_dibayar, 0, ',', '.');
    }
}
