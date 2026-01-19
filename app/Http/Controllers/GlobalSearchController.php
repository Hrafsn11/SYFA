<?php

namespace App\Http\Controllers;

use App\Models\MasterDebiturDanInvestor;
use App\Models\PengajuanPeminjaman;
use App\Models\PengajuanInvestasi;
use App\Models\PeminjamanFinlog;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $results = [
            'pages' => [],
            'debitur' => [],
            'pengajuan_peminjaman' => [],
            'pengajuan_investasi' => [],
            'sfinlog_peminjaman' => [],
        ];

        if ($q !== '') {
            // Search Pages/Routes - prioritized first
            $results['pages'] = $this->searchPages($q);
            // Debitur & Investor Master
            $results['debitur'] = MasterDebiturDanInvestor::query()
                ->where(function ($w) use ($q) {
                    $w->where('nama', 'like', "%{$q}%")
                        ->orWhere('nama_ceo', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('no_telepon', 'like', "%{$q}%");
                })
                ->limit(10)
                ->get()
                ->map(function ($row) {
                    return [
                        'title' => $row->nama,
                        'subtitle' => $row->nama_ceo ? 'CEO: ' . $row->nama_ceo : null,
                        'url' => route('master-data.debitur-investor.edit', ['id' => $row->id_debitur]),
                    ];
                })
                ->all();

            // Pengajuan Peminjaman (search by nomor/no_kontrak or debitur name)
            $results['pengajuan_peminjaman'] = PengajuanPeminjaman::query()
                ->with('debitur')
                ->where(function ($w) use ($q) {
                    $w->where('nomor_peminjaman', 'like', "%{$q}%")
                        ->orWhere('no_kontrak', 'like', "%{$q}%")
                        ->orWhereHas('debitur', function ($d) use ($q) {
                            $d->where('nama', 'like', "%{$q}%");
                        });
                })
                ->limit(10)
                ->get()
                ->map(function ($row) {
                    $title = $row->nomor_peminjaman ?: ($row->no_kontrak ?: ('Pengajuan #' . $row->id_pengajuan_peminjaman));
                    return [
                        'title' => $title,
                        'subtitle' => $row->debitur->nama ?? null,
                        'url' => route('peminjaman.detail', ['id' => $row->id_pengajuan_peminjaman]),
                    ];
                })
                ->all();

            // Pengajuan Investasi
            $results['pengajuan_investasi'] = PengajuanInvestasi::query()
                ->where(function ($w) use ($q) {
                    $w->where('nama_investor', 'like', "%{$q}%")
                        ->orWhere('nomor_kontrak', 'like', "%{$q}%");
                })
                ->limit(10)
                ->get()
                ->map(function ($row) {
                    $title = $row->nomor_kontrak ?: ('Investasi #' . $row->id_pengajuan_investasi);
                    return [
                        'title' => $title,
                        'subtitle' => $row->nama_investor,
                        'url' => route('pengajuan-investasi.show', ['id' => $row->id_pengajuan_investasi]),
                    ];
                })
                ->all();

            // SFinlog Peminjaman
            if (class_exists(PeminjamanFinlog::class)) {
                $results['sfinlog_peminjaman'] = PeminjamanFinlog::query()
                    ->with('debitur')
                    ->where(function ($w) use ($q) {
                        $w->where('nomor_peminjaman', 'like', "%{$q}%")
                            ->orWhere('nama_project', 'like', "%{$q}%")
                            ->orWhereHas('debitur', function ($d) use ($q) {
                                $d->where('nama', 'like', "%{$q}%");
                            });
                    })
                    ->limit(10)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'title' => $row->nomor_peminjaman ?: ($row->nama_project ?: ('SF Peminjaman #' . $row->id_peminjaman_finlog)),
                            'subtitle' => $row->debitur->nama ?? null,
                            'url' => route('sfinlog.peminjaman.detail', ['id' => $row->id_peminjaman_finlog]),
                        ];
                    })
                    ->all();
            }
        }

        // Flatten total count for summary
        $total = collect($results)->flatten(1)->count();

        return view('search.results', [
            'q' => $q,
            'results' => $results,
            'total' => $total,
        ]);
    }

    /**
     * API endpoint for typeahead autocomplete
     */
    public function api(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([
                'pages' => [],
                'debitur' => [],
                'pengajuan_peminjaman' => [],
                'pengajuan_investasi' => [],
                'sfinlog_peminjaman' => [],
            ]);
        }

        $data = [
            'pages' => $this->searchPages($q),
            'debitur' => $this->searchDebitur($q),
            'pengajuan_peminjaman' => $this->searchPengajuanPeminjaman($q),
            'pengajuan_investasi' => $this->searchPengajuanInvestasi($q),
            'sfinlog_peminjaman' => $this->searchSFinlogPeminjaman($q),
        ];

        return response()->json($data);
    }

    /**
     * Search application pages/routes
     */
    private function searchPages(string $q): array
    {
        $pages = [
            // Dashboard
            ['title' => 'Dashboard', 'route' => 'dashboard.index', 'keywords' => ['dashboard', 'beranda', 'home']],
            ['title' => 'Dashboard Pembiayaan SFinance', 'route' => 'sfinance.dashboard.pembiayaan', 'keywords' => ['dashboard', 'pembiayaan', 'sfinance']],
            ['title' => 'Dashboard Investasi Deposito', 'route' => 'sfinance.dashboard.investasi-deposito', 'keywords' => ['dashboard', 'investasi', 'deposito']],

            // Peminjaman
            ['title' => 'Peminjaman Dana', 'route' => 'peminjaman.index', 'keywords' => ['peminjaman', 'dana', 'pinjaman', 'loan']],
            ['title' => 'AR Perbulan', 'route' => 'ar-perbulan.index', 'keywords' => ['ar', 'account receivable', 'piutang', 'perbulan']],
            ['title' => 'AR Performance', 'route' => 'ar-performance.index', 'keywords' => ['ar', 'performance', 'kinerja', 'piutang']],

            // Restrukturisasi
            ['title' => 'Pengajuan Restrukturisasi', 'route' => 'pengajuan-restrukturisasi.index', 'keywords' => ['pengajuan', 'restrukturisasi', 'restructuring']],
            ['title' => 'Program Restrukturisasi', 'route' => 'program-restrukturisasi.index', 'keywords' => ['program', 'restrukturisasi', 'restructuring']],

            // Pengembalian
            ['title' => 'Pengembalian Dana', 'route' => 'pengembalian.index', 'keywords' => ['pengembalian', 'dana', 'return', 'payment']],
            ['title' => 'Debitur Piutang', 'route' => 'debitur-piutang.index', 'keywords' => ['debitur', 'piutang', 'receivable', 'debtor']],
            ['title' => 'Report Pengembalian', 'route' => 'report-pengembalian.index', 'keywords' => ['report', 'laporan', 'pengembalian']],

            // Investasi
            ['title' => 'Pengajuan Investasi', 'route' => 'pengajuan-investasi.index', 'keywords' => ['pengajuan', 'investasi', 'investment']],
            ['title' => 'Aset Investasi', 'route' => 'penyaluran-deposito.index', 'keywords' => ['aset', 'investasi', 'penyaluran', 'deposito']],
            ['title' => 'Kertas Kerja Investor SFinance', 'route' => 'sfinance.kertas-kerja-investor-sfinance.index', 'keywords' => ['kertas', 'kerja', 'investor', 'sfinance']],
            ['title' => 'Pengembalian Investasi', 'route' => 'pengembalian-investasi.index', 'keywords' => ['pengembalian', 'investasi', 'return']],

            // Master Data
            ['title' => 'Master KOL', 'route' => 'master-data.kol.index', 'keywords' => ['master', 'kol', 'kolektibilitas']],
            ['title' => 'Sumber Pendanaan Eksternal', 'route' => 'master-data.sumber-pendanaan-eksternal.index', 'keywords' => ['sumber', 'pendanaan', 'eksternal', 'external', 'funding']],
            ['title' => 'Master Debitur dan Investor', 'route' => 'master-data.debitur-investor.index', 'keywords' => ['master', 'debitur', 'investor', 'debtor']],
            ['title' => 'Master Cells Project', 'route' => 'master-data.cells-project.index', 'keywords' => ['master', 'cells', 'project', 'proyek']],

            // Configuration
            ['title' => 'Config Matrix Pinjaman', 'route' => 'config-matrix-pinjaman.index', 'keywords' => ['config', 'matrix', 'pinjaman', 'konfigurasi']],
            ['title' => 'Config Matrix Score', 'route' => 'matrixscore', 'keywords' => ['config', 'matrix', 'score', 'konfigurasi', 'nilai']],

            // Access Control
            ['title' => 'Users Management', 'route' => 'users.index', 'keywords' => ['users', 'user', 'pengguna', 'management']],
            ['title' => 'Roles Management', 'route' => 'roles.index', 'keywords' => ['roles', 'role', 'peran', 'management']],
            ['title' => 'Permissions Management', 'route' => 'permissions.index', 'keywords' => ['permissions', 'permission', 'izin', 'akses', 'management']],

            // SFinlog
            ['title' => 'SFinlog - Pengajuan Investasi', 'route' => 'sfinlog.pengajuan-investasi.index', 'keywords' => ['sfinlog', 'pengajuan', 'investasi']],
            ['title' => 'SFinlog - Peminjaman', 'route' => 'sfinlog.peminjaman.index', 'keywords' => ['sfinlog', 'peminjaman', 'pinjaman']],
            ['title' => 'SFinlog - Pengembalian Pinjaman', 'route' => 'sfinlog.pengembalian-pinjaman.index', 'keywords' => ['sfinlog', 'pengembalian', 'pinjaman']],
            ['title' => 'SFinlog - AR Perbulan', 'route' => 'sfinlog.ar-perbulan.index', 'keywords' => ['sfinlog', 'ar', 'perbulan']],
        ];

        $qLower = mb_strtolower($q);
        $matched = [];

        foreach ($pages as $page) {
            // Check if query matches title or any keyword
            $titleMatch = mb_stripos(mb_strtolower($page['title']), $qLower) !== false;
            $keywordMatch = false;

            foreach ($page['keywords'] as $keyword) {
                if (mb_stripos($keyword, $qLower) !== false || mb_stripos($qLower, $keyword) !== false) {
                    $keywordMatch = true;
                    break;
                }
            }

            if ($titleMatch || $keywordMatch) {
                // Check if route exists
                try {
                    $url = route($page['route']);
                    $matched[] = [
                        'name' => $page['title'],
                        'title' => $page['title'],
                        'icon' => $this->getIconForPage($page['title']),
                        'subtitle' => null,
                        'url' => $url,
                    ];
                } catch (\Exception $e) {
                    // Route doesn't exist or not accessible, skip
                    continue;
                }
            }
        }

        return array_slice($matched, 0, 10);
    }

    /**
     * Search debitur dan investor
     */
    private function searchDebitur(string $q): array
    {
        return MasterDebiturDanInvestor::query()
            ->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                    ->orWhere('nama_ceo', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('no_telepon', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'name' => $row->nama,
                    'subtitle' => $row->nama_ceo ? 'CEO: ' . $row->nama_ceo : null,
                    'url' => route('master-data.debitur-investor.edit', ['id' => $row->id_debitur]),
                ];
            })
            ->all();
    }

    /**
     * Search pengajuan peminjaman
     */
    private function searchPengajuanPeminjaman(string $q): array
    {
        return PengajuanPeminjaman::query()
            ->with('debitur')
            ->where(function ($w) use ($q) {
                $w->where('nomor_peminjaman', 'like', "%{$q}%")
                    ->orWhere('no_kontrak', 'like', "%{$q}%")
                    ->orWhereHas('debitur', function ($d) use ($q) {
                        $d->where('nama', 'like', "%{$q}%");
                    });
            })
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'name' => $row->nomor_peminjaman ?: ($row->no_kontrak ?: ('Pengajuan #' . $row->id_pengajuan_peminjaman)),
                    'subtitle' => $row->debitur->nama ?? null,
                    'url' => route('peminjaman.detail', ['id' => $row->id_pengajuan_peminjaman]),
                ];
            })
            ->all();
    }

    /**
     * Search pengajuan investasi
     */
    private function searchPengajuanInvestasi(string $q): array
    {
        return PengajuanInvestasi::query()
            ->where(function ($w) use ($q) {
                $w->where('nama_investor', 'like', "%{$q}%")
                    ->orWhere('nomor_kontrak', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'name' => $row->nomor_kontrak ?: ('Investasi #' . $row->id_pengajuan_investasi),
                    'subtitle' => $row->nama_investor,
                    'url' => route('pengajuan-investasi.show', ['id' => $row->id_pengajuan_investasi]),
                ];
            })
            ->all();
    }

    /**
     * Search SFinlog peminjaman
     */
    private function searchSFinlogPeminjaman(string $q): array
    {
        if (!class_exists(PeminjamanFinlog::class)) {
            return [];
        }

        return PeminjamanFinlog::query()
            ->with('debitur')
            ->where(function ($w) use ($q) {
                $w->where('nomor_peminjaman', 'like', "%{$q}%")
                    ->orWhere('nama_project', 'like', "%{$q}%")
                    ->orWhereHas('debitur', function ($d) use ($q) {
                        $d->where('nama', 'like', "%{$q}%");
                    });
            })
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'name' => $row->nomor_peminjaman ?: ($row->nama_project ?: ('SF Peminjaman #' . $row->id_peminjaman_finlog)),
                    'subtitle' => $row->debitur->nama ?? null,
                    'url' => route('sfinlog.peminjaman.detail', ['id' => $row->id_peminjaman_finlog]),
                ];
            })
            ->all();
    }

    /**
     * Get icon for page based on title
     */
    private function getIconForPage(string $title): string
    {
        $titleLower = mb_strtolower($title);

        if (str_contains($titleLower, 'dashboard'))
            return 'ti-smart-home';
        if (str_contains($titleLower, 'peminjaman') || str_contains($titleLower, 'pinjaman'))
            return 'ti-briefcase';
        if (str_contains($titleLower, 'investasi'))
            return 'ti-coin';
        if (str_contains($titleLower, 'restrukturisasi'))
            return 'ti-file-text';
        if (str_contains($titleLower, 'pengembalian'))
            return 'ti-wallet';
        if (str_contains($titleLower, 'ar '))
            return 'ti-chart-line';
        if (str_contains($titleLower, 'debitur') || str_contains($titleLower, 'investor'))
            return 'ti-users';
        if (str_contains($titleLower, 'master') || str_contains($titleLower, 'kol'))
            return 'ti-database';
        if (str_contains($titleLower, 'config'))
            return 'ti-settings';
        if (str_contains($titleLower, 'users') || str_contains($titleLower, 'roles') || str_contains($titleLower, 'permissions'))
            return 'ti-lock';

        return 'ti-file';
    }
}
