<?php

namespace App\Services;

use App\Models\MasterKol;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KolHistoryService
{
    public function getKolHistoryByYear($debiturId, $tahun)
    {
        $payments = $this->getPaymentsByYear($debiturId, $tahun);
        
        $firstLoanDate = $this->getFirstLoanDate($debiturId);
        
        $result = [];
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $lastKolBeforeLunas = null;
        $lunasMonth = null;
        
        for ($month = 1; $month <= 12; $month++) {
            $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
            $monthStart = Carbon::create($tahun, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($tahun, $month, 1)->endOfMonth();
            
            if ($firstLoanDate && $firstLoanDate->gt($monthEnd)) {
                $result[$monthKey] = [
                    'bulan' => $bulanNama[$month],
                    'bulan_number' => $month, 
                    'kol' => 0,
                    'kol_label' => 'KOL 0'
                ];
            } else {
                $monthPayments = $payments->filter(function ($payment) use ($month, $tahun) {
                    $paymentDate = Carbon::parse($payment->tanggal_pembayaran);
                    return $paymentDate->year == $tahun && $paymentDate->month == $month;
                });
                
                $isLunasThisMonth = $this->isLoanLunasInMonth($debiturId, $month, $tahun);
                
                if ($isLunasThisMonth && $lunasMonth === null) {
                    $lunasMonth = $month;
                }
                
                $hasActiveLoan = $this->hasActiveLoanInMonth($debiturId, $month, $tahun);
                
                if ($lunasMonth !== null && $hasActiveLoan && $month > $lunasMonth) {
                    $lunasMonth = null;
                    $lastKolBeforeLunas = null;
                }
                if ($lunasMonth !== null && $month > $lunasMonth && !$hasActiveLoan) {
                    $kol = $lastKolBeforeLunas ?? 1;
                    $result[$monthKey] = [
                        'bulan' => $bulanNama[$month],
                        'bulan_number' => $month, 
                        'kol' => $kol,
                        'kol_label' => "KOL {$kol}"
                    ];
                } elseif ($monthPayments->isEmpty()) {
                    if ($hasActiveLoan) {
                        $overdueDays = $this->getMaxOverdueDaysInMonth($debiturId, $month, $tahun);
                        
                        if ($overdueDays > 0) {
                            $kol = $this->daysLateToKol($overdueDays);
                            $result[$monthKey] = [
                                'bulan' => $bulanNama[$month],
                                'bulan_number' => $month, 
                                'kol' => $kol,
                                'kol_label' => "KOL {$kol}"
                            ];
                        } else {
                            $kol = 1;
                            $result[$monthKey] = [
                                'bulan' => $bulanNama[$month],
                                'bulan_number' => $month, 
                                'kol' => $kol,
                                'kol_label' => 'KOL 1'
                            ];
                        }
                        
                        if (!$isLunasThisMonth) {
                            if ($kol > ($lastKolBeforeLunas ?? 0)) {
                                $lastKolBeforeLunas = $kol;
                            }
                        }
                    } else {
                        $result[$monthKey] = [
                            'bulan' => $bulanNama[$month],
                            'bulan_number' => $month, 
                            'kol' => 0,
                            'kol_label' => 'KOL 0'
                        ];
                    }
                } else {
                    $worstKol = $this->calculateWorstKolForMonth($monthPayments);
                    $result[$monthKey] = [
                        'bulan' => $bulanNama[$month],
                        'bulan_number' => $month, 
                        'kol' => $worstKol,
                        'kol_label' => "KOL {$worstKol}"
                    ];
                    
                    if (!$isLunasThisMonth) {
                        if ($worstKol > ($lastKolBeforeLunas ?? 0)) {
                            $lastKolBeforeLunas = $worstKol;
                        }
                    }
                }
                
                if ($isLunasThisMonth) {
                    if ($lastKolBeforeLunas === null) {
                        $lastKolBeforeLunas = $result[$monthKey]['kol'] ?? 1;
                    }
                }
            }
        }
        
        return $result;
    }
    
    protected function getPaymentsByYear($debiturId, $tahun)
    {
        return DB::table('report_pengembalian as rp')
            ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->select([
                'rp.id_report_pengembalian',
                'rp.due_date',
                'rp.created_at as tanggal_pembayaran',
                'rp.hari_keterlambatan',
                'pm.id_debitur',
            ])
            ->where('pm.id_debitur', $debiturId)
            ->whereYear('rp.created_at', $tahun)
            ->orderBy('rp.created_at')
            ->get();
    }
    
    protected function getFirstLoanDate($debiturId)
    {
        $firstLoan = DB::table('pengajuan_peminjaman')
            ->where('id_debitur', $debiturId)
            ->whereNotNull('harapan_tanggal_pencairan')
            ->orderBy('harapan_tanggal_pencairan', 'asc')
            ->first();
            
        if ($firstLoan && $firstLoan->harapan_tanggal_pencairan) {
            return Carbon::parse($firstLoan->harapan_tanggal_pencairan);
        }
        
        $firstPengembalian = DB::table('pengembalian_pinjaman as pp')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->where('pm.id_debitur', $debiturId)
            ->whereNotNull('pp.tanggal_pencairan')
            ->orderBy('pp.tanggal_pencairan', 'asc')
            ->first();
            
        if ($firstPengembalian && $firstPengembalian->tanggal_pencairan) {
            return Carbon::parse($firstPengembalian->tanggal_pencairan);
        }
        
        return null;
    }
    
    protected function hasActiveLoanInMonth($debiturId, $month, $tahun)
    {
        $monthEnd = Carbon::create($tahun, $month, 1)->endOfMonth();
        
        $activeLoan = DB::table('pengembalian_pinjaman as pp')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->where('pm.id_debitur', $debiturId)
            ->whereNotNull('pp.tanggal_pencairan')
            ->where('pp.tanggal_pencairan', '<=', $monthEnd->format('Y-m-d'))
            ->where(function($query) {
                $query->where(function($q) {
                    $q->where('pp.sisa_bayar_pokok', '>', 0)
                      ->orWhere('pp.sisa_bagi_hasil', '>', 0);
                })
                ->orWhere(function($q) {
                    $q->whereNull('pp.status')
                      ->orWhere('pp.status', '!=', 'Lunas');
                });
            })
            ->first();
            
        return $activeLoan !== null;
    }
    
    protected function isLoanLunasInMonth($debiturId, $month, $tahun)
    {
        $monthEnd = Carbon::create($tahun, $month, 1)->endOfMonth();
        
        $lunasLoan = DB::table('pengembalian_pinjaman as pp')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->where('pm.id_debitur', $debiturId)
            ->whereNotNull('pp.tanggal_pencairan')
            ->where('pp.tanggal_pencairan', '<=', $monthEnd->format('Y-m-d'))
            ->where(function($query) use ($monthEnd) {
                $query->where(function($q) use ($monthEnd) {
                    $q->where('pp.status', 'Lunas')
                      ->where(function($subQ) use ($monthEnd) {
                          $subQ->whereNotNull('pp.updated_at')
                               ->where('pp.updated_at', '<=', $monthEnd->format('Y-m-d 23:59:59'));
                      });
                })
                ->orWhere(function($q) use ($monthEnd) {
                    $q->where('pp.sisa_bayar_pokok', '<=', 0)
                      ->where('pp.sisa_bagi_hasil', '<=', 0)
                      ->where(function($subQ) use ($monthEnd) {
                          $subQ->whereNotNull('pp.updated_at')
                               ->where('pp.updated_at', '<=', $monthEnd->format('Y-m-d 23:59:59'));
                      });
                });
            })
            ->orderBy('pp.updated_at', 'desc')
            ->first();
            
        return $lunasLoan !== null;
    }
    
    protected function calculateWorstKolForMonth($payments)
    {
        $worstDaysLate = 0;
        
        foreach ($payments as $payment) {
            $daysLate = 0;
            
            if ($payment->due_date && $payment->tanggal_pembayaran) {
                $dueDate = Carbon::parse($payment->due_date);
                $paymentDate = Carbon::parse($payment->tanggal_pembayaran);
                
                if ($paymentDate->gt($dueDate)) {
                    $daysLate = $payment->hari_keterlambatan ?? $paymentDate->diffInDays($dueDate);
                } else {
                    $daysLate = 0;
                }
            }
            
            if ($daysLate > $worstDaysLate) {
                $worstDaysLate = $daysLate;
            }
        }
        
        return $this->daysLateToKol($worstDaysLate);
    }
    
    protected function getMaxOverdueDaysInMonth($debiturId, $month, $tahun)
    {
        $monthEnd = Carbon::create($tahun, $month, 1)->endOfMonth();
        $overdueInvoices = DB::table('report_pengembalian as rp')
            ->join('pengembalian_pinjaman as pp', 'rp.id_pengembalian', '=', 'pp.ulid')
            ->join('pengajuan_peminjaman as pm', 'pp.id_pengajuan_peminjaman', '=', 'pm.id_pengajuan_peminjaman')
            ->where('pm.id_debitur', $debiturId)
            ->whereNotNull('rp.due_date')
            ->where('rp.due_date', '<=', $monthEnd->format('Y-m-d'))
            ->where(function($query) use ($monthEnd) {
                $query->whereNull('rp.created_at')
                      ->orWhere('rp.created_at', '>', $monthEnd->format('Y-m-d H:i:s'));
            })
            ->select('rp.due_date')
            ->get();
        
        $maxOverdueDays = 0;
        
        foreach ($overdueInvoices as $invoice) {
            $dueDate = Carbon::parse($invoice->due_date);
            if ($dueDate->lt($monthEnd)) {
                $daysOverdue = $monthEnd->diffInDays($dueDate);
                
                if ($daysOverdue > $maxOverdueDays) {
                    $maxOverdueDays = $daysOverdue;
                }
            }
        }
        
        return $maxOverdueDays;
    }
    
    protected function daysLateToKol($daysLate)
    {
        if ($daysLate == 0) {
            return 1; // Lancar
        } elseif ($daysLate >= 1 && $daysLate <= 29) {
            return 2; // DEL 1-29 hari
        } elseif ($daysLate >= 30 && $daysLate <= 59) {
            return 3; // DEL 30-59 hari
        } elseif ($daysLate >= 60 && $daysLate <= 179) {
            return 4; // DEL 60-179 hari
        } else {
            return 5; // WriteOff >= 180 hari
        }
    }
}