<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'projects';
    protected $primaryKey = 'id_project';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_cells_project',
        'nama_project',
    ];

    public function cellsProject()
    {
        return $this->belongsTo(CellsProject::class, 'id_cells_project', 'id_cells_project');
    }

    /**
     * Relasi ke PengembalianPinjamanFinlog
     */
    public function pengembalianPinjaman()
    {
        return $this->hasMany(PengembalianPinjamanFinlog::class, 'id_project', 'id_project');
    }

    /**
     * Relasi ke PeminjamanFinlog
     */
    public function peminjamanFinlog()
    {
        return $this->hasMany(PeminjamanFinlog::class, 'nama_project', 'id_project');
    }
}
