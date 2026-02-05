<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SummaryLaporan extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'summary_laporans';
    protected $primaryKey = 'id_summary_laporan';
}
