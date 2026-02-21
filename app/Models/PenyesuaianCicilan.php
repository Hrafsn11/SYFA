<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenyesuaianCicilan extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'penyesuaian_cicilan';
    protected $primaryKey = 'id_penyesuaian_cicilan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengajuan_cicilan',
        'nomor_kontrak_restrukturisasi',
        'metode_perhitungan',
        'plafon_pembiayaan',
        'suku_bunga_per_tahun',
        'jangka_waktu_total',
        'nominal_yg_disetujui',
        'masa_tenggang',
        'tanggal_mulai_cicilan',
        'total_pokok',
        'total_margin',
        'total_cicilan',
        'total_terbayar',
        'jaminan',
        'kontrak_generated_at',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metode_perhitungan' => 'string',
        'plafon_pembiayaan' => 'decimal:2',
        'suku_bunga_per_tahun' => 'decimal:2',
        'total_pokok' => 'decimal:2',
        'total_margin' => 'decimal:2',
        'total_cicilan' => 'decimal:2',
        'total_terbayar' => 'decimal:2',
        'status' => 'string',
        'tanggal_mulai_cicilan' => 'date',
        'jangka_waktu_total' => 'integer',
        'masa_tenggang' => 'integer',
        'kontrak_generated_at' => 'datetime',
    ];

    // Relationships
    public function pengajuanCicilan()
    {
        return $this->belongsTo(PengajuanCicilan::class, 'id_pengajuan_cicilan', 'id_pengajuan_cicilan');
    }

    public function jadwalAngsuran()
    {
        return $this->hasMany(JadwalAngsuran::class, 'id_penyesuaian_cicilan', 'id_penyesuaian_cicilan')
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
