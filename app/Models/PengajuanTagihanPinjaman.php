<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanTagihanPinjaman extends Model
{
    use HasFactory, HasUlids;
    protected $table = 'pengajuan_tagihan_pinjaman';
    protected $primaryKey = 'id_pengajuan_peminjaman';
    
    protected $fillable = [
        'nomor_peminjaman',
        'id_debitur',
        'sumber_pembiayaan',
        'id_instansi',
        'nama_bank',
        'no_rekening',
        'nama_rekening',
        'lampiran_sid',
        'nilai_kol',
        'tujuan_pembiayaan',
        'jenis_pembiayaan',
        'total_pinjaman',
        'sisa_bayar_pokok',
        'nominal_pengajuan_awal',
        'harapan_tanggal_pencairan',
        'tanggal_jatuh_tempo',
        'total_bunga',
        'sisa_bunga',
        'jumlah_bulan_keterlambatan',
        'denda_keterlambatan',
        'total_bunga_saat_ini',
        'last_penalty_calculation',
        'lama_pemakaian',
        'last_lama_pemakaian_update',
        'rencana_tgl_pembayaran',
        'pembayaran_total',
        'catatan_lainnya',
        'tenor_pembayaran',
        'persentase_bunga',
        'pps',
        's_finance',
        'yang_harus_dibayarkan',
        'total_nominal_yang_dialihkan',
        'status',
        'created_by',
        'updated_by',
        'upload_bukti_transfer',
        'no_kontrak',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'harapan_tanggal_pencairan' => 'date',
        'total_pinjaman' => 'decimal:2',
        'sisa_bayar_pokok' => 'decimal:2',
        'total_bunga' => 'decimal:2',
        'sisa_bunga' => 'decimal:2',
        'jumlah_bulan_keterlambatan' => 'integer',
        'denda_keterlambatan' => 'decimal:2',
        'total_bunga_saat_ini' => 'decimal:2',
        'last_penalty_calculation' => 'datetime',
        'lama_pemakaian' => 'integer',
        'last_lama_pemakaian_update' => 'datetime',
        'yang_harus_dibayarkan' => 'decimal:2',
        'pps' => 'decimal:2',
        's_finance' => 'decimal:2',
        'pembayaran_total' => 'decimal:2',
        'tenor_pembayaran' => 'integer',
        'persentase_bunga' => 'decimal:2',
    ];

    public function invoices()
    {
        return $this->hasMany(BuktiPeminjaman::class, 'id_pengajuan_peminjaman', 'id_pengajuan_peminjaman');
    }

    public function debitur()
    {
        return $this->belongsTo(MasterDebiturDanInvestor::class, 'id_debitur', 'id_debitur');
    }

    public function instansi()
    {
        return $this->belongsTo(MasterSumberPendanaanEksternal::class, 'id_instansi', 'id_instansi');
    }

    public function buktiPeminjaman()
    {
        return $this->hasMany(BuktiPeminjaman::class, 'id_pengajuan_peminjaman', 'id_pengajuan_peminjaman');
    }

    public function historyStatus()
    {
        return $this->hasMany(HistoryStatusPengajuanPinjaman::class, 'id_pengajuan_peminjaman', 'id_pengajuan_peminjaman');
    }
}
