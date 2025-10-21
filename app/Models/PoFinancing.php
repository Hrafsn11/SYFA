<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoFinancing extends Model
{
    protected $table = 'po_financing';
    protected $primaryKey = 'id_po_financing_detail';
    public $incrementing = true;
    protected $fillable = [
        'id_po_financing', 'no_kontrak', 'nama_client', 'nilai_invoice', 'nilai_pinjaman',
        'nilai_bagi_hasil', 'kontrak_date', 'due_date', 'dokumen_kontrak', 'dokumen_so',
        'dokumen_bast', 'dokumen_lainnya', 'created_by'
    ];

    public function header()
    {
        return $this->belongsTo(PeminjamanPoFinancing::class, 'id_po_financing', 'id_po_financing');
    }
}
