<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianInvoice extends Model
{
    use HasFactory;
    
    protected $table = 'pengembalian_invoice';
    protected $primaryKey = 'id_pengembalian_invoice';
    
    protected $fillable = [
        'id_pengembalian',
        'nominal_yg_dibayarkan',
        'bukti_pembayaran',
    ];

    protected $casts = [
        'nominal_yg_dibayarkan' => 'decimal:2',
    ];

    public function pengembalianPinjaman()
    {
        return $this->belongsTo(PengembalianPinjaman::class, 'id_pengembalian', 'ulid');
    }
}
