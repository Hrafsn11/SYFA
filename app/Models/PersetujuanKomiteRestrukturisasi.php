<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersetujuanKomiteRestrukturisasi extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'persetujuan_komite_restrukturisasi';
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_persetujuan_komite';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_evaluasi_restrukturisasi',
        'nama_anggota',
        'jabatan',
        'tanggal_persetujuan',
        'ttd_digital',
        'user_id',
        'urutan',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tanggal_persetujuan' => 'date',
        'urutan' => 'integer',
    ];

    /**
     * Get the evaluasi that this persetujuan belongs to.
     */
    public function evaluasi(): BelongsTo
    {
        return $this->belongsTo(EvaluasiPengajuanRestrukturisasi::class, 'id_evaluasi_restrukturisasi', 'id_evaluasi_restrukturisasi');
    }

    /**
     * Get the user (if linked to system user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
