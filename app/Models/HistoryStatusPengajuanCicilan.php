<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryStatusPengajuanCicilan extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'history_status_pengajuan_cicilan';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_history_status_cicilan';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_pengajuan_cicilan',
        'status',
        'current_step',
        'date',
        'time',
        'submit_by',
        'approve_by',
        'reject_by',
        'validasi_dokumen',
        'catatan_validasi_dokumen',
        'catatan',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'current_step' => 'integer',
    ];

    /**
     * Get the pengajuan cicilan that this history belongs to.
     */
    public function pengajuanCicilan(): BelongsTo
    {
        return $this->belongsTo(PengajuanCicilan::class, 'id_pengajuan_cicilan', 'id_pengajuan_cicilan');
    }

    /**
     * Get the user who submitted.
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submit_by', 'id');
    }

    /**
     * Get the user who approved.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approve_by', 'id');
    }

    /**
     * Get the user who rejected.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reject_by', 'id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by step.
     */
    public function scopeByStep($query, $step)
    {
        return $query->where('current_step', $step);
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
