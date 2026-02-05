<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiLaporan extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'nilai_laporan';
    protected $primaryKey = 'id_nilai_laporan';
}
