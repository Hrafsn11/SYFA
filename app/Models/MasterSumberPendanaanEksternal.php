<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterSumberPendanaanEksternal extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'master_sumber_pendanaan_eksternal';
    protected $primaryKey = 'id_instansi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_instansi',
        'persentase_bagi_hasil'
    ];
}
