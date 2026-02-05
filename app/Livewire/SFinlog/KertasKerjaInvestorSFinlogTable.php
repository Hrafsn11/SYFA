<?php

namespace App\Livewire\SFinlog;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasiFinlog;
use Carbon\Carbon;

class KertasKerjaInvestorSFinlogTable extends DataTableComponent
{
    protected $model = PengajuanInvestasiFinlog::class;

    public $year;

    protected $listeners = [
        'refreshKertasKerjaTable' => '$refresh',
    ];

    public function mount(): void
    {
        $this->year = request()->get('year', date('Y'));
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi_finlog')
            ->setSearchEnabled()
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setTableAttributes(['class' => 'table border-top'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(true)
            ->setBulkActionsDisabled()
            ->setColumnSelectDisabled()
            ->setSearchPlaceholder('Cari nama deposan, nomor kontrak, nominal...');
    }

    /**
     * Custom search for flexible searching
     */
    public function applySearch(): Builder
    {
        $searchTerm = $this->getSearch();

        if (empty($searchTerm)) {
            return $this->builder();
        }

        // Clean search term - remove "Rp", dots, spaces for nominal search
        $cleanedSearch = preg_replace('/[Rp\s\.]/i', '', $searchTerm);
        $isNumericSearch = is_numeric($cleanedSearch) && strlen($cleanedSearch) > 0;

        return $this->builder()->where(function ($query) use ($searchTerm, $cleanedSearch, $isNumericSearch) {
            // Search by nama_investor
            $query->where('nama_investor', 'like', '%' . $searchTerm . '%');

            // Search by nomor_kontrak
            $query->orWhere('nomor_kontrak', 'like', '%' . $searchTerm . '%');

            // Search by status
            $query->orWhere('status', 'like', '%' . $searchTerm . '%');

            // Search by tanggal_investasi (format: dd-mm-yyyy or yyyy)
            $query->orWhere('tanggal_investasi', 'like', '%' . $searchTerm . '%');

            // If numeric, also search by nominal_investasi
            if ($isNumericSearch) {
                $query->orWhere('nominal_investasi', 'like', '%' . $cleanedSearch . '%');
                $query->orWhere('lama_investasi', '=', $cleanedSearch);
            }
        });
    }

    public function filters(): array
    {
        // Use string keys with prefix to prevent numeric index issues
        $years = ['' => 'Semua Tahun'];
        for ($y = date('Y'); $y >= date('Y') - 10; $y--) {
            $years['Y-' . $y] = (string) $y;
        }

        return [
            SelectFilter::make('Tahun', 'year')
                ->options($years)
                ->setFilterDefaultValue('')
                ->filter(function (Builder $builder, string $value) {
                    // Extract year from 'Y-2025' format
                    if (!empty($value) && str_starts_with($value, 'Y-')) {
                        $yearValue = (int) str_replace('Y-', '', $value);
                        $this->year = $yearValue;
                        // Filter by year of tanggal_investasi
                        $builder->whereYear('tanggal_investasi', $yearValue);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        return PengajuanInvestasiFinlog::query()
            ->select([
                'id_pengajuan_investasi_finlog',
                'tanggal_investasi',
                'nama_investor',
                'nominal_investasi',
                'lama_investasi',
                'persentase_bagi_hasil',
                'nominal_bagi_hasil_yang_didapat',
                'status',
                'nomor_kontrak',
                'sisa_pokok',
                'sisa_bagi_hasil'
            ])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '');
    }

    /**
     * Get calculated data for a row (from Table1)
     */
    private function getCalculatedData($row): array
    {
        // Bagi hasil (nominal/pa) = bagi hasil(%pa) × nominal deposito / 100
        $bagiHasilNominalPa = ($row->persentase_bagi_hasil * $row->nominal_investasi) / 100;

        // Bagi hasil (%bulan) = bagi hasil(%pa) / 12
        $bagiHasilPerBulan = $row->persentase_bagi_hasil / 12;

        // Bagi hasil (COF/bulan) = bagi hasil (nominal/pa) / 12
        $cofBulan = $bagiHasilNominalPa / 12;

        // Bagi hasil per nominal = lama deposito(bulan) × bagi hasil(COF/bulan)
        $bagiHasilPerNominal = $row->lama_investasi * $cofBulan;

        return [
            'bagi_hasil_nominal_pa' => $bagiHasilNominalPa,
            'bagi_hasil_per_bulan' => $bagiHasilPerBulan,
            'cof_bulan' => $cofBulan,
            'bagi_hasil_per_nominal' => $bagiHasilPerNominal,
        ];
    }

    /**
     * Get CoF per bulan for a row (from Table2)
     */
    private function getCofPerBulan($row): array
    {
        $year = $this->year;

        // Bagi hasil (nominal/pa) = bagi hasil(%pa) × nominal deposito / 100
        $bagiHasilNominalPa = ($row->persentase_bagi_hasil * $row->nominal_investasi) / 100;

        // Bagi hasil (COF/bulan) = bagi hasil (nominal/pa) / 12
        $cofBulan = $bagiHasilNominalPa / 12;

        $tanggalMulai = Carbon::parse($row->tanggal_investasi);
        $tanggalJatuhTempo = Carbon::parse($row->tanggal_investasi)->addMonths($row->lama_investasi);

        $cofPerBulan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $tanggalBulanIni = Carbon::create($year, $bulan, 1);
            $tanggalAkhirBulanIni = $tanggalBulanIni->copy()->endOfMonth();

            // Cek apakah investasi aktif di bulan ini
            $isAktif = $tanggalMulai->lte($tanggalAkhirBulanIni) && $tanggalJatuhTempo->gte($tanggalBulanIni);
            $cofPerBulan[$bulan] = $isAktif ? $cofBulan : 0;
        }

        return $cofPerBulan;
    }

    /**
     * Get pengembalian data for a row (from Table3)
     */
    private function getPengembalianData($row): array
    {
        $id = $row->id_pengajuan_investasi_finlog;

        // Get total pengembalian
        $total = DB::table('pengembalian_investasi_finlog')
            ->select([
                DB::raw('SUM(dana_pokok_dibayar) as total_pokok_all'),
                DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil_all')
            ])
            ->where('id_pengajuan_investasi_finlog', $id)
            ->first();

        return [
            'pengembalian_pokok' => $total->total_pokok_all ?? 0,
            'pengembalian_bagi_hasil' => $total->total_bagi_hasil_all ?? 0,
        ];
    }

    public function columns(): array
    {
        $rowNumber = 0;

        return [
            // ===== TABLE 1 COLUMNS (Info Dasar) =====
            Column::make('No')
                ->label(function ($row) use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;
                    return '<div class="text-center">' . $number . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            // EDITABLE
            Column::make('Tanggal Uang Masuk', 'tanggal_investasi')
                ->sortable()
                ->searchable()
                ->label(function ($row) {
                    $value = Carbon::parse($row->tanggal_investasi)->format('d-m-Y');
                    $id = $row->id_pengajuan_investasi_finlog;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'tanggal_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            // EDITABLE
            Column::make('Deposan', 'nama_investor')
                ->sortable()
                ->searchable()
                ->label(function ($row) {
                    $value = $row->nama_investor;
                    $id = $row->id_pengajuan_investasi_finlog;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'nama_investor\'})"></i>
                    </div>';
                })
                ->html(),

            // EDITABLE
            Column::make('Nominal Deposito', 'nominal_investasi')
                ->sortable()
                ->label(function ($row) {
                    $value = 'Rp ' . number_format($row->nominal_investasi, 0, ',', '.');
                    $id = $row->id_pengajuan_investasi_finlog;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'nominal_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            // EDITABLE
            Column::make('Lama Deposito', 'lama_investasi')
                ->sortable()
                ->label(function ($row) {
                    $value = $row->lama_investasi . ' Bulan';
                    $id = $row->id_pengajuan_investasi_finlog;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'lama_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            // EDITABLE
            Column::make('Bagi Hasil (%PA)', 'persentase_bagi_hasil')
                ->sortable()
                ->label(function ($row) {
                    $value = number_format($row->persentase_bagi_hasil, 2) . '%';
                    $id = $row->id_pengajuan_investasi_finlog;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'persentase_bagi_hasil\'})"></i>
                    </div>';
                })
                ->html(),

            // Calculated - NO EDIT
            Column::make('Bagi Hasil (Nominal/PA)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['bagi_hasil_nominal_pa'], 0, ',', '.') . '</div>';
                })
                ->html(),

            // Calculated - NO EDIT
            Column::make('Bagi Hasil Per Nominal')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['bagi_hasil_per_nominal'], 0, ',', '.') . '</div>';
                })
                ->html(),

            // Calculated - NO EDIT
            Column::make('Bagi Hasil (%Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">' . number_format($calc['bagi_hasil_per_bulan'], 2) . '%</div>';
                })
                ->html(),

            // Calculated - NO EDIT
            Column::make('Bagi Hasil (COF/Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['cof_bulan'], 0, ',', '.') . '</div>';
                })
                ->html(),

            // EDITABLE
            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->label(function ($row) {
                    $value = $row->status;
                    $id = $row->id_pengajuan_investasi_finlog;
                    $badge = $value === 'Lunas'
                        ? '<span class="badge bg-label-success">Lunas</span>'
                        : '<span class="badge bg-label-warning">Aktif</span>';
                    return '<div class="text-center editable-cell">' . $badge . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'status\'})"></i>
                    </div>';
                })
                ->html(),

            // ===== TABLE 2 COLUMNS (COF Per Bulan) =====
            Column::make('Jan')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[1], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Feb')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[2], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Mar')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[3], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Apr')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[4], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Mei')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[5], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Jun')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[6], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Jul')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[7], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Agu')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[8], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sep')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[9], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Okt')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[10], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Nov')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[11], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Des')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[12], 0, ',', '.') . '</div>';
                })
                ->html(),

            // ===== TABLE 3 COLUMNS (Pengembalian) =====
            Column::make('Pengembalian Pokok Deposito')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center">Rp ' . number_format($data['pengembalian_pokok'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Pengembalian Bagi Hasil Deposito')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center">Rp ' . number_format($data['pengembalian_bagi_hasil'], 0, ',', '.') . '</div>';
                })
                ->html(),

            // EDITABLE - From database column
            Column::make('Sisa Pokok Belum Dikembalikan', 'sisa_pokok')
                ->sortable()
                ->label(function ($row) {
                    $value = 'Rp ' . number_format($row->sisa_pokok ?? 0, 0, ',', '.');
                    $id = $row->id_pengajuan_investasi_finlog;
                    return '<div class="text-center editable-cell"><strong class="text-danger">' . $value . '</strong>
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'sisa_pokok\'})"></i>
                    </div>';
                })
                ->html(),

            // EDITABLE - From database column
            Column::make('Sisa Bagi Hasil Belum Dikembalikan', 'sisa_bagi_hasil')
                ->sortable()
                ->label(function ($row) {
                    $value = 'Rp ' . number_format($row->sisa_bagi_hasil ?? 0, 0, ',', '.');
                    $id = $row->id_pengajuan_investasi_finlog;
                    return '<div class="text-center editable-cell"><strong class="text-danger">' . $value . '</strong>
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'sisa_bagi_hasil\'})"></i>
                    </div>';
                })
                ->html(),

            // Calculated from database columns
            Column::make('Total Belum Dikembalikan')
                ->label(function ($row) {
                    $total = ($row->sisa_pokok ?? 0) + ($row->sisa_bagi_hasil ?? 0);
                    return '<div class="text-center"><strong class="text-danger">Rp ' . number_format($total, 0, ',', '.') . '</strong></div>';
                })
                ->html(),
        ];
    }
}
