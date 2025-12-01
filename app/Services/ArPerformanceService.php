<?php

namespace App\Services;

use App\Models\ReportPengembalian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ArPerformanceService
{
    protected $cacheEnabled = false; // Disabled for real-time updates
    protected $cacheTTL = 3600; 

    public function getArPerformanceData($tahun = null, $useCache = false) // Default to false for fresh data
    {
        if (!$this->cacheEnabled || !$useCache) {
            return $this->calculateArPerformance($tahun);
        }

        $cacheKey = $this->getCacheKey($tahun);
        
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($tahun) {
            return $this->calculateArPerformance($tahun);
        });
    }

    protected function calculateArPerformance($tahun = null)
    {
        $payments = $this->getPaymentsData($tahun);
        
        if ($payments->isEmpty()) {
            return collect([]);
        }

        return $payments
            ->groupBy('id_debitur')
            ->map(function ($debiturPayments) {
                return $this->aggregateDebiturData($debiturPayments);
            })
            ->values()
            ->sortBy('nama_debitur');
    }

    protected function getPaymentsData($tahun = null)
    {
        $query = DB::table('report_pengembalian as rp')
            ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->leftJoin('bukti_peminjaman as bp', function($join) {
                $join->on('pm.id_pengajuan_peminjaman', '=', 'bp.id_pengajuan_peminjaman')
                     ->on('rp.nomor_invoice', '=', 'bp.no_invoice');
            })
            ->select([
                'rp.id_report_pengembalian',
                'rp.nomor_invoice',
                'rp.due_date',
                'rp.nilai_total_pengembalian',
                'rp.created_at as tanggal_pembayaran',
                'pm.nomor_peminjaman',
                'pm.no_kontrak',
                'pm.id_debitur',
                'pp.nama_perusahaan as nama_debitur',
                DB::raw('COALESCE(bp.no_kontrak, pm.no_kontrak) as kontrak_display')
            ])
            ->when($tahun, fn($q, $year) => $q->whereYear('rp.created_at', $year))
            ->orderBy('pp.nama_perusahaan')
            ->orderBy('rp.created_at');

        return $query->get();
    }

    protected function aggregateDebiturData($payments)
    {
        $firstPayment = $payments->first();
        $result = [
            'id_debitur' => $firstPayment->id_debitur,
            'nama_debitur' => $firstPayment->nama_debitur,
        ];

        $categories = $this->initializeCategories();

        foreach ($payments as $payment) {
            $tanggalPembayaran = Carbon::parse($payment->tanggal_pembayaran);
            $dueDate = Carbon::parse($payment->due_date);
            
            $category = $this->calculateAgingCategory($tanggalPembayaran, $dueDate);
            $daysLate = $this->calculateDaysLate($tanggalPembayaran, $dueDate);
            $nilai = (float)($payment->nilai_total_pengembalian ?? 0);

            $categories[$category]['total'] += $nilai;
            $categories[$category]['count']++;
            $categories[$category]['transactions'][] = [
                'id' => $payment->id_report_pengembalian,
                'nomor_kontrak' => $payment->kontrak_display ?? '-',
                'nomor_invoice' => $payment->nomor_invoice ?? '-',
                'nilai' => $nilai,
                'due_date' => $payment->due_date,
                'tanggal_pembayaran' => $payment->tanggal_pembayaran,
                'hari_keterlambatan' => $daysLate,
            ];
        }

        return array_merge($result, $categories);
    }

    protected function initializeCategories()
    {
        return [
            'belum_jatuh_tempo' => ['total' => 0, 'count' => 0, 'transactions' => []],
            'del_1_30' => ['total' => 0, 'count' => 0, 'transactions' => []],
            'del_31_60' => ['total' => 0, 'count' => 0, 'transactions' => []],
            'del_61_90' => ['total' => 0, 'count' => 0, 'transactions' => []],
            'npl_91_179' => ['total' => 0, 'count' => 0, 'transactions' => []],
            'writeoff_180' => ['total' => 0, 'count' => 0, 'transactions' => []],
        ];
    }

    public function calculateAgingCategory(Carbon $tanggalPembayaran, Carbon $dueDate): string
    {
        // Early return for belum jatuh tempo
        if ($tanggalPembayaran->lte($dueDate)) {
            return 'belum_jatuh_tempo';
        }

        $daysLate = $tanggalPembayaran->diffInDays($dueDate);

        return match(true) {
            $daysLate <= 30 => 'del_1_30',
            $daysLate <= 60 => 'del_31_60',
            $daysLate <= 90 => 'del_61_90',
            $daysLate <= 179 => 'npl_91_179',
            default => 'writeoff_180'
        };
    }

    public function calculateDaysLate(Carbon $tanggalPembayaran, Carbon $dueDate): int
    {
        return $tanggalPembayaran->lte($dueDate) 
            ? 0 
            : $tanggalPembayaran->diffInDays($dueDate);
    }

    public function getTransactionsByCategory($debiturId, $category, $tahun = null): array
    {
        $data = $this->getArPerformanceData($tahun, false); // Always fetch fresh data
        
        $debitur = $data->firstWhere('id_debitur', $debiturId);
        
        // Early return if debitur not found or category doesn't exist
        if (!$debitur || !isset($debitur[$category])) {
            return [];
        }

        return $debitur[$category]['transactions'] ?? [];
    }

    public function clearCache($tahun = null)
    {
        if ($tahun) {
            Cache::forget($this->getCacheKey($tahun));
        } else {
            Cache::flush();
        }
    }

    protected function getCacheKey($tahun = null)
    {
        $tahun = $tahun ?? 'all';
        return "ar_performance_data_{$tahun}";
    }

    public function getCategoryLabel($category)
    {
        $labels = [
            'belum_jatuh_tempo' => 'Belum Jatuh Tempo',
            'del_1_30' => 'DEL (1-30)',
            'del_31_60' => 'DEL (31-60)',
            'del_61_90' => 'DEL (61-90)',
            'npl_91_179' => 'NPL (91-179)',
            'writeoff_180' => 'WriteOff (>180)',
        ];

        return $labels[$category] ?? $category;
    }
}
