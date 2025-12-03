<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiKelengkapanDokumen extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'evaluasi_kelengkapan_dokumen';
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_kelengkapan_dokumen';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_evaluasi_restrukturisasi',
        'nama_dokumen',
        'status',
        'catatan',
    ];

    /**
     * Get the evaluasi that this dokumen belongs to.
     */
    public function evaluasi(): BelongsTo
    {
        return $this->belongsTo(EvaluasiPengajuanRestrukturisasi::class, 'id_evaluasi_restrukturisasi', 'id_evaluasi_restrukturisasi');
    }

    /**
     * Scope to filter complete documents.
     */
    public function scopeLengkap($query)
    {
        return $query->where('status', 'Ya');
    }

    /**
     * Scope to filter incomplete documents.
     */
    public function scopeTidakLengkap($query)
    {
        return $query->where('status', 'Tidak');
    }

    /**
     * Check if document is complete.
     */
    public function isLengkap(): bool
    {
        return $this->status === 'Ya';
    }
}
