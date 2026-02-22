<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PengajuanInvestasiFinlog extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'pengajuan_investasi_finlog';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_pengajuan_investasi_finlog';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_debitur_dan_investor',
        'id_cells_project',
        'nama_investor',
        'tanggal_investasi',
        'lama_investasi',
        'tanggal_berakhir_investasi',
        'nominal_investasi',
        'persentase_bunga',
        'nominal_bagi_hasil_yang_didapat',
        'sisa_pokok',
        'sisa_bunga',
        'upload_bukti_transfer',
        'nomor_kontrak',
        'nama_pic_kontrak',
        'status',
        'current_step',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tanggal_investasi' => 'date',
        'tanggal_berakhir_investasi' => 'date',
        'nominal_investasi' => 'decimal:2',
        'persentase_bunga' => 'decimal:2',
        'nominal_bagi_hasil_yang_didapat' => 'decimal:2',
        'sisa_pokok' => 'decimal:2',
        'sisa_bunga' => 'decimal:2',
        'lama_investasi' => 'integer',
        'current_step' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->created_by) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    /**
     * Get the investor that owns the pengajuan.
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur_dan_investor', 'id_debitur');
    }

    /**
     * Get the project that owns the pengajuan.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(CellsProject::class, 'id_cells_project', 'id_cells_project');
    }

    /**
     * Get the user who created the record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user who last updated the record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Get the histories for the pengajuan.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(HistoryStatusPengajuanInvestasiFinlog::class, 'id_pengajuan_investasi_finlog', 'id_pengajuan_investasi_finlog');
    }
}
