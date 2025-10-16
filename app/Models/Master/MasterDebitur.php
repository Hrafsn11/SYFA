<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDebitur extends Model
{
    use HasFactory;

    protected $table = 'master_debitur';
    protected $primaryKey = 'id_debitur';

    protected $fillable = [
        'id_kol','id_instansi','nama_debitur','alamat','email','nama_ceo','nama_bank','no_rek'
    ];

    public function kol()
    {
        return $this->belongsTo(MasterKol::class, 'id_kol', 'id_kol');
    }

    public function sumberPendanaan()
    {
        return $this->belongsTo(MasterSumberPendanaanEksternal::class, 'id_instansi', 'id_instansi');
    }
}
