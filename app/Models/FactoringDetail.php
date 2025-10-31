<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FactoringDetail extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'factoring_financing';
    protected $primaryKey = 'id_factoring_detail';
    public $incrementing = false;
    protected $keyType = 'string';

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
