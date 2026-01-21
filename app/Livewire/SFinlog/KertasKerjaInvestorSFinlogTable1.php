<?php

namespace App\Livewire\SFinlog;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasiFinlog;
use Carbon\Carbon;

class KertasKerjaInvestorSFinlogTable1 extends DataTableComponent
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
        $year = $this->year;

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

            Column::make('Tanggal Uang Masuk', 'tanggal_investasi')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">' . Carbon::parse($value)->format('d-m-Y') . '</div>')
                ->html(),

            Column::make('Deposan', 'nama_investor')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . $value . '</div>')
                ->html(),

            Column::make('Nominal Deposito', 'nominal_investasi')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">Rp ' . number_format($value, 0, ',', '.') . '</div>')
                ->html(),

            Column::make('Lama Deposito', 'lama_investasi')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">' . $value . ' Bulan</div>')
                ->html(),

            Column::make('Bagi Hasil (%PA)', 'persentase_bagi_hasil')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">' . number_format($value, 2) . '%</div>')
                ->html(),

            Column::make('Bagi Hasil (Nominal/PA)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['bagi_hasil_nominal_pa'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Bagi Hasil Per Nominal')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['bagi_hasil_per_nominal'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Bagi Hasil (%Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">' . number_format($calc['bagi_hasil_per_bulan'], 2) . '%</div>';
                })
                ->html(),

            Column::make('Bagi Hasil (COF/Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['cof_bulan'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    if ($value === 'Lunas') {
                        return '<div class="text-center"><span class="badge bg-label-success">Lunas</span></div>';
                    }
                    return '<div class="text-center"><span class="badge bg-label-warning">Aktif</span></div>';
                })
                ->html(),
        ];
    }
}
