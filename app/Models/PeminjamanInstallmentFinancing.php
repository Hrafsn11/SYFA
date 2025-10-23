<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanInstallmentFinancing extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_installment_financing';
    protected $primaryKey = 'id_installment';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_debitur','nama_bank','no_rekening','nama_rekening','total_pinjaman','tenor_pembayaran','persentase_bagi_hasil','pps','sfinance','total_pembayaran','status','yang_harus_dibayarkan','catatan_lainnya','created_by','updated_by'
    ];

    public function details()
    {
        return $this->hasMany(InstallmentFinancing::class, 'id_installment', 'id_installment');
    }

    public function debitur()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }
}
