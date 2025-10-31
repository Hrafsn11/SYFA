<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormKerjaInvestor extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'form_kerja_investor';
    protected $primaryKey = 'id_form_kerja_investor';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_debitur',
        'nama_investor',
        'deposito',
        'tanggal_pembayaran',
        'lama_investasi',
        'jumlah_investasi',
        'bagi_hasil',
        'bagi_hasil_keseluruhan',
        'status',
        'alasan_penolakan',
        'bukti_transfer',
        'keterangan_bukti',
        'nomor_kontrak',
        'tanggal_kontrak',
        'catatan_kontrak',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'tanggal_kontrak' => 'date',
        'jumlah_investasi' => 'decimal:2',
        'bagi_hasil' => 'decimal:2',
        'bagi_hasil_keseluruhan' => 'decimal:2',
    ];

 
    public function investor()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }
}
