<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianPinjamanFinlog extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pengembalian_pinjaman_finlog';
    protected $primaryKey = 'id_pengembalian_pinjaman_finlog';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pinjaman_finlog',
        'id_cells_project',
        'id_project',
        'jumlah_pengembalian',
        'sisa_pinjaman',
        'sisa_bagi_hasil',
        'total_sisa_pinjaman',
        'tanggal_pengembalian',
        'bukti_pembayaran',
        'jatuh_tempo',
        'catatan',
        'status',
    ];

    protected $casts = [
        'jumlah_pengembalian' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
        'sisa_bagi_hasil' => 'decimal:2',
        'total_sisa_pinjaman' => 'decimal:2',
        'tanggal_pengembalian' => 'date',
        'jatuh_tempo' => 'date',
    ];

    /**
     * Relasi ke PeminjamanFinlog
     */
    public function peminjamanFinlog()
    {
        return $this->belongsTo(PeminjamanFinlog::class, 'id_pinjaman_finlog', 'id_peminjaman_finlog');
    }

    /**
     * Relasi ke CellsProject
     */
    public function cellsProject()
    {
        return $this->belongsTo(CellsProject::class, 'id_cells_project', 'id_cells_project');
    }

    /**
     * Relasi ke Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project', 'id_project');
    }
}
