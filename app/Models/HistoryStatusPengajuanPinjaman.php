<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryStatusPengajuanPinjaman extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'history_status_pengajuan_pinjaman';
    
    protected $primaryKey = 'id_history_status_pengajuan_pinjaman';

    protected $fillable = [
        'id_pengajuan_peminjaman',
        'id_config_matrix_peminjaman',
        'submit_step1_by',
        'date',
        'validasi_dokumen',
        'catatan_validasi_dokumen_ditolak',
        'nominal_yang_disetujui',
        'catatan_validasi_dokumen_disetujui',
        'tanggal_pencairan',
        'status',
        'reject_by',
        'approve_by',
        'current_step',
        'devisasi',
    ];


    /**
     * Get the pengajuan peminjaman that this history belongs to.
     */
    public function pengajuanPeminjaman(): BelongsTo
    {
        return $this->belongsTo(PengajuanPeminjaman::class, 'id_pengajuan_peminjaman', 'id_pengajuan_peminjaman');
    }

    /**
     * Get the config matrix that this history belongs to.
     */
    public function configMatrix(): BelongsTo
    {
        return $this->belongsTo(ConfigMatrixPinjaman::class, 'id_config_matrix_peminjaman', 'id_matrix_pinjaman');
    }

    /**
     * Get the user who submitted step 1.
     */
    public function submitBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submit_step1_by', 'id');
    }

    /**
     * Get the user who submitted (alias for submitBy).
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submit_step1_by', 'id');
    }

    /**
     * Get the user who rejected the application.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reject_by', 'id');
    }

    /**
     * Get the user who approved the application.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approve_by', 'id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by validation status.
     */
    public function scopeByValidasiDokumen($query, $validasi)
    {
        return $query->where('validasi_dokumen', $validasi);
    }

    /**
     * Scope to get approved documents.
     */
    public function scopeApproved($query)
    {
        return $query->where('validasi_dokumen', 'disetujui');
    }

    /**
     * Scope to get rejected documents.
     */
    public function scopeRejected($query)
    {
        return $query->where('validasi_dokumen', 'ditolak');
    }
}
