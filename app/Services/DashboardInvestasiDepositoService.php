<?php

namespace App\Services;

use App\Models\PengajuanInvestasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardInvestasiDepositoService
{
    protected ?string $investorId = null;
    protected bool $isRestricted = false;

    public function __construct()
    {
        $this->initializeRestriction();
    }

    private function initializeRestriction(): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->isRestricted = true;
            return;
        }

        if ($user->hasRole('super-admin')) {
            $this->isRestricted = false;
            return;
        }

        $hasUnrestrictedRole = $user->roles()->where('restriction', 1)->exists();

        if ($hasUnrestrictedRole) {
            $this->isRestricted = false;
            return;
        }

        $this->isRestricted = true;
        $debiturInvestor = $user->debitur;
        $this->investorId = $debiturInvestor ? $debiturInvestor->id_debitur : null;
    }

    public function isUserRestricted(): bool
    {
        return $this->isRestricted;
    }

    public function getInvestorId(): ?string
    {
        return $this->investorId;
    }

    private function applyRestriction($query, string $investorColumn = 'id_debitur_dan_investor')
    {
        if ($this->isRestricted && $this->investorId) {
            $query->where($investorColumn, $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            $query->whereRaw('1 = 0');
        }
        return $query;
    }

    public function getSummaryData(): array
    {
        // 1. Total Deposito Pokok
        $totalDepositoPokok = $this->getTotalDepositoPokok();

        // 2. Total CoF
        $totalCoF = $this->getTotalCoF();

        // 3. Total Pengembalian
        $totalPengembalian = $this->getTotalPengembalian();

        // 4. Total Outstanding
        $totalOutstanding = $this->getTotalOutstanding();

        // 5. Total Dana Disalurkan
        $totalDisalurkan = $this->getTotalDisalurkan();

        // 6. Outstanding Penyaluran
        $outstandingPenyaluran = $this->getOutstandingPenyaluran();

        return [
            'total_deposito_pokok' => $totalDepositoPokok,
            'total_deposito_pokok_percentage' => 0.0,
            'total_deposito_pokok_is_increase' => false,
            'total_deposito_pokok_is_new' => false,

            'total_cof' => $totalCoF,
            'total_cof_percentage' => 0.0,
            'total_cof_is_increase' => false,
            'total_cof_is_new' => false,

            'total_pengembalian' => $totalPengembalian,
            'total_pengembalian_percentage' => 0.0,
            'total_pengembalian_is_increase' => false,
            'total_pengembalian_is_new' => false,

            'total_outstanding' => $totalOutstanding,
            'total_outstanding_percentage' => 0.0,
            'total_outstanding_is_increase' => false,
            'total_outstanding_is_new' => false,

            'total_disalurkan' => $totalDisalurkan,
            'outstanding_penyaluran' => $outstandingPenyaluran,

            'previous_month_name' => '',
        ];
    }

    private function calculateStats(float $previous, float $current): array
    {
        if ($previous == 0) {
            if ($current > 0) {
                return ['percentage' => 100, 'is_increase' => true, 'is_new' => true];
            }
            return ['percentage' => 0, 'is_increase' => false, 'is_new' => false];
        }

        $percentage = (($current - $previous) / $previous) * 100;
        return [
            'percentage' => abs($percentage),
            'is_increase' => $percentage >= 0,
            'is_new' => false
        ];
    }

    private function getTotalDepositoPokok(): float
    {
        $query = DB::table('pengajuan_investasi')
            ->whereNotIn('status', ['Draft', 'Rejected', 'Ditolak']);

        $this->applyRestriction($query);

        return (float)$query->sum('jumlah_investasi');
    }

    private function getTotalCoF(): float
    {
        $query = DB::table('pengajuan_investasi')
            ->select('jumlah_investasi', 'bunga_pertahun')
            ->whereNotIn('status', ['Draft', 'Rejected', 'Ditolak']);

        $this->applyRestriction($query);

        $data = $query->get();
        $totalCof = 0;

        foreach ($data as $item) {
            $jumlahInvestasi = (float)$item->jumlah_investasi;
            $bungaPertahun = (float)$item->bunga_pertahun;
            $bungaPerBulan = $bungaPertahun / 12;
            $cofBulan = ($jumlahInvestasi * $bungaPerBulan) / 100;
            $totalCof += $cofBulan;
        }

        return $totalCof;
    }

    private function getTotalPengembalian(): float
    {
        $query = DB::table('pengembalian_investasi as pi')
            ->join('pengajuan_investasi as pj', 'pi.id_pengajuan_investasi', '=', 'pj.id_pengajuan_investasi')
            ->whereNotIn('pj.status', ['Draft', 'Rejected', 'Ditolak']);

        if ($this->isRestricted && $this->investorId) {
            $query->where('pj.id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return 0.0;
        }

        return (float)$query->selectRaw('COALESCE(SUM(pi.dana_pokok_dibayar + pi.bunga_dibayar), 0) as total')
            ->value('total');
    }

    private function getTotalOutstanding(): float
    {
        $query = DB::table('pengajuan_investasi')
            ->whereNotIn('status', ['Draft', 'Rejected', 'Ditolak']);

        $this->applyRestriction($query);

        return (float)$query->selectRaw('COALESCE(SUM(sisa_pokok + sisa_bunga), 0) as total')
            ->value('total');
    }

    private function getTotalDisalurkan(): float
    {
        $query = DB::table('pengajuan_investasi')
            ->whereNotIn('status', ['Draft', 'Rejected', 'Ditolak']);

        $this->applyRestriction($query);

        return (float)$query->sum('total_disalurkan');
    }

    private function getOutstandingPenyaluran(): float
    {
        $query = DB::table('pengajuan_investasi')
            ->whereNotIn('status', ['Draft', 'Rejected', 'Ditolak']);

        $this->applyRestriction($query);

        return (float)$query->selectRaw('COALESCE(SUM(total_disalurkan - total_kembali_dari_penyaluran), 0) as total')
            ->value('total');
    }

    public function getChartInvestasiPokok(?string $bulan = null): array
    {
        $currentYear = date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengajuan_investasi')
            ->select('nama_investor', DB::raw('SUM(jumlah_investasi) as total_pokok'))
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth)
            ->groupBy('nama_investor')
            ->orderBy('nama_investor');

        $this->applyRestriction($query);

        $data = $query->get();

        if ($data->isEmpty()) {
            return ['categories' => [], 'series' => [['name' => 'Pokok', 'data' => []]]];
        }

        $categories = [];
        $pokokData = [];

        foreach ($data as $item) {
            $categories[] = $item->nama_investor;
            $pokokData[] = (float)$item->total_pokok;
        }

        return [
            'categories' => $categories,
            'series' => [['name' => 'Pokok', 'data' => $pokokData]]
        ];
    }

    public function getChartCoF(?string $bulan = null): array
    {
        $currentYear = date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengajuan_investasi')
            ->select('nama_investor', 'jumlah_investasi', 'bunga_pertahun')
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth);

        $this->applyRestriction($query);

        $data = $query->get();

        if ($data->isEmpty()) {
            return ['categories' => [], 'series' => [['name' => 'CoF', 'data' => []]]];
        }

        $cofPerInvestor = [];

        foreach ($data as $item) {
            $namaInvestor = $item->nama_investor;
            $jumlahInvestasi = (float)$item->jumlah_investasi;
            $bungaPertahun = (float)$item->bunga_pertahun;
            $bungaPerBulan = $bungaPertahun / 12;
            $cofBulan = ($jumlahInvestasi * $bungaPerBulan) / 100;

            if (!isset($cofPerInvestor[$namaInvestor])) {
                $cofPerInvestor[$namaInvestor] = 0;
            }
            $cofPerInvestor[$namaInvestor] += $cofBulan;
        }

        ksort($cofPerInvestor);

        $categories = array_keys($cofPerInvestor);
        $cofData = array_values($cofPerInvestor);

        return [
            'categories' => $categories,
            'series' => [['name' => 'CoF', 'data' => $cofData]]
        ];
    }

    public function getChartPengembalian(?string $bulan = null): array
    {
        $currentYear = date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengembalian_investasi as pi')
            ->join('pengajuan_investasi as pj', 'pi.id_pengajuan_investasi', '=', 'pj.id_pengajuan_investasi')
            ->select(
                'pj.nama_investor',
                DB::raw('SUM(pi.dana_pokok_dibayar) as total_pokok'),
                DB::raw('SUM(pi.bunga_dibayar) as total_bunga')
            )
            ->whereYear('pi.tanggal_pengembalian', $currentYear)
            ->whereMonth('pi.tanggal_pengembalian', $selectedMonth)
            ->groupBy('pj.nama_investor')
            ->orderBy('pj.nama_investor');

        if ($this->isRestricted && $this->investorId) {
            $query->where('pj.id_debitur_dan_investor', $this->investorId);
        } elseif ($this->isRestricted && !$this->investorId) {
            return [
                'categories' => [],
                'series' => [
                    ['name' => 'Pokok', 'data' => []],
                    ['name' => 'Bunga', 'data' => []]
                ]
            ];
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return [
                'categories' => [],
                'series' => [
                    ['name' => 'Pokok', 'data' => []],
                    ['name' => 'Bunga', 'data' => []]
                ]
            ];
        }

        $categories = [];
        $pokokData = [];
        $bungaData = [];

        foreach ($data as $item) {
            $categories[] = $item->nama_investor;
            $pokokData[] = (float)$item->total_pokok;
            $bungaData[] = (float)$item->total_bunga;
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Pokok', 'data' => $pokokData],
                ['name' => 'Bunga', 'data' => $bungaData]
            ]
        ];
    }

    public function getChartSisaInvestasi(?string $bulan = null): array
    {
        $currentYear = date('Y');
        $selectedMonth = $bulan ? (int)$bulan : (int)date('m');

        $query = DB::table('pengajuan_investasi')
            ->select(
                'nama_investor',
                DB::raw('SUM(sisa_pokok) as total_sisa_pokok'),
                DB::raw('SUM(sisa_bunga) as total_sisa_bunga')
            )
            ->whereYear('tanggal_investasi', $currentYear)
            ->whereMonth('tanggal_investasi', $selectedMonth)
            ->groupBy('nama_investor')
            ->orderBy('nama_investor');

        $this->applyRestriction($query);

        $data = $query->get();

        if ($data->isEmpty()) {
            return [
                'categories' => [],
                'series' => [
                    ['name' => 'Pokok', 'data' => []],
                    ['name' => 'Bunga', 'data' => []]
                ]
            ];
        }

        $categories = [];
        $pokokData = [];
        $bungaData = [];

        foreach ($data as $item) {
            $categories[] = $item->nama_investor;
            $pokokData[] = (float)$item->total_sisa_pokok;
            $bungaData[] = (float)$item->total_sisa_bunga;
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Pokok', 'data' => $pokokData],
                ['name' => 'Bunga', 'data' => $bungaData]
            ]
        ];
    }

    public function getMonthOptions(): array
    {
        return [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    }

    public function getTrenInvestasiData(): array
    {
        $pivot = Carbon::today()->startOfMonth();
        $categories = [];
        $masuk = [];
        $pengembalian = [];

        $bulanNama = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        for ($i = 11; $i >= 0; $i--) {
            $m = $pivot->copy()->subMonths($i);
            $start = $m->copy()->startOfMonth();
            $end = $m->copy()->endOfMonth();

            $categories[] = $bulanNama[$m->month] . ' ' . $m->year;

            // Masuk Query
            $masukQuery = DB::table('pengajuan_investasi')
                ->whereBetween('tanggal_investasi', [$start, $end])
                ->whereNotIn('status', ['Draft', 'Rejected', 'Ditolak']);
            $this->applyRestriction($masukQuery);
            $masuk[] = (float)$masukQuery->sum('jumlah_investasi');

            // Pengembalian Query
            $kembaliQuery = DB::table('pengembalian_investasi as pi')
                ->join('pengajuan_investasi as pj', 'pi.id_pengajuan_investasi', '=', 'pj.id_pengajuan_investasi')
                ->whereBetween('pi.tanggal_pengembalian', [$start, $end])
                ->whereNotIn('pj.status', ['Draft', 'Rejected', 'Ditolak']);
                
            if ($this->isRestricted && $this->investorId) {
                $kembaliQuery->where('pj.id_debitur_dan_investor', $this->investorId);
            } elseif ($this->isRestricted && !$this->investorId) {
                $pengembalian[] = 0.0;
                continue;
            }
            $pengembalian[] = (float)$kembaliQuery->sum(DB::raw('pi.dana_pokok_dibayar + pi.bunga_dibayar'));
        }

        return compact('categories', 'masuk', 'pengembalian');
    }

    public function getUpcomingMaturingInvestments(): array
    {
        $query = DB::table('pengajuan_investasi')
            ->whereNotIn('status', ['Draft', 'Rejected', 'Ditolak'])
            ->where('sisa_pokok', '>', 0);
            
        $this->applyRestriction($query);
        
        $investments = $query->get();
        $upcoming = [];
        $today = Carbon::today();
        $limit = Carbon::today()->addDays(30);
        
        foreach ($investments as $inv) {
            $startDate = Carbon::parse($inv->tanggal_investasi);
            $maturityDate = $startDate->copy()->addMonths((int)$inv->lama_investasi);
            
            // Check if maturity date is in the next 30 days
            if ($maturityDate->gte($today) && $maturityDate->lte($limit)) {
                $upcoming[] = [
                    'nomor_kontrak' => $inv->nomor_kontrak ?? '-',
                    'nama_investor' => $inv->nama_investor,
                    'pic' => $inv->nama_pic_kontrak ?? '-',
                    'jumlah_investasi' => (float)$inv->jumlah_investasi,
                    'sisa_pokok' => (float)$inv->sisa_pokok,
                    'tanggal_jatuh_tempo' => $maturityDate->format('d/m/Y'),
                    'hari_tersisa' => (int)$today->diffInDays($maturityDate, false),
                ];
            }
        }
        
        // Sort by days remaining ascending
        usort($upcoming, fn($a, $b) => $a['hari_tersisa'] <=> $b['hari_tersisa']);
        
        return $upcoming;
    }

    public function getUpcomingMaturingDistributions(): array
    {
        $query = DB::table('penyaluran_dana_investasi as pdi')
            ->join('pengajuan_investasi as pi', 'pdi.id_pengajuan_investasi', '=', 'pi.id_pengajuan_investasi')
            ->join('master_debitur_dan_investor as md', 'pdi.id_debitur', '=', 'md.id_debitur')
            ->select([
                'pdi.*',
                'pi.nomor_kontrak as nomor_kontrak_investasi',
                'md.nama as nama_debitur',
                'md.nama_ceo as pic_debitur',
            ])
            ->whereNotIn('pi.status', ['Draft', 'Rejected', 'Ditolak'])
            ->whereRaw('pdi.nominal_yang_disalurkan > pdi.nominal_yang_dikembalikan');

        $this->applyRestriction($query, 'pi.id_debitur_dan_investor');

        $distributions = $query->get();
        $upcoming = [];
        $today = Carbon::today();
        $limit = Carbon::today()->addDays(30);

        foreach ($distributions as $dist) {
            $returnDate = Carbon::parse($dist->tanggal_pengembalian);
            
            if ($returnDate->gte($today) && $returnDate->lte($limit)) {
                $sisaBelumKembali = (float)$dist->nominal_yang_disalurkan - (float)$dist->nominal_yang_dikembalikan;
                $upcoming[] = [
                    'id_penyaluran_dana_investasi' => $dist->id_penyaluran_dana_investasi,
                    'nomor_kontrak_investasi' => $dist->nomor_kontrak_investasi ?: '-',
                    'nama_debitur' => $dist->nama_debitur,
                    'pic' => $dist->pic_debitur ?? '-',
                    'nominal_disalurkan' => (float)$dist->nominal_yang_disalurkan,
                    'sisa_tagihan' => $sisaBelumKembali,
                    'tanggal_jatuh_tempo' => $returnDate->format('d/m/Y'),
                    'hari_tersisa' => (int)$today->diffInDays($returnDate, false),
                ];
            }
        }

        usort($upcoming, fn($a, $b) => $a['hari_tersisa'] <=> $b['hari_tersisa']);

        return $upcoming;
    }

    public function getTopInvestorsList(): array
    {
        $query = DB::table('pengajuan_investasi')
            ->select('nama_investor', DB::raw('SUM(jumlah_investasi) as total_investasi'), DB::raw('SUM(sisa_pokok) as total_outstanding'))
            ->whereNotIn('status', ['Draft', 'Rejected', 'Ditolak'])
            ->groupBy('nama_investor')
            ->orderBy('total_investasi', 'desc')
            ->limit(5);
            
        $this->applyRestriction($query);
        
        return $query->get()->toArray();
    }

    public function getJenisInvestasiMix(): array
    {
        $query = DB::table('pengajuan_investasi')
            ->select('jenis_investasi', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(jumlah_investasi) as total'))
            ->whereNotIn('status', ['Draft', 'Rejected', 'Ditolak'])
            ->groupBy('jenis_investasi');
            
        $this->applyRestriction($query);
        
        $data = $query->get();
        $labels = [];
        $values = [];
        
        foreach ($data as $row) {
            $labels[] = $row->jenis_investasi ?: 'Lainnya';
            $values[] = (float)$row->total;
        }
        
        return compact('labels', 'values');
    }
}
