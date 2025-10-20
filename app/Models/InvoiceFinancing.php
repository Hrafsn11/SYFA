<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceFinancing extends Model
{
    use HasFactory;

    protected $table = 'invoice_financing';
    protected $primaryKey = 'id_invoice';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_peminjaman','no_invoice','nama_client','nilai_invoice','nilai_pinjaman','nilai_bagi_hasil','invoice_date','due_date','dokumen_invoice','dokumen_kontrak','dokumen_so','dokumen_bast','created_by'
    ];

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanInvoiceFinancing::class, 'id_peminjaman', 'id_peminjaman');
    }
}
