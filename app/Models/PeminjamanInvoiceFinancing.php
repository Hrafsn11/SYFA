<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanInvoiceFinancing extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_invoice_financing';
    protected $primaryKey = 'id_invoice_financing';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_debitur','id_instansi','sumber_pembiayaan','nama_bank','no_rekening','nama_rekening','lampiran_sid','tujuan_pembiayaan','total_pinjaman','harapan_tanggal_pencairan','total_bagi_hasil','rencana_tgl_pembayaran','pembayaran_total','catatan_lainnya','status','created_by'
    ];

    public function invoices()
    {
        return $this->hasMany(InvoiceFinancing::class, 'id_invoice_financing', 'id_invoice_financing');
    }

    public function debitur()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }
}
