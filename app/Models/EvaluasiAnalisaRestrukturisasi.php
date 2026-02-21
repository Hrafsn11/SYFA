<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiAnalisaRestrukturisasi extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'evaluasi_analisa_restrukturisasi';
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_analisa_restrukturisasi';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_evaluasi_restrukturisasi',
        'aspek',
        'evaluasi',
        'catatan',
    ];

    /**
     * Get the evaluasi that this analisa belongs to.
     */
    public function pengajuanCicilan()
    {
        return $this->belongsTo(PengajuanCicilan::class, 'id_pengajuan_restrukturisasi', 'id_pengajuan_restrukturisasi');
    }

    /**
     * Get evaluasi badge class based on result.
     */
    public function getEvaluasiBadgeClass(): string
    {
        return match(strtolower($this->evaluasi)) {
            'sesuai', 'memadai', 'layak' => 'success',
            'tidak', 'tidak sesuai', 'tidak layak', 'defisit' => 'danger',
            'sedang' => 'warning',
            'rendah' => 'success',
            'tinggi' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Check if analisa result is positive.
     */
    public function isPositif(): bool
    {
        $positifResults = ['sesuai', 'memadai', 'layak', 'rendah'];
        return in_array(strtolower($this->evaluasi), $positifResults);
    }
}
