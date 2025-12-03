<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramRestrukturisasi extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'program_restrukturisasi';
    protected $primaryKey = 'id_program_restrukturisasi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengajuan_restrukturisasi',
        'metode_perhitungan',
        'plafon_pembiayaan',
        'suku_bunga_per_tahun',
        'jangka_waktu_total',
        'masa_tenggang',
        'tanggal_mulai_cicilan',
        'total_pokok',
        'total_margin',
        'total_cicilan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'plafon_pembiayaan' => 'decimal:2',
        'suku_bunga_per_tahun' => 'decimal:2',
        'total_pokok' => 'decimal:2',
        'total_margin' => 'decimal:2',
        'total_cicilan' => 'decimal:2',
        'tanggal_mulai_cicilan' => 'date',
        'jangka_waktu_total' => 'integer',
        'masa_tenggang' => 'integer',
    ];

    // Relationships
    public function pengajuanRestrukturisasi()
    {
        return $this->belongsTo(PengajuanRestrukturisasi::class, 'id_pengajuan_restrukturisasi', 'id_pengajuan_restrukturisasi');
    }

    public function jadwalAngsuran()
    {
        return $this->hasMany(JadwalAngsuran::class, 'id_program_restrukturisasi', 'id_program_restrukturisasi')
            ->orderBy('no');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id');
    }
}
