<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKol extends Model
{
    use HasFactory;

    protected $table = 'master_kol';
    protected $primaryKey = 'id_kol';

    protected $fillable = [
        'kol','persentase_pencairan','jmlh_hari_keterlambatan'
    ];

    protected $appends = ['persentase_label', 'tanggal_tenggat_label'];

    protected $casts = [
        'persentase_pencairan' => 'float',
        'jmlh_hari_keterlambatan' => 'integer',
    ];

    public function getPersentaseLabelAttribute()
    {
        if (! isset($this->persentase_pencairan)) {
            return '';
        }
        return rtrim(rtrim(number_format($this->persentase_pencairan, 2, '.', ''), '0'), '.') . '%';
    }

    public function getTanggalTenggatLabelAttribute()
    {
        $d = intval($this->jmlh_hari_keterlambatan ?? 0);
        if ($d <= 0) {
            return '0 Hari';
        }
        if ($d <= 29) {
            return '1-29 Hari';
        }
        if ($d <= 59) {
            return '30–59 Hari';
        }
        if ($d <= 179) {
            return '60–179 Hari';
        }
        return '≥180 Hari';
    }
}
