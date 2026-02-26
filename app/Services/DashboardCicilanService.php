<?php

namespace App\Services;

use App\Models\JadwalAngsuran;
use App\Models\PengajuanCicilan;
use App\Models\PenyesuaianCicilan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardCicilanService
{
    private const BULAN_NAMA = [
        1  => 'Jan', 2  => 'Feb', 3  => 'Mar', 4  => 'Apr',
        5  => 'Mei', 6  => 'Jun', 7  => 'Jul', 8  => 'Agu',
        9  => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    private const BULAN_NAMA_FULL = [
        1  => 'Januari', 2  => 'Februari', 3  => 'Maret', 4 => 'April',
        5  => 'Mei',     6  => 'Juni',     7  => 'Juli',  8 => 'Agustus',
        9  => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    // =========================================================
    // SUMMARY CARDS
    // =========================================================

    public function getSummaryData(): array
    {
        $totalPengajuan = PengajuanCicilan::count();
        $dalamProses    = PengajuanCicilan::whereNotIn('status', ['Selesai', 'Ditolak'])->count();
        $selesai        = PengajuanCicilan::where('status', 'Selesai')->count();
        $ditolak        = PengajuanCicilan::where('status', 'like', '%Ditolak%')->count();

        // Gunakan total_cicilan (bukan plafon_pembiayaan) agar angka akurat
        $totalCicilanKeseluruhan = (float) PenyesuaianCicilan::sum('total_cicilan');

        // Total sudah terbayar dari tabel penyesuaian_cicilan (di-update saat konfirmasi pembayaran)
        $totalTerbayar = (float) PenyesuaianCicilan::sum('total_terbayar');

        // Sisa belum terbayar
        $totalSisa = max(0, $totalCicilanKeseluruhan - $totalTerbayar);

        $persenTerbayar = $totalCicilanKeseluruhan > 0
            ? round(($totalTerbayar / $totalCicilanKeseluruhan) * 100, 1)
            : 0.0;

        // Angsuran jatuh tempo hari ini
        $angsuranJatuhTempoHariIni = JadwalAngsuran::whereDate('tanggal_jatuh_tempo', Carbon::today())
            ->whereNotIn('status', ['Lunas'])
            ->count();

        // Angsuran terlambat (sudah lewat jatuh tempo, belum lunas)
        $angsuranTerlambat = JadwalAngsuran::where('tanggal_jatuh_tempo', '<', Carbon::today())
            ->whereNotIn('status', ['Lunas'])
            ->count();

        return [
            'total_pengajuan'               => $totalPengajuan,
            'dalam_proses'                  => $dalamProses,
            'selesai'                       => $selesai,
            'ditolak'                       => $ditolak,
            'total_cicilan_keseluruhan'     => $totalCicilanKeseluruhan,
            'total_terbayar'                => $totalTerbayar,
            'total_sisa'                    => $totalSisa,
            'persen_terbayar'               => $persenTerbayar,
            'angsuran_jatuh_tempo_hari_ini' => $angsuranJatuhTempoHariIni,
            'angsuran_terlambat'            => $angsuranTerlambat,
        ];
    }

    // =========================================================
    // CHART 1 – Tren Pengajuan Cicilan Per Bulan (12 bulan terakhir)
    // =========================================================

    public function getTrenPengajuanData(string $bulan, int|string $tahun): array
    {
        $pivot      = Carbon::create((int) $tahun, (int) $bulan, 1)->endOfMonth();
        $categories = [];
        $masuk      = [];
        $selesai    = [];
        $ditolak    = [];

        for ($i = 11; $i >= 0; $i--) {
            $m = $pivot->copy()->subMonths($i);
            $start = $m->copy()->startOfMonth();
            $end   = $m->copy()->endOfMonth();

            $categories[] = self::BULAN_NAMA[$m->month] . ' ' . $m->year;

            $masuk[]   = PengajuanCicilan::whereBetween('created_at', [$start, $end])->count();
            $selesai[] = PengajuanCicilan::where('status', 'Selesai')
                ->whereBetween('updated_at', [$start, $end])->count();
            $ditolak[] = PengajuanCicilan::where('status', 'like', '%Ditolak%')
                ->whereBetween('updated_at', [$start, $end])->count();
        }

        return compact('categories', 'masuk', 'selesai', 'ditolak');
    }

    // =========================================================
    // CHART 2 – Realisasi Pembayaran Angsuran Per Bulan
    // =========================================================

    public function getRealisasiPembayaranData(string $bulan, int|string $tahun): array
    {
        $pivot      = Carbon::create((int) $tahun, (int) $bulan, 1)->endOfMonth();
        $categories = [];
        $pokok      = [];
        $margin     = [];

        for ($i = 11; $i >= 0; $i--) {
            $m     = $pivot->copy()->subMonths($i);
            $start = $m->copy()->startOfMonth();
            $end   = $m->copy()->endOfMonth();

            $categories[] = self::BULAN_NAMA[$m->month] . ' ' . $m->year;

            // Ambil angsuran yang dibayar di bulan ini (Lunas atau Dibayar Sebagian)
            // Proporsikan total_terbayar ke pokok/margin berdasarkan rasio di jadwal
            $rows = JadwalAngsuran::whereBetween('tanggal_bayar', [$start, $end])
                ->whereNotNull('tanggal_bayar')
                ->whereIn('status', ['Lunas', 'Dibayar Sebagian'])
                ->selectRaw('
                    SUM(CASE WHEN total_cicilan > 0
                        THEN total_terbayar * pokok / total_cicilan
                        ELSE 0 END) as sum_pokok,
                    SUM(CASE WHEN total_cicilan > 0
                        THEN total_terbayar * margin / total_cicilan
                        ELSE 0 END) as sum_margin
                ')
                ->first();

            $pokok[]  = round((float) ($rows->sum_pokok  ?? 0), 2);
            $margin[] = round((float) ($rows->sum_margin ?? 0), 2);
        }

        return compact('categories', 'pokok', 'margin');
    }

    // =========================================================
    // CHART 3 – Total Pokok & Margin (single-month view, dipilih via filter)
    // =========================================================

    public function getPokokMarginBulanData(string $bulanTahun): array
    {
        [$tahun, $bulan] = explode('-', $bulanTahun);
        $start = Carbon::create((int) $tahun, (int) $bulan, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();
        $label = self::BULAN_NAMA[(int) $bulan] . ' ' . $tahun;

        $rows = PenyesuaianCicilan::where(function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_mulai_cicilan', [$start, $end])
                    ->orWhereBetween('created_at', [$start, $end]);
            })
            ->selectRaw('SUM(total_pokok) as sum_pokok, SUM(total_margin) as sum_margin')
            ->first();

        return [
            'categories' => [$label],
            'pokok'      => [(float) ($rows->sum_pokok  ?? 0)],
            'margin'     => [(float) ($rows->sum_margin ?? 0)],
        ];
    }

    public function getPokokMarginData(int|string $tahun): array
    {
        $categories = [];
        $pokok      = [];
        $margin     = [];

        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create((int) $tahun, $m, 1)->startOfMonth();
            $end   = $start->copy()->endOfMonth();

            $categories[] = self::BULAN_NAMA[$m];

            // Gunakan tanggal_mulai_cicilan agar merepresentasikan kapan program aktif
            $rows = PenyesuaianCicilan::where(function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_mulai_cicilan', [$start, $end])
                    ->orWhereBetween('created_at', [$start, $end]);
            })
                ->selectRaw('SUM(total_pokok) as sum_pokok, SUM(total_margin) as sum_margin')
                ->first();

            $pokok[]  = (float) ($rows->sum_pokok  ?? 0);
            $margin[] = (float) ($rows->sum_margin ?? 0);
        }

        return compact('categories', 'pokok', 'margin');
    }

    // =========================================================
    // CHART 4 – Angsuran Lunas vs Belum Lunas Per Bulan
    // =========================================================

    public function getAngsuranStatusData(string $bulan, int|string $tahun): array
    {
        $pivot      = Carbon::create((int) $tahun, (int) $bulan, 1)->endOfMonth();
        $categories = [];
        $lunas      = [];
        $dibayarSebagian = [];
        $belumLunas = [];

        for ($i = 11; $i >= 0; $i--) {
            $m     = $pivot->copy()->subMonths($i);
            $start = $m->copy()->startOfMonth();
            $end   = $m->copy()->endOfMonth();

            $categories[] = self::BULAN_NAMA[$m->month] . ' ' . $m->year;

            $lunas[]           = JadwalAngsuran::whereBetween('tanggal_jatuh_tempo', [$start, $end])
                ->where('status', 'Lunas')->count();
            $dibayarSebagian[] = JadwalAngsuran::whereBetween('tanggal_jatuh_tempo', [$start, $end])
                ->where('status', 'Dibayar Sebagian')->count();
            $belumLunas[]      = JadwalAngsuran::whereBetween('tanggal_jatuh_tempo', [$start, $end])
                ->whereNotIn('status', ['Lunas', 'Dibayar Sebagian'])->count();
        }

        return compact('categories', 'lunas', 'dibayarSebagian', 'belumLunas');
    }

    // =========================================================
    // TABLE 1 – Angsuran Jatuh Tempo 30 Hari Ke Depan
    // =========================================================

    public function getAngsuranJatuhTempoData(): array
    {
        $today = Carbon::today();
        $limit = $today->copy()->addDays(30);

        return JadwalAngsuran::with([
            'penyesuaianCicilan.pengajuanCicilan',
        ])
            // Tampilkan: (1) yang belum lunas & sudah lewat JT (overdue), dan
            //            (2) yang belum lunas & JT dalam 30 hari ke depan
            ->where('tanggal_jatuh_tempo', '<=', $limit)
            ->whereNotIn('status', ['Lunas'])
            ->orderBy('tanggal_jatuh_tempo')
            ->get()
            ->map(function ($row) {
                $penyesuaian = $row->penyesuaianCicilan;
                $pengajuan   = $penyesuaian?->pengajuanCicilan;
                return [
                    'no'                        => $row->no,
                    'nama_perusahaan'            => $pengajuan?->nama_perusahaan ?? '-',
                    'nomor_kontrak'              => $penyesuaian?->nomor_kontrak_restrukturisasi ?? '-',
                    'tanggal_jatuh_tempo'        => $row->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-',
                    'total_cicilan'              => (float) $row->total_cicilan,
                    'sisa_pembayaran'            => (float) $row->sisa_pembayaran,
                    'status'                     => $row->status,
                    'hari_tersisa'               => $row->tanggal_jatuh_tempo
                        ? (int) Carbon::today()->diffInDays($row->tanggal_jatuh_tempo, false)
                        : null,
                ];
            })->values()->toArray();
    }

    // =========================================================
    // TABLE 2 – Status Pengajuan Per Step
    // =========================================================

    public function getStatusPengajuanData(): array
    {
        $statuses = [
            'Draft'                   => ['label' => 'Draft',                   'color' => 'warning'],
            'Submit Dokumen'          => ['label' => 'Submit Dokumen',          'color' => 'info'],
            'Dokumen Tervalidasi'     => ['label' => 'Dokumen Tervalidasi',     'color' => 'success'],
            'Disetujui CEO SKI'       => ['label' => 'Disetujui CEO SKI',       'color' => 'success'],
            'Disetujui Direktur SKI'  => ['label' => 'Disetujui Direktur SKI', 'color' => 'success'],
            'Selesai'                 => ['label' => 'Selesai',                 'color' => 'primary'],
            'Ditolak'                 => ['label' => 'Ditolak',                 'color' => 'danger'],
        ];

        $rows = PengajuanCicilan::selectRaw(
            'status,
             COUNT(*) as jumlah,
             SUM(COALESCE(sisa_pokok_belum_dibayar, jumlah_plafon_awal, 0)) as total_plafon'
        )
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $result = [];
        foreach ($statuses as $key => $meta) {
            $data = $rows->get($key);
            $result[] = [
                'status'      => $meta['label'],
                'color'       => $meta['color'],
                'jumlah'      => $data ? (int) $data->jumlah : 0,
                'total_plafon' => $data ? (float) $data->total_plafon : 0.0,
            ];
        }

        return $result;
    }

    // =========================================================
    // TABLE 3 – Breakdown Metode Perhitungan
    // =========================================================

    public function getMetodePerhitunganData(): array
    {
        return PenyesuaianCicilan::selectRaw(
            'metode_perhitungan,
             COUNT(*) as jumlah,
             SUM(plafon_pembiayaan) as total_plafon,
             AVG(jangka_waktu_total) as rata_jangka_waktu,
             SUM(total_terbayar) as total_terbayar,
             SUM(total_cicilan)  as total_cicilan'
        )
            ->groupBy('metode_perhitungan')
            ->get()
            ->map(function ($row) {
                return [
                    'metode'           => $row->metode_perhitungan ?? '-',
                    'jumlah'           => (int) $row->jumlah,
                    'total_plafon'     => (float) $row->total_plafon,
                    'rata_jangka_waktu' => round((float) $row->rata_jangka_waktu, 1),
                    'total_terbayar'   => (float) $row->total_terbayar,
                    'total_cicilan'    => (float) $row->total_cicilan,
                    'persen_terbayar'  => $row->total_cicilan > 0
                        ? round(($row->total_terbayar / $row->total_cicilan) * 100, 1)
                        : 0.0,
                ];
            })->values()->toArray();
    }

    // =========================================================
    // CHART 5 – Distribusi Jenis Restrukturisasi
    // =========================================================

    public function getJenisRestrukturisasiData(): array
    {
        $allRecords = PengajuanCicilan::whereNotNull('jenis_restrukturisasi')->get();

        $counts = [];
        foreach ($allRecords as $record) {
            foreach ((array) ($record->jenis_restrukturisasi ?? []) as $type) {
                $type = trim((string) $type);
                if ($type !== '') {
                    $counts[$type] = ($counts[$type] ?? 0) + 1;
                }
            }
        }

        arsort($counts);

        return [
            'categories' => array_values(array_keys($counts)),
            'data'       => array_values($counts),
        ];
    }

    // =========================================================
    // CHART 6 – Angsuran per Debitur (Lunas vs Belum Lunas)
    // =========================================================

    /**
     * Data tren pengajuan untuk 1 bulan spesifik (format input: 'YYYY-MM').
     * Digunakan oleh chart Tren dengan filter per bulan.
     */
    public function getTrenBulanData(string $bulanTahun): array
    {
        [$tahun, $bulan] = explode('-', $bulanTahun);
        $m     = Carbon::create((int) $tahun, (int) $bulan, 1);
        $start = $m->copy()->startOfMonth();
        $end   = $m->copy()->endOfMonth();

        $label = self::BULAN_NAMA[$m->month] . ' ' . $m->year;

        return [
            'categories' => [$label],
            'masuk'      => [PengajuanCicilan::whereBetween('created_at', [$start, $end])->count()],
            'selesai'    => [PengajuanCicilan::where('status', 'Selesai')->whereBetween('updated_at', [$start, $end])->count()],
            'ditolak'    => [PengajuanCicilan::where('status', 'like', '%Ditolak%')->whereBetween('updated_at', [$start, $end])->count()],
        ];
    }

    public function getAngsuranPerDebiturData(): array
    {
        $categories = [];
        $lunas      = [];
        $belumLunas = [];

        $penyesuaian = PenyesuaianCicilan::with(['pengajuanCicilan', 'jadwalAngsuran'])->get();

        foreach ($penyesuaian as $p) {
            $nama = $p->pengajuanCicilan?->nama_perusahaan ?? 'Tidak Diketahui';
            $categories[] = mb_strlen($nama) > 18 ? mb_substr($nama, 0, 18) . '\u2026' : $nama;

            $angsuran     = $p->jadwalAngsuran;
            // Hitung angsuran yang sudah terbayar (Lunas atau Dibayar Sebagian)
            $lunas[]      = $angsuran->whereIn('status', ['Lunas', 'Dibayar Sebagian'])->count();
            $belumLunas[] = $angsuran->whereNotIn('status', ['Lunas', 'Dibayar Sebagian'])->count();
        }

        return compact('categories', 'lunas', 'belumLunas');
    }

    // =========================================================
    // TABLE – Monitoring Debitur
    // =========================================================

    public function getDebiturMonitoringData(): array
    {
        $today = Carbon::today();

        return PenyesuaianCicilan::with(['pengajuanCicilan', 'jadwalAngsuran'])
            ->get()
            ->map(function ($p) use ($today) {
                $angsuran     = $p->jadwalAngsuran;
                $total        = $angsuran->count();
                // Angsuran yang sudah terbayar (Lunas maupun Dibayar Sebagian)
                $lunasCount   = $angsuran->whereIn('status', ['Lunas', 'Dibayar Sebagian'])->count();
                $overdueCount = $angsuran
                    ->filter(fn ($a) => $a->tanggal_jatuh_tempo < $today
                        && ! in_array($a->status, ['Lunas', 'Dibayar Sebagian']))
                    ->count();

                $persen = (float) $p->total_cicilan > 0
                    ? round(((float) $p->total_terbayar / (float) $p->total_cicilan) * 100, 1)
                    : 0.0;

                if ($overdueCount > 0) {
                    $health = 'Belum Lunas'; $color = 'danger';
                } elseif ($persen >= 100) {
                    $health = 'Lunas'; $color = 'success';
                } elseif ($persen >= 50) {
                    $health = 'Berjalan'; $color = 'info';
                } else {
                    $health = 'Baru Mulai'; $color = 'secondary';
                }

                return [
                    'nama_perusahaan' => $p->pengajuanCicilan?->nama_perusahaan ?? '-',
                    'nomor_kontrak'   => $p->nomor_kontrak_restrukturisasi       ?? '-',
                    'total_cicilan'   => (float) $p->total_cicilan,
                    'total_terbayar'  => (float) $p->total_terbayar,
                    'total_sisa'      => max(0, (float) $p->total_cicilan - (float) $p->total_terbayar),
                    'persen_terbayar' => $persen,
                    'total_angsuran'  => $total,
                    'lunas_count'     => $lunasCount,
                    'overdue_count'   => $overdueCount,
                    'health'          => $health,
                    'health_color'    => $color,
                ];
            })->values()->toArray();
    }

    // =========================================================
    // HELPERS
    // =========================================================

    public function getMonthOptions(): array
    {
        return array_map(fn ($n) => self::BULAN_NAMA_FULL[$n], array_combine(
            array_map(fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT), range(1, 12)),
            range(1, 12)
        ));
    }

    public function getYearOptions(int $past = 5, int $future = 1): array
    {
        $current = (int) date('Y');
        $years   = [];
        for ($y = $current - $past; $y <= $current + $future; $y++) {
            $years[$y] = (string) $y;
        }
        return $years;
    }
}
