<?php

namespace App\Livewire\SFinlog;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasiFinlog;
use Carbon\Carbon;

class KertasKerjaInvestorSfinlogTable1 extends DataTableComponent
{
    protected $model = PengajuanInvestasiFinlog::class;

    public $year;

    protected $listeners = ['refreshKertasKerjaTable' => '$refresh', 'yearChanged' => 'setYear'];

    public function mount(): void
    {
        $this->year = request()->get('year', date('Y'));
    }

    public function setYear($year)
    {
        $this->year = $year;
        $this->resetPage();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi_finlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari deposan, nomor kontrak, status...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setTableAttributes(['class' => 'table border-top'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(true)
            ->setBulkActionsDisabled()
            ->setColumnSelectDisabled();
    }

    public function filters(): array
    {
        $years = [];
        for ($y = date('Y'); $y >= date('Y') - 10; $y--) {
            $years[(string) $y] = (string) $y;
        }

        return [
            SelectFilter::make('Tahun', 'year')
                ->options(array_merge(['' => 'Semua Tahun'], $years))
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $this->year = (int) $value;
                        $this->dispatch('yearChanged', $this->year);
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
                'nomor_kontrak'
            ])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '');
    }

    /**
     * Get calculated data for a row
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

            // EDITABLE
            Column::make('Tanggal Uang Masuk', 'tanggal_investasi')
                ->sortable()
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
        ];
    }
}
