<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceFinancing extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'invoice_financing';
    protected $primaryKey = 'id_invoice';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_invoice_financing','no_invoice','nama_client','nilai_invoice','nilai_pinjaman','nilai_bagi_hasil','invoice_date','due_date','dokumen_invoice','dokumen_kontrak','dokumen_so','dokumen_bast','created_by'
    ];

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanInvoiceFinancing::class, 'id_invoice_financing', 'id_invoice_financing');
    }
}
