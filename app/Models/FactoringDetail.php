<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactoringDetail extends Model
{
    use HasFactory;

    protected $table = 'factoring_financing';
    protected $primaryKey = 'id_factoring_detail';

    protected $fillable = [
        'id_factoring',
        'no_kontrak',
        'nama_client',
        'nilai_invoice',
        'nilai_pinjaman',
        'nilai_bagi_hasil',
        'kontrak_date',
        'due_date',
        'dokumen_invoice',
        'dokumen_so',
        'dokumen_bast',
        'dokumen_kontrak',
    ];

    public function header()
    {
        return $this->belongsTo(PeminjamanFactoring::class, 'id_factoring', 'id_factoring');
    }
}
