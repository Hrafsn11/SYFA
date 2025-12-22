<?php

namespace App\Livewire\Sfinlog;

use Livewire\Component;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\PengembalianInvestasiFinlog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardInvestasiDepositoSfinlog extends Component
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
     * Data diambil dari pengajuan_investasi_finlog, kolom nominal_investasi
     * Filter berdasarkan bulan yang dipilih ($selectedMonth)
     * Chart menampilkan total deposito pokok per perusahaan (investor)
     */
    private function getChartDepositoPokok()
    {
        $currentYear = date('Y');
        $selectedMonth = $this->selectedMonthDepositoPokok ? (int) $this->selectedMonthDepositoPokok : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Query untuk mendapatkan total deposito pokok per perusahaan
        // Filter berdasarkan bulan yang dipilih
        $dataPerPerusahaan = DB::table('pengajuan_investasi_finlog')
            ->select(
                'nama_investor',
                DB::raw('SUM(nominal_investasi) as total_pokok')
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
     * Data diambil dari pengajuan_investasi_finlog
     * CoF per bulan dari kertas kerja investor SFinlog: bagi_hasil (nominal/pa) / 12
     * dimana bagi_hasil (nominal/pa) = (persentase_bagi_hasil * nominal_investasi) / 100
     * Filter berdasarkan bulan yang dipilih ($selectedMonth)
     * Chart menampilkan total CoF per bulan per perusahaan (investor)
     */
    private function getChartCoF()
    {
        $currentYear = date('Y');
        $selectedMonth = $this->selectedMonthCoF ? (int) $this->selectedMonthCoF : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Query untuk mendapatkan data investasi per perusahaan
        // Filter berdasarkan bulan yang dipilih
        $dataInvestasi = DB::table('pengajuan_investasi_finlog')
            ->select(
                'nama_investor',
                'nominal_investasi',
                'persentase_bagi_hasil'
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
        // Bagi hasil (nominal/pa) = bagi hasil(%pa) × nominal deposito / 100
        // Bagi hasil (COF/bulan) = bagi hasil (nominal/pa) / 12
        $cofPerPerusahaan = [];
        
        foreach ($dataInvestasi as $item) {
            $namaInvestor = $item->nama_investor;
            $nominalInvestasi = (float) $item->nominal_investasi;
            $persentaseBagiHasil = (float) $item->persentase_bagi_hasil;
            
            // Hitung CoF per bulan
            $bagiHasilNominalPa = ($persentaseBagiHasil * $nominalInvestasi) / 100;
            $cofBulan = $bagiHasilNominalPa / 12;
            
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
     * Data diambil dari pengembalian_investasi_finlog
     * Kolom: dana_pokok_dibayar (Pengembalian Pokok) dan bagi_hasil_dibayar (Pengembalian Bagi Hasil)
     * Filter berdasarkan bulan yang dipilih ($selectedMonth) dari tanggal_pengembalian
     * Chart menampilkan total pengembalian pokok dan bagi hasil per perusahaan (investor)
     */
    private function getChartPengembalian()
    {
        $currentYear = date('Y');
        $selectedMonth = $this->selectedMonthPengembalian ? (int) $this->selectedMonthPengembalian : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Query untuk mendapatkan total pengembalian pokok dan bagi hasil per perusahaan
        // Join dengan pengajuan_investasi_finlog untuk mendapatkan nama_investor
        // Filter berdasarkan bulan yang dipilih dari tanggal_pengembalian
        $dataPengembalian = DB::table('pengembalian_investasi_finlog')
            ->join('pengajuan_investasi_finlog', 'pengembalian_investasi_finlog.id_pengajuan_investasi_finlog', '=', 'pengajuan_investasi_finlog.id_pengajuan_investasi_finlog')
            ->select(
                'pengajuan_investasi_finlog.nama_investor',
                DB::raw('SUM(pengembalian_investasi_finlog.dana_pokok_dibayar) as total_pokok'),
                DB::raw('SUM(pengembalian_investasi_finlog.bagi_hasil_dibayar) as total_bagi_hasil')
            )
            ->whereYear('pengembalian_investasi_finlog.tanggal_pengembalian', $currentYear)
            ->whereMonth('pengembalian_investasi_finlog.tanggal_pengembalian', $selectedMonth)
            ->groupBy('pengajuan_investasi_finlog.nama_investor')
            ->orderBy('pengajuan_investasi_finlog.nama_investor')
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
     * Data diambil dari kertas kerja investor SFinlog
     * Kolom: Sisa Pokok Belum Dikembalikan dan Sisa Bagi Hasil Belum Dikembalikan
     * Filter berdasarkan bulan yang dipilih ($selectedMonth) dari tanggal_investasi
     * Chart menampilkan total sisa pokok dan sisa bagi hasil per perusahaan (investor)
     */
    private function getChartSisaDeposito()
    {
        $currentYear = date('Y');
        $selectedMonth = $this->selectedMonthSisaDeposito ? (int) $this->selectedMonthSisaDeposito : (($this->selectedMonth ? (int) $this->selectedMonth : (int) date('m')));
        
        // Query untuk mendapatkan data investasi yang sudah memiliki nomor kontrak
        $investasi = DB::table('pengajuan_investasi_finlog')
            ->select(
                'id_pengajuan_investasi_finlog',
                'nama_investor',
                'nominal_investasi',
                'persentase_bagi_hasil',
                'lama_investasi'
            )
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth)
            ->get();

        // Jika tidak ada data, return array kosong
        if ($investasi->isEmpty()) {
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

        // Hitung total pengembalian per investor
        $totalPengembalian = DB::table('pengembalian_investasi_finlog')
            ->select(
                'id_pengajuan_investasi_finlog',
                DB::raw('SUM(dana_pokok_dibayar) as total_pokok_all'),
                DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil_all')
            )
            ->groupBy('id_pengajuan_investasi_finlog')
            ->get()
            ->keyBy('id_pengajuan_investasi_finlog');

        $companyData = [];

        foreach ($investasi as $inv) {
            $id = $inv->id_pengajuan_investasi_finlog;
            $total = $totalPengembalian->get($id);
            
            // Bagi hasil (nominal/pa) = bagi hasil(%pa) × nominal deposito / 100
            $bagiHasilNominalPa = ($inv->persentase_bagi_hasil * $inv->nominal_investasi) / 100;
            
            // Bagi hasil (COF/bulan) = bagi hasil (nominal/pa) / 12
            $cofBulan = $bagiHasilNominalPa / 12;
            
            // Bagi hasil per nominal = lama deposito(bulan) × bagi hasil(COF/bulan)
            $bagiHasilPerNominal = $inv->lama_investasi * $cofBulan;
            
            // Hitung sisa pokok dan sisa bagi hasil
            $totalPokokDikembalikan = $total->total_pokok_all ?? 0;
            $totalBagiHasilDibayar = $total->total_bagi_hasil_all ?? 0;
            
            // Sisa pokok = nominal investasi - total pokok yang sudah dikembalikan
            $sisaPokok = max(0, $inv->nominal_investasi - $totalPokokDikembalikan);
            
            // Sisa bagi hasil = bagi hasil per nominal - total bagi hasil yang sudah dibayar
            $sisaBagiHasil = max(0, $bagiHasilPerNominal - $totalBagiHasilDibayar);
            
            if ($sisaPokok > 0 || $sisaBagiHasil > 0) {
                if (!isset($companyData[$inv->nama_investor])) {
                    $companyData[$inv->nama_investor] = [
                        'sisa_pokok' => 0,
                        'sisa_bagi_hasil' => 0
                    ];
                }
                $companyData[$inv->nama_investor]['sisa_pokok'] += $sisaPokok;
                $companyData[$inv->nama_investor]['sisa_bagi_hasil'] += $sisaBagiHasil;
            }
        }

        // Extract categories (nama perusahaan) dan data (total sisa pokok dan sisa bagi hasil)
        $categories = [];
        $dataSisaPokok = [];
        $dataSisaBagiHasil = [];

        foreach ($companyData as $namaInvestor => $data) {
            $categories[] = $namaInvestor;
            $dataSisaPokok[] = (float) $data['sisa_pokok'];
            $dataSisaBagiHasil[] = (float) $data['sisa_bagi_hasil'];
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
     * Data diambil dari pengajuan_investasi_finlog, kolom nominal_investasi
     */
    private function getTotalDepositoPokok($year, $month)
    {
        $total = DB::table('pengajuan_investasi_finlog')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month)
            ->sum('nominal_investasi');

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
     * CoF per bulan dari kertas kerja investor SFinlog: bagi_hasil (nominal/pa) / 12
     * dimana bagi_hasil (nominal/pa) = (persentase_bagi_hasil * nominal_investasi) / 100
     * Data diambil dari pengajuan_investasi_finlog
     */
    private function getTotalCoF($year, $month)
    {
        $dataInvestasi = DB::table('pengajuan_investasi_finlog')
            ->select('nominal_investasi', 'persentase_bagi_hasil')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month)
            ->get();

        $totalCof = 0;
        
        foreach ($dataInvestasi as $item) {
            $nominalInvestasi = (float) $item->nominal_investasi;
            $persentaseBagiHasil = (float) $item->persentase_bagi_hasil;
            
            // Hitung CoF per bulan
            $bagiHasilNominalPa = ($persentaseBagiHasil * $nominalInvestasi) / 100;
            $cofBulan = $bagiHasilNominalPa / 12;
            
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
     * Data diambil dari pengembalian_investasi_finlog: dana_pokok_dibayar + bagi_hasil_dibayar
     */
    private function getTotalPengembalian($year, $month)
    {
        $total = DB::table('pengembalian_investasi_finlog')
            ->whereYear('tanggal_pengembalian', $year)
            ->whereMonth('tanggal_pengembalian', $month)
            ->selectRaw('COALESCE(SUM(dana_pokok_dibayar + bagi_hasil_dibayar), 0) as total')
            ->value('total');

        return (float) $total;
    }

    /**
     * Get total outstanding deposito (sisa pokok + sisa bagi hasil) untuk bulan tertentu
     * Data diambil dari kertas kerja investor SFinlog
     */
    private function getTotalOutstandingDeposito($year, $month)
    {
        // Ambil data investasi yang sudah memiliki nomor kontrak
        $investasi = DB::table('pengajuan_investasi_finlog')
            ->select(
                'id_pengajuan_investasi_finlog',
                'nominal_investasi',
                'persentase_bagi_hasil',
                'lama_investasi'
            )
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->whereYear('tanggal_investasi', $year)
            ->whereMonth('tanggal_investasi', $month)
            ->get();

        if ($investasi->isEmpty()) {
            return 0;
        }

        // Hitung total pengembalian per investor
        $totalPengembalian = DB::table('pengembalian_investasi_finlog')
            ->select(
                'id_pengajuan_investasi_finlog',
                DB::raw('SUM(dana_pokok_dibayar) as total_pokok_all'),
                DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil_all')
            )
            ->groupBy('id_pengajuan_investasi_finlog')
            ->get()
            ->keyBy('id_pengajuan_investasi_finlog');

        $totalOutstanding = 0;
        foreach ($investasi as $inv) {
            $id = $inv->id_pengajuan_investasi_finlog;
            $total = $totalPengembalian->get($id);
            
            // Bagi hasil (nominal/pa) = bagi hasil(%pa) × nominal deposito / 100
            $bagiHasilNominalPa = ($inv->persentase_bagi_hasil * $inv->nominal_investasi) / 100;
            
            // Bagi hasil (COF/bulan) = bagi hasil (nominal/pa) / 12
            $cofBulan = $bagiHasilNominalPa / 12;
            
            // Bagi hasil per nominal = lama deposito(bulan) × bagi hasil(COF/bulan)
            $bagiHasilPerNominal = $inv->lama_investasi * $cofBulan;
            
            // Hitung sisa pokok dan sisa bagi hasil
            $totalPokokDikembalikan = $total->total_pokok_all ?? 0;
            $totalBagiHasilDibayar = $total->total_bagi_hasil_all ?? 0;
            
            // Sisa pokok = nominal investasi - total pokok yang sudah dikembalikan
            $sisaPokok = max(0, $inv->nominal_investasi - $totalPokokDikembalikan);
            
            // Sisa bagi hasil = bagi hasil per nominal - total bagi hasil yang sudah dibayar
            $sisaBagiHasil = max(0, $bagiHasilPerNominal - $totalBagiHasilDibayar);
            
            $totalOutstanding += $sisaPokok + $sisaBagiHasil;
        }

        return (float) $totalOutstanding;
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
     * Menggunakan data dari kertas kerja investor SFinlog (sisa_pokok + sisa_bagi_hasil)
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

        return view('livewire.sfinlog.dashboard-investasi-deposito', compact(
            'summaryData',
            'chartDepositoPokok',
            'chartCoF',
            'chartPengembalian',
            'chartSisaDeposito'
        ));
    }
}
