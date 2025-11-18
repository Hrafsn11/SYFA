<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterDebiturDanInvestor extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'master_debitur_dan_investor';

    protected $primaryKey = 'id_debitur';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'id_kol',
        'nama',
        'alamat',
        'email',
        'no_telepon',
        'status',
        'deposito',
        'nama_ceo',
        'nama_bank',
        'no_rek',
        'npwp',
        'flagging',
        'tanda_tangan',
    ];

    protected $casts = [
        'flagging' => 'string',
        'status' => 'string',
        'deposito' => 'string',
    ];

    public function kol()
    {
        return $this->belongsTo(MasterKol::class, 'id_kol', 'id_kol');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Debitur($query)
    {
        return $query->where('flagging', 'tidak');
    }

    public function Investor($query)
    {
        return $query->where('flagging', 'ya');
    }
}
