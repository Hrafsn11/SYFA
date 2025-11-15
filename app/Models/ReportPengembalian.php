<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPengembalian extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'report_pengembalian';

    protected $primaryKey = 'id_report_pengembalian';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id_pengembalian',
        'nomor_peminjaman',
        'nomor_invoice',
        'due_date',
        'hari_keterlambatan',
        'total_bulan_pemakaian',
        'nilai_total_pengembalian',
    ];

    protected $casts = [
        'due_date' => 'date',
        'total_bulan_pemakaian' => 'integer',
        'nilai_total_pengembalian' => 'decimal:2',
    ];

    public function pengembalianPinjaman()
    {
        return $this->belongsTo(PengembalianPinjaman::class, 'id_pengembalian', 'ulid');
    }

    public function pengembalianInvoices()
    {
        return $this->hasManyThrough(
            PengembalianInvoice::class,
            PengembalianPinjaman::class,
            'ulid',
            'id_pengembalian',
            'id_pengembalian',
            'ulid'
        );
    }
}
