<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPeminjaman extends Model
{
    use HasFactory, HasUlids;
    protected $table = 'pengajuan_peminjaman';
    protected $primaryKey = 'id_pengajuan_peminjaman';
    
    protected $fillable = [
        'nomor_peminjaman',
        'id_debitur',
        'sumber_pembiayaan',
        'id_instansi',
        'nama_bank',
        'no_rekening',
        'nama_rekening',
        'lampiran_sid',
        'nilai_kol',
        'tujuan_pembiayaan',
        'jenis_pembiayaan',
        'total_pinjaman',
        'harapan_tanggal_pencairan',
        'total_bagi_hasil',
        'rencana_tgl_pembayaran',
        'pembayaran_total',
        'catatan_lainnya',
        'tenor_pembayaran',
        'persentase_bagi_hasil',
        'pps',
        's_finance',
        'yang_harus_dibayarkan',
        'total_nominal_yang_dialihkan',
        'status',
        'created_by',
        'updated_by',
    ];

    public function invoices()
    {
        return $this->hasMany(BuktiPeminjaman::class, 'id_pengajuan_peminjaman', 'id_pengajuan_peminjaman');
    }

    public function debitur()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }

    public function instansi()
    {
        return $this->belongsTo(MasterSumberPendanaanEksternal::class, 'id_instansi', 'id_instansi');
    }

    public function buktiPeminjaman()
    {
        return $this->hasMany(BuktiPeminjaman::class, 'id_pengajuan_peminjaman', 'id_pengajuan_peminjaman');
    }
}
