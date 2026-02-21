<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CellsProject extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'cells_projects';
    protected $primaryKey = 'id_cells_project';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_cells_bisnis',
        'nama_pic',
        'alamat',
        'deskripsi_bidang',
        'tanda_tangan_pic',
        'profile_pict',
    ];

    public function pengajuanTagihanPinjaman()
    {
        return $this->hasMany(PengajuanTagihanPinjaman::class, 'id_cells_project');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'id_cells_project', 'id_cells_project');
    }

    /**
     * Relasi ke PengembalianPinjamanFinlog
     */
    public function pengembalianPinjaman()
    {
        return $this->hasMany(PengembalianPinjamanFinlog::class, 'id_cells_project', 'id_cells_project');
    }
}
