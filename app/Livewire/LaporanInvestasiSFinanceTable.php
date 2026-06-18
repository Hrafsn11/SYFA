<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasi;
use Carbon\Carbon;

class LaporanInvestasiSFinanceTable extends DataTableComponent
{
    protected $model = PengajuanInvestasi::class;

    public $year;
    public $globalSearch = '';
    public $filterStatus = '';

    protected $listeners = [
        'refreshLaporanInvestasiTable' => '$refresh',
        'yearChanged'                  => 'setYear',
        'globalSearchChanged'          => 'setGlobalSearch',
        'statusFilterChanged'          => 'setStatusFilter',
    ];

    public function setStatusFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function mount($year = null, $filterStatus = null): void
    {
        $this->year         = $year ?? '';
        $this->filterStatus = $filterStatus ?? '';
    }

    public function setYear($year)
    {
        $this->year = $year;
        $this->resetPage();
    }

    public function setGlobalSearch($search)
    {
        $this->globalSearch = $search;
        $this->resetPage();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi')
            ->setSearchDisabled()
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setTableAttributes(['class' => 'table table-hover align-middle laporan-tabel', 'id' => 'laporan-investasi-tabel'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setPerPageFieldAttributes(['class' => 'form-select form-select-sm'])
            ->setFiltersDisabled()
            ->setBulkActionsDisabled()
            ->setColumnSelectDisabled();
    }

    public function builder(): Builder
    {
        $query = PengajuanInvestasi::query()
            ->select([
                'id_pengajuan_investasi',
                'tanggal_investasi',
                'jenis_investasi',
                'nama_investor',
                'jumlah_investasi',
                'lama_investasi',
                'bunga_pertahun',
                'nominal_bunga_yang_didapatkan',
                'sisa_pokok',
                'sisa_bunga',
                'status',
                'nomor_kontrak',
            ])
            ->where(function ($q) {
                $q->whereNotNull('nomor_kontrak')
                    ->where('nomor_kontrak', '!=', '')
                    ->orWhereHas('penyaluranDanaInvestasi');
            });

        $user = auth()->user();
        $hasUnrestrictedRole = false;

        if ($user) {
            if ($user->hasRole('super-admin')) {
                $hasUnrestrictedRole = true;
            } else {
                $roles = $user->roles;
                $hasUnrestrictedRole = $roles->contains(function ($role) {
                    return $role->restriction == 1;
                });
            }
        }

        if (!$hasUnrestrictedRole) {
            $debiturInvestor = $user ? $user->debitur : null;
            if ($debiturInvestor) {
                $query->where('pengajuan_investasi.id_debitur_dan_investor', $debiturInvestor->id_debitur);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        if (!empty($this->globalSearch)) {
            $search = $this->globalSearch;
            $query->where(function ($q) use ($search) {
                $q->where('nama_investor', 'like', '%' . $search . '%')
                    ->orWhere('nomor_kontrak', 'like', '%' . $search . '%')
                    ->orWhere('jenis_investasi', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        if (!empty($this->year)) {
            $query->whereYear('tanggal_investasi', $this->year);
        }

        return $query;
    }

    /** Resolve the display year (fallback to current year when filter is empty) */
    private function displayYear(): int
    {
        return !empty($this->year) ? (int) $this->year : (int) date('Y');
    }

    /** Calculated data (Table1 extras + Table3) */
    private function getRowData($row): array
    {
        $year = $this->displayYear();
        $id   = $row->id_pengajuan_investasi;

        $bungaPerBulan = $row->bunga_pertahun / 12;
        $cofBulan      = ($row->jumlah_investasi * $bungaPerBulan) / 100;

        $tanggalMulai      = Carbon::parse($row->tanggal_investasi);
        $tanggalAkhirPeriode = Carbon::create($year, 12, 31);

        // CoF akhir periode
        if ($tanggalMulai->year > $year) {
            $cofAkhirPeriode = 0;
        } else {
            $now        = Carbon::now();
            $tanggalBatas = $tanggalAkhirPeriode->lt($now) ? $tanggalAkhirPeriode : $now;
            $bulanBerjalan = max(0, $tanggalMulai->diffInMonths($tanggalBatas) + 1);
            $totalSeharusnya = $cofBulan * $bulanBerjalan;
            $totalDibayar    = DB::table('pengembalian_investasi')
                ->where('id_pengajuan_investasi', $id)
                ->where('tanggal_pengembalian', '<=', $tanggalBatas)
                ->sum('bunga_dibayar');
            $cofAkhirPeriode = max(0, $totalSeharusnya - $totalDibayar);
        }

        // Last tanggal pengembalian
        $tglTerakhir = DB::table('pengembalian_investasi')
            ->where('id_pengajuan_investasi', $id)
            ->max('tanggal_pengembalian');

        // CoF per bulan (only within active investment window)
        $tanggalJatuhTempo = $tanggalMulai->copy()->addMonths((int) $row->lama_investasi);
        $cofPerBulan = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $startOfMonth = Carbon::create($year, $bulan, 1);
            $endOfMonth   = $startOfMonth->copy()->endOfMonth();
            $isAktif      = $tanggalMulai->lte($endOfMonth) && $tanggalJatuhTempo->gte($startOfMonth);
            $cofPerBulan[$bulan] = $isAktif ? $cofBulan : 0;
        }

        // Pengembalian totals
        $total = DB::table('pengembalian_investasi')
            ->selectRaw('SUM(dana_pokok_dibayar) as total_pokok, SUM(bunga_dibayar) as total_bunga')
            ->where('id_pengajuan_investasi', $id)
            ->first();

        return [
            'bunga_per_bulan'      => $bungaPerBulan,
            'cof_bulan'            => $cofBulan,
            'cof_akhir_periode'    => $cofAkhirPeriode,
            'tgl_pengembalian'     => $tglTerakhir,
            'cof_per_bulan'        => $cofPerBulan,
            'pengembalian_pokok'   => $total->total_pokok ?? 0,
            'pengembalian_bunga'   => $total->total_bunga ?? 0,
        ];
    }

    private static $bulanLabel = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    public function columns(): array
    {
        $rowNumber = 0;

        $cols = [
            // ── TABLE 1 COLUMNS ─────────────────────────────────────────────

            Column::make('No')
                ->label(function ($row) use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;
                    return '<div class="text-center">' . $number . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('Tanggal Uang Masuk', 'tanggal_investasi')
                ->sortable()
                ->label(fn($row) =>
                    '<div class="text-center">' . Carbon::parse($row->tanggal_investasi)->format('d-m-Y') . '</div>'
                )
                ->html(),

            Column::make('Jenis Investasi', 'jenis_investasi')
                ->sortable()
                ->label(fn($row) =>
                    '<div class="text-center">' . ($row->jenis_investasi ?? '-') . '</div>'
                )
                ->html(),

            Column::make('Deposan', 'nama_investor')
                ->sortable()
                ->label(fn($row) =>
                    '<div class="text-center">' . e($row->nama_investor) . '</div>'
                )
                ->html(),

            Column::make('Nominal Deposit', 'jumlah_investasi')
                ->sortable()
                ->label(fn($row) =>
                    '<div class="text-center">Rp ' . number_format($row->jumlah_investasi, 0, ',', '.') . '</div>'
                )
                ->html(),

            Column::make('Lama Investasi', 'lama_investasi')
                ->sortable()
                ->label(fn($row) =>
                    '<div class="text-center">' . $row->lama_investasi . ' Bulan</div>'
                )
                ->html(),

            Column::make('Bunga (%PA)', 'bunga_pertahun')
                ->sortable()
                ->label(fn($row) =>
                    '<div class="text-center">' . number_format($row->bunga_pertahun, 2) . '%</div>'
                )
                ->html(),

            Column::make('Bunga Nominal', 'nominal_bunga_yang_didapatkan')
                ->sortable()
                ->label(fn($row) =>
                    '<div class="text-center">Rp ' . number_format($row->nominal_bunga_yang_didapatkan, 0, ',', '.') . '</div>'
                )
                ->html(),

            Column::make('Bunga (%Bulan)')
                ->label(function ($row) {
                    $d = $this->getRowData($row);
                    return '<div class="text-center">' . number_format($d['bunga_per_bulan'], 2) . '%</div>';
                })
                ->html(),

            Column::make('Bunga (COF/Bulan)')
                ->label(function ($row) {
                    $d = $this->getRowData($row);
                    return '<div class="text-center">Rp ' . number_format($d['cof_bulan'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('CoF Per Akhir Des')
                ->label(function ($row) {
                    $d = $this->getRowData($row);
                    return '<div class="text-center">Rp ' . number_format($d['cof_akhir_periode'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->label(function ($row) {
                    $badge = $row->status === 'Lunas'
                        ? '<span class="badge bg-label-success">Lunas</span>'
                        : '<span class="badge bg-label-warning">Aktif</span>';
                    return '<div class="text-center">' . $badge . '</div>';
                })
                ->html(),

            Column::make('Tgl Pengembalian')
                ->label(function ($row) {
                    $d   = $this->getRowData($row);
                    $tgl = $d['tgl_pengembalian'];
                    return '<div class="text-center">' . ($tgl ? Carbon::parse($tgl)->format('d-m-Y') : '-') . '</div>';
                })
                ->html(),
        ];

        // ── TABLE 2 COLUMNS: CoF per bulan ───────────────────────────────
        foreach (range(1, 12) as $bulan) {
            $label = self::$bulanLabel[$bulan];
            $cols[] = Column::make($label)
                ->label(function ($row) use ($bulan) {
                    $d   = $this->getRowData($row);
                    $val = $d['cof_per_bulan'][$bulan];
                    $html = $val > 0
                        ? 'Rp ' . number_format($val, 0, ',', '.')
                        : '<span class="text-muted">-</span>';
                    return '<div class="text-center">' . $html . '</div>';
                })
                ->html();
        }

        // ── TABLE 3 COLUMNS ──────────────────────────────────────────────
        $cols[] = Column::make('Pengembalian Pokok')
            ->label(function ($row) {
                $d = $this->getRowData($row);
                return '<div class="text-center">Rp ' . number_format($d['pengembalian_pokok'], 0, ',', '.') . '</div>';
            })
            ->html();

        $cols[] = Column::make('Pengembalian Bunga')
            ->label(function ($row) {
                $d = $this->getRowData($row);
                return '<div class="text-center">Rp ' . number_format($d['pengembalian_bunga'], 0, ',', '.') . '</div>';
            })
            ->html();

        $cols[] = Column::make('Sisa Pokok', 'sisa_pokok')
            ->sortable()
            ->label(fn($row) =>
                '<div class="text-center"><strong class="text-danger">Rp ' . number_format($row->sisa_pokok, 0, ',', '.') . '</strong></div>'
            )
            ->html();

        $cols[] = Column::make('Sisa Bunga', 'sisa_bunga')
            ->sortable()
            ->label(fn($row) =>
                '<div class="text-center"><strong class="text-danger">Rp ' . number_format($row->sisa_bunga, 0, ',', '.') . '</strong></div>'
            )
            ->html();

        $cols[] = Column::make('Total Belum Dikembalikan')
            ->label(fn($row) =>
                '<div class="text-center"><strong class="text-danger">Rp ' . number_format($row->sisa_pokok + $row->sisa_bunga, 0, ',', '.') . '</strong></div>'
            )
            ->html();

        return $cols;
    }
}
