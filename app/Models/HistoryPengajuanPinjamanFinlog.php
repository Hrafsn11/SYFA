<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryPengajuanPinjamanFinlog extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'history_pengajuan_pinjaman_finlog';
    protected $primaryKey = 'id_history_pengajuan_pinjaman_finlog';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_peminjaman_finlog',
        'submit_step1_by',
        'reject_by',
        'approve_by',
        'date',
        'time',
        'bagi_hasil_disetujui',
        'catatan_penolakan',
        'status',
        'current_step',
    ];

    protected $casts = [
        'date' => 'date',
        'bagi_hasil_disetujui' => 'decimal:2',
        'current_step' => 'integer',
    ];

    /**
     * Get the peminjaman finlog that this history belongs to.
     */
    public function peminjamanFinlog(): BelongsTo
    {
        return $this->belongsTo(PeminjamanFinlog::class, 'id_peminjaman_finlog', 'id_peminjaman_finlog');
    }

    /**
     * Get the user who submitted step 1.
     */
    public function submitBy(): BelongsTo
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
     * Scope to filter by current step.
     */
    public function scopeByStep($query, $step)
    {
        return $query->where('current_step', $step);
    }
}
