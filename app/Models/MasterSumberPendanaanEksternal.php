<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSumberPendanaanEksternal extends Model
{
    use HasFactory;

    protected $table = 'master_sumber_pendanaan_eksternal';
    protected $primaryKey = 'id_instansi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_instansi',
        'persentase_bagi_hasil'
    ];
}
