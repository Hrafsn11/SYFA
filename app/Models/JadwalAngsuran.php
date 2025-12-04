<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalAngsuran extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'jadwal_angsuran';
    protected $primaryKey = 'id_jadwal_angsuran';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_program_restrukturisasi',
        'no',
        'tanggal_jatuh_tempo',
        'pokok',
        'margin',
        'total_cicilan',
        'catatan',
        'is_grace_period',
        'status',
        'tanggal_bayar',
        'nominal_bayar',
    ];

    protected $casts = [
        'pokok' => 'decimal:2',
        'margin' => 'decimal:2',
        'total_cicilan' => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'is_grace_period' => 'boolean',
        'no' => 'integer',
    ];

    // Relationships
    public function programRestrukturisasi()
    {
        return $this->belongsTo(ProgramRestrukturisasi::class, 'id_program_restrukturisasi', 'id_program_restrukturisasi');
    }
}
