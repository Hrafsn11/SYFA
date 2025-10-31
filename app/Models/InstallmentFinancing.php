<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class InstallmentFinancing extends Model
{
    use HasUlids;

    protected $table = 'installment_financing';
    protected $primaryKey = 'id_installment_detail';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_installment','no_invoice','nama_client','nilai_invoice','invoice_date','nama_barang','dokumen_invoice','dokumen_lainnya'
    ];

    public function header()
    {
        return $this->belongsTo(PeminjamanInstallmentFinancing::class, 'id_installment', 'id_installment');
    }
}
