<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianInvoice extends Model
{
    use HasFactory, HasUlids;
    
    protected $table = 'pengembalian_invoice';
    protected $primaryKey = 'id_pengembalian_invoice';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_pengembalian',
        'nominal_yg_dibayarkan',
        'bukti_pembayaran',
    ];

    protected $casts = [
        'nominal_yg_dibayarkan' => 'decimal:2',
    ];

    public function pengembalianTagihanPinjaman()
    {
        return $this->belongsTo(PengembalianTagihanPinjaman::class, 'id_pengembalian', 'ulid');
    }
    
    public function pengembalian()
    {
        return $this->belongsTo(PengembalianPinjaman::class, 'id_pengembalian', 'ulid');
    }

    public function reportPengembalian()
    {
        return $this->hasManyThrough(
            ReportPengembalian::class,
            PengembalianPinjaman::class,
            'ulid',
            'id_pengembalian',
            'id_pengembalian',
            'ulid'
        );
    }
}
