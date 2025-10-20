<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDebiturDanInvestor extends Model
{
    use HasFactory;

    protected $table = 'master_debitur_dan_investor';
    protected $primaryKey = 'id_debitur';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_kol',
        'nama_debitur',
        'alamat',
        'email',
        'nama_ceo',
        'nama_bank',
        'no_rek',
        'flagging'
    ];

    public function kol()
    {
        return $this->belongsTo(MasterKol::class, 'id_kol', 'id_kol');
    }
}
