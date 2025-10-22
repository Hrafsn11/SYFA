<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentFinancing extends Model
{
    protected $table = 'installment_financing';
    protected $primaryKey = 'id_installment_detail';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_installment','no_invoice','nama_client','nilai_invoice','invoice_date','nama_barang','dokumen_invoice','dokumen_lainnya'
    ];

    public function header()
    {
        return $this->belongsTo(PeminjamanInstallmentFinancing::class, 'id_installment', 'id_installment');
    }
}
