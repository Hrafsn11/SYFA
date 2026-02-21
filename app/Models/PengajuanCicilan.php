<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanCicilan extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'pengajuan_cicilan';

    protected $primaryKey = 'id_pengajuan_cicilan';

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
        'current_step',
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
        'current_step' => 'integer',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the debitur that owns the pengajuan.
     */
    public function debitur()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }

    /**
     * Get the pengajuan peminjaman that this cicilan belongs to.
     */
    public function pengajuanPeminjaman()
    {
        return $this->belongsTo(PengajuanPeminjaman::class, 'id_pengajuan_peminjaman', 'id_pengajuan_peminjaman');
    }

    /**
     * Alias for pengajuanPeminjaman.
     */
    public function peminjaman()
    {
        return $this->pengajuanPeminjaman();
    }

    /**
     * Get all history records for this pengajuan.
     */
    public function histories()
    {
        return $this->hasMany(HistoryStatusPengajuanCicilan::class, 'id_pengajuan_cicilan', 'id_pengajuan_cicilan');
    }

    /**
     * Get the latest history record.
     */
    public function latestHistory()
    {
        return $this->hasOne(HistoryStatusPengajuanCicilan::class, 'id_pengajuan_cicilan', 'id_pengajuan_cicilan')
            ->orderBy('id_history_status_cicilan', 'desc')
            ->limit(1);
    }

    /**
     * Get the evaluasi for this pengajuan.
     */
    public function evaluasi()
    {
        return $this->hasOne(EvaluasiPengajuanCicilan::class, 'id_pengajuan_cicilan', 'id_pengajuan_cicilan');
    }

    /**
     * Get the penyesuaian cicilan for this pengajuan.
     */
    public function penyesuaianCicilan()
    {
        return $this->hasOne(PenyesuaianCicilan::class, 'id_pengajuan_cicilan', 'id_pengajuan_cicilan');
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get draft records.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }

    /**
     * Scope to get submitted records.
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', '!=', 'Draft');
    }

    /**
     * Scope to filter by current step.
     */
    public function scopeByStep($query, $step)
    {
        return $query->where('current_step', $step);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if pengajuan is in draft status.
     */
    public function isDraft(): bool
    {
        return $this->status === 'Draft';
    }

    /**
     * Check if pengajuan is rejected.
     */
    public function isDitolak(): bool
    {
        return str_contains($this->status, 'Ditolak');
    }

    /**
     * Check if pengajuan is completed.
     */
    public function isSelesai(): bool
    {
        return $this->status === 'Selesai' || $this->current_step === 5;
    }

    /**
     * Check if pengajuan can be edited.
     */
    public function canBeEdited(): bool
    {
        // Can edit if Draft or Ditolak at step 1
        return $this->status === 'Draft' || ($this->isDitolak() && $this->current_step === 1);
    }

    /**
     * Get status badge class for display.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'Draft' => 'warning',
            'Submit Dokumen' => 'info',
            'Dokumen Tervalidasi' => 'success',
            'Disetujui CEO SKI' => 'success',
            'Disetujui Direktur SKI' => 'success',
            'Selesai' => 'primary',
            'Ditolak' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get current step name.
     */
    public function getCurrentStepName(): string
    {
        return match ($this->current_step) {
            1 => 'Pengajuan Cicilan',
            2 => 'Validasi Dokumen',
            3 => 'Persetujuan CEO',
            4 => 'Persetujuan Direktur',
            5 => 'Selesai',
            default => 'Unknown'
        };
    }
}
