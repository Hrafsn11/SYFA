<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianPinjaman extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pengembalian_pinjaman';

    protected $primaryKey = 'ulid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id_pengajuan_peminjaman',
        'nama_perusahaan',
        'nomor_peminjaman',
        'total_pinjaman',
        'total_bagi_hasil',
        'tanggal_pencairan',
        'lama_pemakaian',
        'invoice_dibayarkan',
        'nominal_invoice',
        'sisa_bayar_pokok',
        'sisa_bagi_hasil',
        'catatan',
        'status',
    ];

    protected $casts = [
        'total_pinjaman' => 'decimal:2',
        'total_bagi_hasil' => 'decimal:2',
        'nominal_invoice' => 'decimal:2',
        'sisa_bayar_pokok' => 'decimal:2',
        'sisa_bagi_hasil' => 'decimal:2',
        'tanggal_pencairan' => 'date',
        'lama_pemakaian' => 'integer',
    ];

    public function pengajuanPeminjaman()
    {
        return $this->belongsTo(PengajuanPeminjaman::class, 'id_pengajuan_peminjaman', 'id_pengajuan_peminjaman');
    }

    public function pengembalianInvoices()
    {
        return $this->hasMany(PengembalianInvoice::class, 'id_pengembalian', 'ulid');
    }
}
