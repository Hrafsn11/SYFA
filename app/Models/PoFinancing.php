<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PoFinancing extends Model
{
    use HasUlids;

    protected $table = 'po_financing';
    protected $primaryKey = 'id_po_financing_detail';
    public $incrementing = false;
    protected $keyType = 'string';

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
