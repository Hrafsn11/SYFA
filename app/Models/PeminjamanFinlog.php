<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanFinlog extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'peminjaman_finlog';
    protected $primaryKey = 'id_peminjaman_finlog';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_peminjaman',
        'id_debitur',
        'id_cells_project',
        'nama_project',
        'durasi_project',
        'nib_perusahaan',
        'nilai_pinjaman',
        'presentase_bagi_hasil',
        'nilai_bagi_hasil',
        'total_pinjaman',
        'harapan_tanggal_pencairan',
        'top',
        'rencana_tgl_pengembalian',
        'dokumen_mitra',
        'form_new_customer',
        'dokumen_kerja_sama',
        'dokumen_npa',
        'akta_perusahaan',
        'ktp_owner',
        'ktp_pic',
        'surat_izin_usaha',
        'catatan',
        'status',
        'current_step',
        'nomor_kontrak',
        'biaya_administrasi',
        'jaminan',
        'bukti_transfer',
    ];

    protected $casts = [
        'nilai_pinjaman' => 'decimal:2',
        'presentase_bagi_hasil' => 'decimal:2',
        'nilai_bagi_hasil' => 'decimal:2',
        'total_pinjaman' => 'decimal:2',
        'biaya_administrasi' => 'decimal:2',
        'harapan_tanggal_pencairan' => 'date',
        'rencana_tgl_pengembalian' => 'date',
        'durasi_project' => 'integer',
        'top' => 'integer',
        'current_step' => 'integer',
    ];

    public function debitur()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }

    public function cellsProject()
    {
        return $this->belongsTo(CellsProject::class, 'id_cells_project');
    }

    public function histories()
    {
        return $this->hasMany(HistoryPengajuanPinjamanFinlog::class, 'id_peminjaman_finlog', 'id_peminjaman_finlog');
    }

    /**
     * Relasi ke PengembalianPinjamanFinlog
     */
    public function pengembalianPinjaman()
    {
        return $this->hasMany(PengembalianPinjamanFinlog::class, 'id_pinjaman_finlog', 'id_peminjaman_finlog');
    }

    public function latestPengembalian()
    {
        return $this->hasOne(PengembalianPinjamanFinlog::class, 'id_pinjaman_finlog', 'id_peminjaman_finlog')->latestOfMany('created_at');
    }
}
