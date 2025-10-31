<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeminjamanInstallmentFinancing extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'peminjaman_installment_financing';
    protected $primaryKey = 'id_installment';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_debitur','nama_bank','no_rekening','nama_rekening','total_pinjaman','tenor_pembayaran','persentase_bagi_hasil','pps','sfinance','total_pembayaran','status','yang_harus_dibayarkan','catatan_lainnya','created_by','updated_by','nomor_peminjaman'
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
