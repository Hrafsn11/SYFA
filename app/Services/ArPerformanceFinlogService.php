<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ArPerformanceFinlogService
{
    protected $cacheEnabled = false; // Disabled for real-time updates
    protected $cacheTTL = 3600;

    public function getArPerformanceData($tahun = null, $bulan = null, $useCache = false)
    {
        if (!$this->cacheEnabled || !$useCache) {
            return $this->calculateArPerformance($tahun, $bulan);
        }

        $cacheKey = $this->getCacheKey($tahun, $bulan);
        
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($tahun, $bulan) {
            return $this->calculateArPerformance($tahun, $bulan);
        });
    }

    protected function calculateArPerformance($tahun = null, $bulan = null)
    {
        $payments = $this->getPaymentsData($tahun, $bulan);
        
        if ($payments->isEmpty()) {
            return collect([]);
        }

        $result = $payments
            ->groupBy('id_debitur')
            ->map(function ($debiturPayments) {
                return $this->aggregateDebiturData($debiturPayments);
            })
            ->values()
            ->sortBy('nama_debitur');
        
        // Ensure we always return a Collection
        return collect($result->all());
    }

    protected function getPaymentsData($tahun = null, $bulan = null)
    {
        $query = DB::table('pengembalian_pinjaman_finlog as ppf')
            ->join('peminjaman_finlog as pf', 
                'ppf.id_pinjaman_finlog', '=', 'pf.id_peminjaman_finlog')
            ->join('master_debitur_dan_investor as mdi',
                'pf.id_debitur', '=', 'mdi.id_debitur')
            ->select([
                'ppf.id_pengembalian_pinjaman_finlog as id_report_pengembalian',
                
                // Field mapping untuk compatibility dengan view
                'pf.nomor_peminjaman as nomor_invoice',
                'pf.nomor_peminjaman as nomor_peminjaman',
                'pf.nomor_peminjaman as kontrak_display',
                
                // Core fields
                'ppf.jumlah_pengembalian as nilai_total_pengembalian',
                'ppf.tanggal_pengembalian as tanggal_pembayaran',
                'ppf.jatuh_tempo as due_date',
                
                // Debitur info
                'pf.id_debitur',
                'mdi.nama as nama_debitur',
            ])
            ->when($tahun, fn($q, $year) => 
                $q->whereYear('ppf.tanggal_pengembalian', $year))
            ->when($bulan, fn($q, $month) => 
                $q->whereMonth('ppf.tanggal_pengembalian', $month))
            ->orderBy('mdi.nama')
            ->orderBy('ppf.tanggal_pengembalian');

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

    public function getTransactionsByCategory($debiturId, $category, $tahun = null, $bulan = null): array
    {
        $data = $this->getArPerformanceData($tahun, $bulan, false);
        
        $debitur = $data->firstWhere('id_debitur', $debiturId);
        
        if (!$debitur || !isset($debitur[$category])) {
            return [];
        }

        return $debitur[$category]['transactions'] ?? [];
    }

    public function clearCache($tahun = null, $bulan = null)
    {
        if ($tahun || $bulan) {
            Cache::forget($this->getCacheKey($tahun, $bulan));
        } else {
            Cache::flush();
        }
    }

    protected function getCacheKey($tahun = null, $bulan = null)
    {
        $tahun = $tahun ?? 'all';
        $bulan = $bulan ?? 'all';
        return "ar_performance_finlog_data_{$tahun}_{$bulan}";
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
