<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluasiPengajuanCicilan extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'evaluasi_pengajuan_cicilan';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_evaluasi_cicilan';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_pengajuan_cicilan',
        'rekomendasi',
        'justifikasi_rekomendasi',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'evaluated_at' => 'datetime',
    ];

    /**
     * Get the pengajuan cicilan that this evaluasi belongs to.
     */
    public function pengajuanCicilan(): BelongsTo
    {
        return $this->belongsTo(PengajuanCicilan::class, 'id_pengajuan_cicilan', 'id_pengajuan_cicilan');
    }

    /**
     * Get kelengkapan dokumen records (Section A).
     */
    public function kelengkapanDokumen(): HasMany
    {
        return $this->hasMany(EvaluasiKelengkapanDokumen::class, 'id_evaluasi_cicilan', 'id_evaluasi_cicilan');
    }

    /**
     * Get kelayakan debitur records (Section B).
     */
    public function kelayakanDebitur(): HasMany
    {
        return $this->hasMany(EvaluasiKelayakanDebitur::class, 'id_evaluasi_cicilan', 'id_evaluasi_cicilan');
    }

    /**
     * Get analisa cicilan records (Section C).
     */
    public function analisaCicilan(): HasMany
    {
        return $this->hasMany(EvaluasiAnalisaCicilan::class, 'id_evaluasi_cicilan', 'id_evaluasi_cicilan');
    }

    /**
     * Get persetujuan komite records (Section E).
     */
    public function persetujuanKomite(): HasMany
    {
        return $this->hasMany(PersetujuanKomiteCicilan::class, 'id_evaluasi_cicilan', 'id_evaluasi_cicilan');
    }

    /**
     * Check if all dokumen are complete (all "Ya").
     */
    public function isDokumenLengkap(): bool
    {
        $total = $this->kelengkapanDokumen()->count();
        $lengkap = $this->kelengkapanDokumen()->where('status', 'Ya')->count();

        return $total > 0 && $total === $lengkap;
    }

    /**
     * Check if debitur is eligible (all "Ya").
     */
    public function isDebiturLayak(): bool
    {
        $total = $this->kelayakanDebitur()->count();
        $layak = $this->kelayakanDebitur()->where('status', 'Ya')->count();

        return $total > 0 && $total === $layak;
    }

    /**
     * Get rekomendasi badge class.
     */
    public function getRekomendasiBadgeClass(): string
    {
        return match($this->rekomendasi) {
            'Setuju' => 'success',
            'Tolak' => 'danger',
            'Opsi Lain' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get completion percentage of evaluation.
     */
    public function getCompletionPercentage(): int
    {
        $sections = [
            $this->kelengkapanDokumen()->count() > 0,
            $this->kelayakanDebitur()->count() > 0,
            $this->analisaCicilan()->count() > 0,
            !empty($this->rekomendasi),
            $this->persetujuanKomite()->count() > 0,
        ];

        $completed = count(array_filter($sections));
        return (int) (($completed / 5) * 100);
    }
}
