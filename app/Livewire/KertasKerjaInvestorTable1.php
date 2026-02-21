<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasi;
use Carbon\Carbon;

class KertasKerjaInvestorTable1 extends DataTableComponent
{
    protected $model = PengajuanInvestasi::class;

    public $year;
    public $globalSearch = '';

    protected $listeners = ['refreshKertasKerjaTable' => '$refresh', 'yearChanged' => 'setYear', 'globalSearchChanged' => 'setGlobalSearch'];

    public function mount(): void
    {
        $this->year = request()->get('year', date('Y'));
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
            ->setTableAttributes(['class' => 'table border-top'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
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
                'nomor_kontrak'
            ])
            ->where(function ($q) {
                $q->whereNotNull('nomor_kontrak')
                    ->where('nomor_kontrak', '!=', '')
                    ->orWhereHas('penyaluranDanaInvestasi');
            });

        // Apply global search filter
        if (!empty($this->globalSearch)) {
            $search = $this->globalSearch;
            $query->where(function ($q) use ($search) {
                $q->where('nama_investor', 'like', '%' . $search . '%')
                    ->orWhere('nomor_kontrak', 'like', '%' . $search . '%')
                    ->orWhere('jenis_investasi', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        // Apply year filter - filter by year of tanggal_investasi
        if (!empty($this->year)) {
            $query->whereYear('tanggal_investasi', $this->year);
        }

        return $query;
    }

    /**
     * Get calculated data for a row
     */
    private function getCalculatedData($row): array
    {
        $year = $this->year;
        $id = $row->id_pengajuan_investasi;

        // Get tanggal pengembalian terakhir
        $tglTerakhir = DB::table('pengembalian_investasi')
            ->where('id_pengajuan_investasi', $id)
            ->max('tanggal_pengembalian');

        $bungaPerBulan = $row->bunga_pertahun / 12;
        $cofBulan = ($row->jumlah_investasi * $bungaPerBulan) / 100;

        $tanggalMulai = Carbon::parse($row->tanggal_investasi);
        $tanggalAkhirPeriode = Carbon::create($year, 12, 31);

        // Hitung CoF akhir periode
        if ($tanggalMulai->year > $year) {
            $cofAkhirPeriode = 0;
        } else {
            $tanggalSekarang = Carbon::now();
            $tanggalBatas = $tanggalAkhirPeriode->lt($tanggalSekarang) ? $tanggalAkhirPeriode : $tanggalSekarang;

            $bulanBerjalan = max(0, $tanggalMulai->diffInMonths($tanggalBatas) + 1);

            $totalSeharusnya = $cofBulan * $bulanBerjalan;

            $totalDibayar = DB::table('pengembalian_investasi')
                ->where('id_pengajuan_investasi', $id)
                ->where('tanggal_pengembalian', '<=', $tanggalBatas)
                ->sum('bunga_dibayar');

            $cofAkhirPeriode = max(0, $totalSeharusnya - $totalDibayar);
        }

        return [
            'bunga_per_bulan' => $bungaPerBulan,
            'cof_bulan' => $cofBulan,
            'cof_akhir_periode' => $cofAkhirPeriode,
            'tgl_pengembalian' => $tglTerakhir,
        ];
    }

    public function columns(): array
    {
        $rowNumber = 0;

        return [
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
                ->label(function ($row) {
                    $value = Carbon::parse($row->tanggal_investasi)->format('d-m-Y');
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'tanggal_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Jenis Investasi', 'jenis_investasi')
                ->sortable()
                ->searchable(function (Builder $builder, $term) {
                    $builder->orWhere('jenis_investasi', 'like', '%' . $term . '%')
                        ->orWhere('nomor_kontrak', 'like', '%' . $term . '%');
                })
                ->label(function ($row) {
                    $value = $row->jenis_investasi ?? '-';
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'jenis_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Deposan', 'nama_investor')
                ->sortable()
                ->searchable(function (Builder $builder, $term) {
                    $builder->orWhere('nama_investor', 'like', '%' . $term . '%');
                })
                ->label(function ($row) {
                    $value = $row->nama_investor;
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'nama_investor\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Nominal Deposit', 'jumlah_investasi')
                ->sortable()
                ->label(function ($row) {
                    $value = 'Rp ' . number_format($row->jumlah_investasi, 0, ',', '.');
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'jumlah_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Lama Investasi', 'lama_investasi')
                ->sortable()
                ->label(function ($row) {
                    $value = $row->lama_investasi . ' Bulan';
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'lama_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Bunga (%PA)', 'bunga_pertahun')
                ->sortable()
                ->label(function ($row) {
                    $value = number_format($row->bunga_pertahun, 2) . '%';
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'bunga_pertahun\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Bunga Nominal', 'nominal_bunga_yang_didapatkan')
                ->sortable()
                ->label(function ($row) {
                    $value = 'Rp ' . number_format($row->nominal_bunga_yang_didapatkan, 0, ',', '.');
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'nominal_bunga_yang_didapatkan\'})"></i>
                    </div>';
                })
                ->html(),

            // Calculated field - NO EDIT
            Column::make('Bunga (%Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">' . number_format($calc['bunga_per_bulan'], 2) . '%</div>';
                })
                ->html(),

            // Calculated field - NO EDIT
            Column::make('Bunga (COF/Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['cof_bulan'], 0, ',', '.') . '</div>';
                })
                ->html(),

            // Calculated field - NO EDIT
            Column::make('CoF Per Akhir Des')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['cof_akhir_periode'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable(function (Builder $builder, $term) {
                    $builder->orWhere('status', 'like', '%' . $term . '%');
                })
                ->label(function ($row) {
                    $value = $row->status;
                    $id = $row->id_pengajuan_investasi;
                    $badge = $value === 'Lunas'
                        ? '<span class="badge bg-label-success">Lunas</span>'
                        : '<span class="badge bg-label-warning">Aktif</span>';
                    return '<div class="text-center editable-cell">' . $badge . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'status\'})"></i>
                    </div>';
                })
                ->html(),

            // Tgl Pengembalian from another table - NO EDIT
            Column::make('Tgl Pengembalian')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    $tgl = $calc['tgl_pengembalian'];
                    return '<div class="text-center">' . ($tgl ? Carbon::parse($tgl)->format('d-m-Y') : '-') . '</div>';
                })
                ->html(),
        ];
    }
}
