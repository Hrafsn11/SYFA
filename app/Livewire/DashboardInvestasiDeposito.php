<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasi;
use Carbon\Carbon;

class DashboardInvestasiDeposito extends Component
{
    // Property untuk filter bulan masing-masing chart
    public $selectedMonthDepositoPokok = null;
    public $selectedMonthCoF = null;
    public $selectedMonthPengembalian = null;
    public $selectedMonthSisaDeposito = null;

    // Property untuk backward compatibility (default untuk semua chart jika belum di-set)
    public $selectedMonth = null;

    public function mount()
    {
        // Set default bulan ke bulan saat ini untuk semua filter
        $currentMonth = date('m');
        $this->selectedMonth = $currentMonth;
        $this->selectedMonthDepositoPokok = $currentMonth;
        $this->selectedMonthCoF = $currentMonth;
        $this->selectedMonthPengembalian = $currentMonth;
        $this->selectedMonthSisaDeposito = $currentMonth;
    }

    public function updatedSelectedMonthDepositoPokok()
    {
        // Method ini akan dipanggil ketika bulan berubah untuk chart Deposito Pokok
    }

    public function updatedSelectedMonthCoF()
    {
        // Method ini akan dipanggil ketika bulan berubah untuk chart CoF
    }

    public function updatedSelectedMonthPengembalian()
    {
        // Method ini akan dipanggil ketika bulan berubah untuk chart Pengembalian
    }

    public function updatedSelectedMonthSisaDeposito()
    {
        // Method ini akan dipanggil ketika bulan berubah untuk chart Sisa Deposito
    }

    public function updatedSelectedMonth()
    {
        // Method ini akan dipanggil ketika bulan berubah (backward compatibility)
    }

    /**
     * Get chart data for Total Deposito Pokok yang masuk Per Bulan
     * Data diambil dari pengajuan_investasi, kolom jumlah_investasi
     * Filter berdasarkan bulan yang dipilih ($selectedMonth)
     * Chart menampilkan total deposito pokok per perusahaan (investor)
     */
    private function getChartDepositoPokok()
    {
        $currentYear = date('Y');
        $selectedMonth = $this->selectedMonthDepositoPokok ? (int) $this->selectedMonthDepositoPokok : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Query untuk mendapatkan total deposito pokok per perusahaan
        // Filter berdasarkan bulan yang dipilih
        $dataPerPerusahaan = DB::table('pengajuan_investasi')
            ->select(
                'nama_investor',
                DB::raw('SUM(jumlah_investasi) as total_pokok')
            )
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth)
            ->groupBy('nama_investor')
            ->orderBy('nama_investor')
            ->get();

        // Jika tidak ada data, return array kosong
        if ($dataPerPerusahaan->isEmpty()) {
            return [
                'categories' => [],
                'series' => [
                    [
                        'name' => 'Pokok',
                        'data' => []
                    ]
                ]
            ];
        }

        // Extract categories (nama perusahaan) dan data (total pokok)
        $categories = [];
        $data = [];

        foreach ($dataPerPerusahaan as $item) {
            $categories[] = $item->nama_investor;
            $data[] = (float) $item->total_pokok;
        }

        return [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Pokok',
                    'data' => $data
                ]
            ]
        ];
    }

    /**
     * Get chart data for Total CoF per bulan
     * Data diambil dari pengajuan_investasi
     * CoF per bulan = (jumlah_investasi * (bagi_hasil_pertahun / 12)) / 100
     * Filter berdasarkan bulan yang dipilih ($selectedMonth)
     * Chart menampilkan total CoF per bulan per perusahaan (investor)
     */
    private function getChartCoF()
    {
        $currentYear = date('Y');
        $selectedMonth = $this->selectedMonthCoF ? (int) $this->selectedMonthCoF : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Query untuk mendapatkan data investasi per perusahaan
        // Filter berdasarkan bulan yang dipilih
        $dataInvestasi = DB::table('pengajuan_investasi')
            ->select(
                'nama_investor',
                'jumlah_investasi',
                'bagi_hasil_pertahun'
            )
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth)
            ->get();

        // Jika tidak ada data, return array kosong
        if ($dataInvestasi->isEmpty()) {
            return [
                'categories' => [],
                'series' => [
                    [
                        'name' => 'Pokok',
                        'data' => []
                    ]
                ]
            ];
        }

        // Hitung CoF per bulan per perusahaan
        // CoF per bulan = (jumlah_investasi * (bagi_hasil_pertahun / 12)) / 100
        $cofPerPerusahaan = [];
        
        foreach ($dataInvestasi as $item) {
            $namaInvestor = $item->nama_investor;
            $jumlahInvestasi = (float) $item->jumlah_investasi;
            $bagiHasilPertahun = (float) $item->bagi_hasil_pertahun;
            
            // Hitung CoF per bulan
            $bagiHasilPerBulan = $bagiHasilPertahun / 12;
            $cofBulan = ($jumlahInvestasi * $bagiHasilPerBulan) / 100;
            
            // Akumulasi per perusahaan
            if (!isset($cofPerPerusahaan[$namaInvestor])) {
                $cofPerPerusahaan[$namaInvestor] = 0;
            }
            $cofPerPerusahaan[$namaInvestor] += $cofBulan;
        }

        // Sort berdasarkan nama perusahaan
        ksort($cofPerPerusahaan);

        // Extract categories (nama perusahaan) dan data (total CoF per bulan)
        $categories = [];
        $data = [];

        foreach ($cofPerPerusahaan as $namaInvestor => $totalCof) {
            $categories[] = $namaInvestor;
            $data[] = $totalCof;
        }

        return [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Pokok',
                    'data' => $data
                ]
            ]
        ];
    }

    /**
     * Get chart data for Total Pengembalian Pokok dan Bagi Hasil Perbulan
     * Data diambil dari pengembalian_investasi
     * Kolom: dana_pokok_dibayar (Pengembalian Pokok) dan bagi_hasil_dibayar (Pengembalian Bagi Hasil)
     * Filter berdasarkan bulan yang dipilih ($selectedMonth) dari tanggal_pengembalian
     * Chart menampilkan total pengembalian pokok dan bagi hasil per perusahaan (investor)
     */
    private function getChartPengembalian()
    {
        $currentYear = date('Y');
        $selectedMonth = $this->selectedMonthPengembalian ? (int) $this->selectedMonthPengembalian : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Query untuk mendapatkan total pengembalian pokok dan bagi hasil per perusahaan
        // Join dengan pengajuan_investasi untuk mendapatkan nama_investor
        // Filter berdasarkan bulan yang dipilih dari tanggal_pengembalian
        $dataPengembalian = DB::table('pengembalian_investasi')
            ->join('pengajuan_investasi', 'pengembalian_investasi.id_pengajuan_investasi', '=', 'pengajuan_investasi.id_pengajuan_investasi')
            ->select(
                'pengajuan_investasi.nama_investor',
                DB::raw('SUM(pengembalian_investasi.dana_pokok_dibayar) as total_pokok'),
                DB::raw('SUM(pengembalian_investasi.bagi_hasil_dibayar) as total_bagi_hasil')
            )
            ->whereYear('pengembalian_investasi.tanggal_pengembalian', $currentYear)
            ->whereMonth('pengembalian_investasi.tanggal_pengembalian', $selectedMonth)
            ->groupBy('pengajuan_investasi.nama_investor')
            ->orderBy('pengajuan_investasi.nama_investor')
            ->get();

        // Jika tidak ada data, return array kosong
        if ($dataPengembalian->isEmpty()) {
            return [
                'categories' => [],
            'series' => [
                [
                    'name' => 'Pokok',
                        'data' => []
                    ],
                    [
                        'name' => 'Bagi Hasil',
                        'data' => []
                    ]
                ]
            ];
        }

        // Extract categories (nama perusahaan) dan data (total pokok dan bagi hasil)
        $categories = [];
        $dataPokok = [];
        $dataBagiHasil = [];

        foreach ($dataPengembalian as $item) {
            $categories[] = $item->nama_investor;
            $dataPokok[] = (float) $item->total_pokok;
            $dataBagiHasil[] = (float) $item->total_bagi_hasil;
        }

        return [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Pokok',
                    'data' => $dataPokok
                ],
                [
                    'name' => 'Bagi Hasil',
                    'data' => $dataBagiHasil
                ]
            ]
        ];
    }

    /**
     * Get chart data for Total Sisa Deposito Pokok dan CoF yang Belum Dikembalikan
     * Data diambil dari pengajuan_investasi
     * Kolom: sisa_pokok (Sisa Pokok Belum Dikembalikan) dan sisa_bagi_hasil (Sisa Bagi Hasil Belum Dikembalikan)
     * Filter berdasarkan bulan yang dipilih ($selectedMonth) dari tanggal_investasi
     * Chart menampilkan total sisa pokok dan sisa bagi hasil per perusahaan (investor)
     */
    private function getChartSisaDeposito()
    {
        $currentYear = date('Y');
        $selectedMonth = $this->selectedMonthSisaDeposito ? (int) $this->selectedMonthSisaDeposito : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Query untuk mendapatkan total sisa pokok dan sisa bagi hasil per perusahaan
        // Filter berdasarkan bulan investasi yang dipilih
        $dataSisa = DB::table('pengajuan_investasi')
            ->select(
                'nama_investor',
                DB::raw('SUM(sisa_pokok) as total_sisa_pokok'),
                DB::raw('SUM(sisa_bagi_hasil) as total_sisa_bagi_hasil')
            )
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth)
            ->groupBy('nama_investor')
            ->orderBy('nama_investor')
            ->get();

        // Jika tidak ada data, return array kosong
        if ($dataSisa->isEmpty()) {
            return [
                'categories' => [],
            'series' => [
                [
                    'name' => 'Pokok',
                        'data' => []
                ],
                [
                    'name' => 'Bagi Hasil',
                        'data' => []
                    ]
                ]
            ];
        }

        // Extract categories (nama perusahaan) dan data (total sisa pokok dan sisa bagi hasil)
        $categories = [];
        $dataSisaPokok = [];
        $dataSisaBagiHasil = [];

        foreach ($dataSisa as $item) {
            $categories[] = $item->nama_investor;
            $dataSisaPokok[] = (float) $item->total_sisa_pokok;
            $dataSisaBagiHasil[] = (float) $item->total_sisa_bagi_hasil;
        }

        return [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Pokok',
                    'data' => $dataSisaPokok
                ],
                [
                    'name' => 'Bagi Hasil',
                    'data' => $dataSisaBagiHasil
                ]
            ]
        ];
    }

    /**
     * Get total deposito pokok untuk bulan tertentu
     * Data diambil dari pengajuan_investasi, kolom jumlah_investasi
     */
    private function getTotalDepositoPokok($year, $month)
    {
        $total = DB::table('pengajuan_investasi')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month)
            ->sum('jumlah_investasi');

        return (float) $total;
    }

    /**
     * Get summary data for Total Deposito Pokok Masuk Bulan Ini
     * Menghitung total deposito pokok bulan ini dan membandingkan dengan bulan sebelumnya
     */
    private function getSummaryDepositoPokok()
    {
        $currentYear = date('Y');
        // Card menggunakan filter dari chart Deposito Pokok
        $selectedMonth = $this->selectedMonthDepositoPokok ? (int) $this->selectedMonthDepositoPokok : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Hitung total deposito pokok bulan ini
        $totalBulanIni = $this->getTotalDepositoPokok($currentYear, $selectedMonth);
        
        // Hitung bulan sebelumnya
        $bulanSebelumnya = $selectedMonth - 1;
        $tahunSebelumnya = $currentYear;
        
        // Jika bulan sebelumnya adalah 0, berarti bulan sebelumnya adalah Desember tahun lalu
        if ($bulanSebelumnya < 1) {
            $bulanSebelumnya = 12;
            $tahunSebelumnya = $currentYear - 1;
        }
        
        // Hitung total deposito pokok bulan sebelumnya
        $totalBulanSebelumnya = $this->getTotalDepositoPokok($tahunSebelumnya, $bulanSebelumnya);
        
        // Hitung persentase perubahan
        $persentase = 0;
        if ($totalBulanSebelumnya > 0) {
            $persentase = (($totalBulanIni - $totalBulanSebelumnya) / $totalBulanSebelumnya) * 100;
        }
        
        // Format nama bulan untuk period
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $period = $namaBulan[$selectedMonth] . ' ' . $currentYear;
        
        return [
            'total_deposito_pokok' => $totalBulanIni,
            'total_deposito_pokok_percent' => round($persentase, 1),
            'total_deposito_pokok_period' => $period,
        ];
    }

    /**
     * Get total CoF (Cost of Fund) untuk bulan tertentu
     * CoF per bulan = (jumlah_investasi * (bagi_hasil_pertahun / 12)) / 100
     * Data diambil dari pengajuan_investasi
     */
    private function getTotalCoF($year, $month)
    {
        $dataInvestasi = DB::table('pengajuan_investasi')
            ->select('jumlah_investasi', 'bagi_hasil_pertahun')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month)
            ->get();

        $totalCof = 0;
        
        foreach ($dataInvestasi as $item) {
            $jumlahInvestasi = (float) $item->jumlah_investasi;
            $bagiHasilPertahun = (float) $item->bagi_hasil_pertahun;
            
            // Hitung CoF per bulan
            $bagiHasilPerBulan = $bagiHasilPertahun / 12;
            $cofBulan = ($jumlahInvestasi * $bagiHasilPerBulan) / 100;
            
            $totalCof += $cofBulan;
        }

        return $totalCof;
    }

    /**
     * Get summary data for Total CoF (Cost of Fund) Bulan Ini
     * Menghitung total CoF bulan ini dan membandingkan dengan bulan sebelumnya
     */
    private function getSummaryCoF()
    {
        $currentYear = date('Y');
        // Card menggunakan filter dari chart CoF
        $selectedMonth = $this->selectedMonthCoF ? (int) $this->selectedMonthCoF : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Hitung total CoF bulan ini
        $totalBulanIni = $this->getTotalCoF($currentYear, $selectedMonth);
        
        // Hitung bulan sebelumnya
        $bulanSebelumnya = $selectedMonth - 1;
        $tahunSebelumnya = $currentYear;
        
        // Jika bulan sebelumnya adalah 0, berarti bulan sebelumnya adalah Desember tahun lalu
        if ($bulanSebelumnya < 1) {
            $bulanSebelumnya = 12;
            $tahunSebelumnya = $currentYear - 1;
        }
        
        // Hitung total CoF bulan sebelumnya
        $totalBulanSebelumnya = $this->getTotalCoF($tahunSebelumnya, $bulanSebelumnya);
        
        // Hitung persentase perubahan
        $persentase = 0;
        if ($totalBulanSebelumnya > 0) {
            $persentase = (($totalBulanIni - $totalBulanSebelumnya) / $totalBulanSebelumnya) * 100;
        }
        
        // Format nama bulan untuk period
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $period = $namaBulan[$selectedMonth] . ' ' . $currentYear;
        
        return [
            'total_cof' => $totalBulanIni,
            'total_cof_percent' => round($persentase, 1),
            'total_cof_period' => $period,
        ];
    }

    /**
     * Get total pengembalian (pokok + bagi hasil) untuk bulan tertentu
     * Data diambil dari pengembalian_investasi: dana_pokok_dibayar + bagi_hasil_dibayar
     */
    private function getTotalPengembalian($year, $month)
    {
        $total = DB::table('pengembalian_investasi')
            ->whereYear('tanggal_pengembalian', $year)
            ->whereMonth('tanggal_pengembalian', $month)
            ->selectRaw('COALESCE(SUM(dana_pokok_dibayar + bagi_hasil_dibayar), 0) as total')
            ->value('total');

        return (float) $total;
    }

    /**
     * Get total outstanding deposito (sisa pokok + sisa bagi hasil) untuk bulan tertentu
     * Data diambil dari pengajuan_investasi
     */
    private function getTotalOutstandingDeposito($year, $month)
    {
        return (float) DB::table('pengajuan_investasi')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month)
            ->selectRaw('COALESCE(SUM(sisa_pokok + sisa_bagi_hasil), 0) as total_outstanding')
            ->value('total_outstanding');
    }

    /**
     * Get summary data for Total Pengembalian Bulan Ini
     * Menghitung total pengembalian (pokok + bagi hasil) bulan ini dan membandingkan dengan bulan sebelumnya
     */
    private function getSummaryPengembalian()
    {
        $currentYear = date('Y');
        // Card menggunakan filter dari chart Pengembalian
        $selectedMonth = $this->selectedMonthPengembalian ? (int) $this->selectedMonthPengembalian : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Hitung total pengembalian bulan ini
        $totalBulanIni = $this->getTotalPengembalian($currentYear, $selectedMonth);
        
        // Hitung bulan sebelumnya
        $bulanSebelumnya = $selectedMonth - 1;
        $tahunSebelumnya = $currentYear;
        
        if ($bulanSebelumnya < 1) {
            $bulanSebelumnya = 12;
            $tahunSebelumnya = $currentYear - 1;
        }
        
        // Hitung total pengembalian bulan sebelumnya
        $totalBulanSebelumnya = $this->getTotalPengembalian($tahunSebelumnya, $bulanSebelumnya);
        
        // Hitung persentase perubahan
        $persentase = 0;
        if ($totalBulanSebelumnya > 0) {
            $persentase = (($totalBulanIni - $totalBulanSebelumnya) / $totalBulanSebelumnya) * 100;
        }
        
        // Format nama bulan untuk period
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $period = $namaBulan[$selectedMonth] . ' ' . $currentYear;
        
        return [
            'total_pengembalian' => $totalBulanIni,
            'total_pengembalian_percent' => round($persentase, 1),
            'total_pengembalian_period' => $period,
        ];
    }

    /**
     * Get summary data untuk Total Outstanding Deposito
     * Menggunakan kolom total_belum_dikembalikan dari kertas kerja (sisa_pokok + sisa_bagi_hasil)
     * Dibandingkan dengan bulan sebelumnya; jika tidak ada transaksi di bulan sebelumnya maka persentase 0
     */
    private function getSummaryOutstandingDeposito()
    {
        $currentYear = date('Y');
        // Gunakan filter yang sama dengan chart Sisa Deposito
        $selectedMonth = $this->selectedMonthSisaDeposito
            ? (int) $this->selectedMonthSisaDeposito
            : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));

        $totalBulanIni = $this->getTotalOutstandingDeposito($currentYear, $selectedMonth);

        // Hitung bulan sebelumnya
        $bulanSebelumnya = $selectedMonth - 1;
        $tahunSebelumnya = $currentYear;

        if ($bulanSebelumnya < 1) {
            $bulanSebelumnya = 12;
            $tahunSebelumnya = $currentYear - 1;
        }

        $totalBulanSebelumnya = $this->getTotalOutstandingDeposito($tahunSebelumnya, $bulanSebelumnya);

        // Hitung persentase perubahan; jika tidak ada transaksi sebelumnya, persentase = 0
        $persentase = 0;
        if ($totalBulanSebelumnya > 0) {
            $persentase = (($totalBulanIni - $totalBulanSebelumnya) / $totalBulanSebelumnya) * 100;
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $period = $namaBulan[$selectedMonth] . ' ' . $currentYear;

        return [
            'total_outstanding' => $totalBulanIni,
            'total_outstanding_percent' => round($persentase, 1),
            'total_outstanding_period' => $period,
        ];
    }

    public function render()
    {
        // Data untuk summary cards
        $summaryDepositoPokok = $this->getSummaryDepositoPokok();
        $summaryCoF = $this->getSummaryCoF();
        $summaryPengembalian = $this->getSummaryPengembalian();
        $summaryOutstanding = $this->getSummaryOutstandingDeposito();
        
        $summaryData = [
            'total_deposito_pokok' => $summaryDepositoPokok['total_deposito_pokok'],
            'total_deposito_pokok_percent' => $summaryDepositoPokok['total_deposito_pokok_percent'],
            'total_deposito_pokok_period' => $summaryDepositoPokok['total_deposito_pokok_period'],
            
            'total_cof' => $summaryCoF['total_cof'],
            'total_cof_percent' => $summaryCoF['total_cof_percent'],
            'total_cof_period' => $summaryCoF['total_cof_period'],
            
            'total_pengembalian' => $summaryPengembalian['total_pengembalian'],
            'total_pengembalian_percent' => $summaryPengembalian['total_pengembalian_percent'],
            'total_pengembalian_period' => $summaryPengembalian['total_pengembalian_period'],
            
            'total_outstanding' => $summaryOutstanding['total_outstanding'],
            'total_outstanding_percent' => $summaryOutstanding['total_outstanding_percent'],
            'total_outstanding_period' => $summaryOutstanding['total_outstanding_period'],
        ];

        // Data untuk chart Total Deposito Pokok yang masuk Per Bulan
        $chartDepositoPokok = $this->getChartDepositoPokok();

        // Data untuk chart Total CoF per bulan
        $chartCoF = $this->getChartCoF();

        // Data untuk chart Total Pengembalian Pokok dan Bagi Hasil Perbulan
        $chartPengembalian = $this->getChartPengembalian();

        // Data untuk chart Total Sisa Deposito Pokok dan CoF yang Belum Dikembalikan
        $chartSisaDeposito = $this->getChartSisaDeposito();

        return view('livewire.dashboard-investasi-deposito', compact(
            'summaryData',
            'chartDepositoPokok',
            'chartCoF',
            'chartPengembalian',
            'chartSisaDeposito'
        ))->layout('layouts.app');
    }
}

