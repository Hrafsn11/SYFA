<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryStatusPengajuanInvestor extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'history_status_pengajuan_investor';
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_history_status_pengajuan_investor';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_pengajuan_investasi',
        'submit_step1_by',
        'date',
        'validasi_bagi_hasil',
        'catatan_validasi_dokumen_ditolak',
        'status',
        'reject_by',
        'approve_by',
        'time',
        'current_step',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'current_step' => 'integer',
    ];

    /**
     * Get the pengajuan investasi that this history belongs to.
     */
    public function pengajuanInvestasi(): BelongsTo
    {
        return $this->belongsTo(PengajuanInvestasi::class, 'id_pengajuan_investasi', 'id_pengajuan_investasi');
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
    public function scopeByValidasiBagiHasil($query, $validasi)
    {
        return $query->where('validasi_bagi_hasil', $validasi);
    }

    /**
     * Scope to get approved documents.
     */
    public function scopeApproved($query)
    {
        return $query->where('validasi_bagi_hasil', 'disetujui');
    }

    /**
     * Scope to get rejected documents.
     */
    public function scopeRejected($query)
    {
        return $query->where('validasi_bagi_hasil', 'ditolak');
    }
}
