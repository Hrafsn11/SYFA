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
        'total_terbayar',
        'bukti_pembayaran',
    ];

    protected $casts = [
        'pokok' => 'decimal:2',
        'margin' => 'decimal:2',
        'total_cicilan' => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'total_terbayar' => 'decimal:2',
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

    /**
     * Relasi ke Riwayat Pembayaran
     */
    public function riwayatPembayaran()
    {
        return $this->hasMany(RiwayatPembayaranRestrukturisasi::class, 'id_jadwal_angsuran', 'id_jadwal_angsuran');
    }

    /**
     * Hitung sisa pembayaran yang harus dibayar
     */
    public function getSisaPembayaranAttribute(): float
    {
        return max(0, (float) $this->total_cicilan - (float) $this->total_terbayar);
    }

    /**
     * Cek apakah angsuran sudah lunas
     */
    public function getIsLunasAttribute(): bool
    {
        return $this->total_terbayar >= $this->total_cicilan;
    }
}
