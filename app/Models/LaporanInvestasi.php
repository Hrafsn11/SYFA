<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanInvestasi extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'laporan_investasi';
    protected $primaryKey = 'id_laporan_investasi';
}
