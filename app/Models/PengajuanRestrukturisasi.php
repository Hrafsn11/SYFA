<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanRestrukturisasi extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'pengajuan_restrukturisasi';

    protected $primaryKey = 'id_pengajuan_restrukturisasi';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_debitur',
        'id_pengajuan_peminjaman',
        'nama_perusahaan',
        'npwp',
        'alamat_kantor',
        'nomor_telepon',
        'nama_pic',
        'jabatan_pic',
        'nomor_kontrak_pembiayaan',
        'tanggal_akad',
        'jenis_pembiayaan',
        'jumlah_plafon_awal',
        'sisa_pokok_belum_dibayar',
        'tunggakan_margin_bunga',
        'jatuh_tempo_terakhir',
        'status_dpd',
        'alasan_restrukturisasi',
        'jenis_restrukturisasi',
        'jenis_restrukturisasi_lainnya',
        'rencana_pemulihan_usaha',
        'dokumen_ktp_pic',
        'dokumen_npwp_perusahaan',
        'dokumen_laporan_keuangan',
        'dokumen_arus_kas',
        'dokumen_kondisi_eksternal',
        'dokumen_kontrak_pembiayaan',
        'dokumen_lainnya',
        'dokumen_tanda_tangan',
        'tempat',
        'tanggal',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_akad' => 'date',
        'jatuh_tempo_terakhir' => 'date',
        'tanggal' => 'date',
        'jumlah_plafon_awal' => 'decimal:2',
        'sisa_pokok_belum_dibayar' => 'decimal:2',
        'tunggakan_pokok' => 'decimal:2',
        'tunggakan_margin_bunga' => 'decimal:2',
        'jenis_restrukturisasi' => 'array',
    ];

    // Relationships
    public function debitur()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }

    public function pengajuanPeminjaman()
    {
        return $this->belongsTo(PengajuanPeminjaman::class, 'id_pengajuan_peminjaman', 'id_pengajuan_peminjaman');
    }

    public function peminjaman()
    {
        return $this->pengajuanPeminjaman();
    }
}
