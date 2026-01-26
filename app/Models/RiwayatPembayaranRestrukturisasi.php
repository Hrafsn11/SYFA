<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPembayaranRestrukturisasi extends Model
{
    use HasUlids;

    protected $table = 'riwayat_pembayaran_restrukturisasi';
    protected $primaryKey = 'id_riwayat_pembayaran';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_jadwal_angsuran',
        'nominal_bayar',
        'bukti_pembayaran',
        'tanggal_bayar',
        'status',
        'catatan',
        'dikonfirmasi_oleh',
        'dikonfirmasi_at',
    ];

    protected $casts = [
        'nominal_bayar' => 'decimal:2',
        'tanggal_bayar' => 'date',
        'dikonfirmasi_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_TERTUNDA = 'Tertunda';
    const STATUS_DIKONFIRMASI = 'Dikonfirmasi';
    const STATUS_DITOLAK = 'Ditolak';

    /**
     * Relasi ke Jadwal Angsuran
     */
    public function jadwalAngsuran(): BelongsTo
    {
        return $this->belongsTo(JadwalAngsuran::class, 'id_jadwal_angsuran', 'id_jadwal_angsuran');
    }

    /**
     * Relasi ke User yang mengkonfirmasi
     */
    public function konfirmator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh', 'id');
    }

    /**
     * Scope untuk pembayaran tertunda
     */
    public function scopeTertunda($query)
    {
        return $query->where('status', self::STATUS_TERTUNDA);
    }

    /**
     * Scope untuk pembayaran yang sudah dikonfirmasi
     */
    public function scopeDikonfirmasi($query)
    {
        return $query->where('status', self::STATUS_DIKONFIRMASI);
    }

    /**
     * Scope untuk pembayaran yang ditolak
     */
    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }
}
