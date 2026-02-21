<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiKelayakanDebitur extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'evaluasi_kelayakan_debitur';
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_kelayakan_debitur';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_evaluasi_restrukturisasi',
        'kriteria',
        'status',
        'catatan',
    ];

    /**
     * Get the evaluasi that this kriteria belongs to.
     */
    public function pengajuanCicilan()
    {
        return $this->belongsTo(PengajuanCicilan::class, 'id_pengajuan_restrukturisasi', 'id_pengajuan_restrukturisasi');
    }

    /**
     * Scope to filter eligible criteria.
     */
    public function scopeLayak($query)
    {
        return $query->where('status', 'Ya');
    }

    /**
     * Scope to filter non-eligible criteria.
     */
    public function scopeTidakLayak($query)
    {
        return $query->where('status', 'Tidak');
    }

    /**
     * Check if criteria is met.
     */
    public function isLayak(): bool
    {
        return $this->status === 'Ya';
    }
}
