<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryStatusPengajuanInvestasiFinlog extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'history_status_pengajuan_investasi_finlog';
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_history_status_pengajuan_investasi_finlog';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_pengajuan_investasi_finlog',
        'submit_step1_by',
        'date',
        'validasi_pengajuan',
        'persetujuan_ceo_finlog',
        'catatan_penolakan',
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
     * Get the pengajuan investasi finlog that this history belongs to.
     */
    public function pengajuanInvestasiFinlog(): BelongsTo
    {
        return $this->belongsTo(PengajuanInvestasiFinlog::class, 'id_pengajuan_investasi_finlog', 'id_pengajuan_investasi_finlog');
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
     * Scope to filter by validasi pengajuan status.
     */
    public function scopeByValidasiPengajuan($query, $validasi)
    {
        return $query->where('validasi_pengajuan', $validasi);
    }

    /**
     * Scope to filter by persetujuan CEO Finlog status.
     */
    public function scopeByPersetujuanCeoFinlog($query, $persetujuan)
    {
        return $query->where('persetujuan_ceo_finlog', $persetujuan);
    }

    /**
     * Scope to filter by current step.
     */
    public function scopeByStep($query, $step)
    {
        return $query->where('current_step', $step);
    }
}
