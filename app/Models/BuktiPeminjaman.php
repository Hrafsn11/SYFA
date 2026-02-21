<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPeminjaman extends Model
{
    use HasFactory, HasUlids;
    protected $table = 'bukti_peminjaman';
    protected $primaryKey = 'id_bukti_peminjaman';
    protected $fillable = [
        'id_pengajuan_peminjaman',
        'no_invoice',
        'no_kontrak',
        'nama_client',
        'nilai_invoice',
        'nilai_pinjaman',
        'nilai_bunga',
        'invoice_date',
        'due_date',
        'dokumen_invoice',
        'dokumen_kontrak',
        'dokumen_so',
        'dokumen_bast',
        'kontrak_date',
        'dokumen_lainnya',
        'nama_barang',
    ];
}
