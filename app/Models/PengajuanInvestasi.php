<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PengajuanInvestasi extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'pengajuan_investasi';
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_pengajuan_investasi';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_debitur_dan_investor',
        'nama_investor',
        'deposito',
        'tanggal_investasi',
        'lama_investasi',
        'jumlah_investasi',
        'bagi_hasil_pertahun',
        'nominal_bagi_hasil_yang_didapatkan',
        'upload_bukti_transfer',
        'status',
        'current_step',
        'created_by',
        'updated_by',
        'nomor_kontrak',
        'sisa_pokok',
        'sisa_bagi_hasil',
        'total_disalurkan',
        'total_kembali_dari_penyaluran',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tanggal_investasi' => 'date',
        'jumlah_investasi' => 'decimal:2',
        'nominal_bagi_hasil_yang_didapatkan' => 'decimal:2',
        'lama_investasi' => 'integer',
        'bagi_hasil_pertahun' => 'integer',
        'current_step' => 'integer',
        'sisa_pokok' => 'decimal:2',
        'sisa_bagi_hasil' => 'decimal:2',
        'total_disalurkan' => 'decimal:2',
        'total_kembali_dari_penyaluran' => 'decimal:2',
    ];

       protected static function boot()
    {
        parent::boot();

        static::creating(function ($investasi): void {
            if (!$investasi->sisa_pokok || $investasi->sisa_pokok == 0) {
                $investasi->sisa_pokok = $investasi->jumlah_investasi ?? 0;
            }
            
            if (!$investasi->sisa_bagi_hasil || $investasi->sisa_bagi_hasil == 0) {
                $investasi->sisa_bagi_hasil = $investasi->nominal_bagi_hasil_yang_didapatkan ?? 0;
            }
        });
    }

    /**
     * Get the investor (debitur dan investor) that owns the pengajuan.
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur_dan_investor', 'id_debitur');
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
     * Get all history records for this pengajuan.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(HistoryStatusPengajuanInvestor::class, 'id_pengajuan_investasi', 'id_pengajuan_investasi');
    }

    /**
     * Get the latest history record.
     */
    public function latestHistory()
    {
        return $this->hasOne(HistoryStatusPengajuanInvestor::class, 'id_pengajuan_investasi', 'id_pengajuan_investasi')
                    ->orderBy('id_history_status_pengajuan_investor', 'desc')
                    ->limit(1);
    }

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
        return $query->where('status', 'Submitted');
    }

    /**
     * Scope to get approved records.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope to get rejected records.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

   
    public function penyaluranDeposito(): HasMany
    {
        return $this->hasMany(PenyaluranDeposito::class, 'id_pengajuan_investasi', 'id_pengajuan_investasi');
    }

    /**
     * Get all pengembalian investasi records for this pengajuan.
     */
    public function pengembalianInvestasi(): HasMany
    {
        return $this->hasMany(PengembalianInvestasi::class, 'id_pengajuan_investasi', 'id_pengajuan_investasi');
    }

   
    public function scopeWithSisaDana($query)
    {
        if (!str_contains($query->toSql(), 'pengajuan_investasi')) {
            $query->from('pengajuan_investasi');
        }
        
        return $query
            ->leftJoin(
                DB::raw('(
                    SELECT 
                        id_pengajuan_investasi as pd_id_pengajuan_investasi, 
                        SUM(nominal_yang_disalurkan) as total_disalurkan 
                    FROM penyaluran_deposito 
                    GROUP BY id_pengajuan_investasi
                ) as pd_aggregated'),
                'pengajuan_investasi.id_pengajuan_investasi', 
                '=', 
                'pd_aggregated.pd_id_pengajuan_investasi'
            )
            ->select([
                'pengajuan_investasi.*',
                DB::raw('COALESCE(pd_aggregated.total_disalurkan, 0) as total_disalurkan'),
                DB::raw('(pengajuan_investasi.jumlah_investasi - COALESCE(pd_aggregated.total_disalurkan, 0)) as sisa_dana')
            ]);
    }

    
    public function scopeHasSisaDana($query, $minimum = 0)
    {
        return $query->havingRaw('sisa_dana > ?', [$minimum]);
    }

   
    public function getSisaDana(): float
    {
        $totalDisalurkan = $this->penyaluranDeposito()
            ->sum('nominal_yang_disalurkan');
        
        return floatval($this->jumlah_investasi) - floatval($totalDisalurkan);
    }

    /**
     * Get sisa dana yang masih di perusahaan (belum balik)
     * 
     * Formula: total_disalurkan - total_kembali_dari_penyaluran
     * 
     * @return float
     */
    public function getSisaDanaDiPerusahaanAttribute(): float
    {
        return max(0, floatval($this->total_disalurkan ?? 0) - floatval($this->total_kembali_dari_penyaluran ?? 0));
    }

    /**
     * Get dana tersedia yang bisa dikembalikan ke investor
     * 
     * Formula: sisa_pokok - sisa_dana_di_perusahaan
     * 
     * Logic:
     * - Dana internal (tidak disalurkan) bisa langsung dikembalikan
     * - Dana yang disalurkan harus nunggu perusahaan bayar dulu
     * 
     * @return float
     */
    public function getDanaTersediaAttribute(): float
    {
        $sisaDiPerusahaan = $this->sisa_dana_di_perusahaan; // Pakai accessor
        return max(0, floatval($this->sisa_pokok ?? 0) - $sisaDiPerusahaan);
    }

    /**
     * Get dana tersedia formatted (Rupiah)
     * 
     * @return string
     */
    public function getDanaTersediaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->dana_tersedia, 0, ',', '.');
    }
}
