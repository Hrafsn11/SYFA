<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLaporan extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'detail_laporan';
    protected $primaryKey = 'id_detail_laporan';

    public function nilai_laporan()
    {
        return $this->hasMany(NilaiLaporan::class, 'id_detail_laporan', 'id_detail_laporan');
    }
}
