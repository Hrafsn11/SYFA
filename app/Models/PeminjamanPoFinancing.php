<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanPoFinancing extends Model
{
    protected $table = 'peminjaman_po_financing';
    protected $primaryKey = 'id_po_financing';
    public $incrementing = true;
    protected $fillable = [
        'id_debitur', 'id_instansi', 'no_kontrak', 'nama_bank', 'no_rekening', 'nama_rekening',
        'lampiran_sid', 'tujuan_pembiayaan', 'total_pinjaman', 'harapan_tanggal_pencairan',
        'total_bagi_hasil', 'rencana_tgl_pembayaran', 'pembayaran_total', 'catatan_lainnya',
        'status', 'sumber_pembiayaan', 'created_by', 'updated_by'
    ];

    public function details()
    {
        return $this->hasMany(PoFinancing::class, 'id_po_financing', 'id_po_financing');
    }

    public function debitur()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }
}
