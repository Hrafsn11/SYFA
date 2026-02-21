<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\LaporanTagihanBulanan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class LaporanTagihanBulananTable extends DataTableComponent
{
    use HasDebiturAuthorization;

    protected $model = LaporanTagihanBulanan::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_ar_perbulan')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari perusahaan...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('bulan', 'desc')
            ->setTableAttributes(['class' => 'table table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari perusahaan...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(true)
            ->setBulkActionsDisabled();
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Bulan')
                ->options([
                    '' => 'Semua Bulan',
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
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->whereRaw("MONTH(STR_TO_DATE(CONCAT(laporan_tagihan_bulanan.bulan, '-01'), '%Y-%m-%d')) = ?", [$value]);
                    }
                }),

            SelectFilter::make('Tahun')
                ->options([
                    '' => 'Semua Tahun',
                    '2023' => '2023',
                    '2024' => '2024',
                    '2025' => '2025',
                    '2026' => '2026',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->whereRaw("YEAR(STR_TO_DATE(CONCAT(laporan_tagihan_bulanan.bulan, '-01'), '%Y-%m-%d')) = ?", [$value]);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        // Update table names in query
        $query = LaporanTagihanBulanan::query()
            ->select([
                'laporan_tagihan_bulanan.*',
                'master_debitur_dan_investor.user_id',
            ])
            ->leftJoin('master_debitur_dan_investor', 'laporan_tagihan_bulanan.id_debitur', '=', 'master_debitur_dan_investor.id_debitur')
            ->orderBy('laporan_tagihan_bulanan.bulan', 'desc');

        return $this->applyDebiturAuthorization($query);
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

            Column::make('Periode', 'bulan')
                ->sortable()
                ->format(function ($value) {
                    if (!$value) return '-';
                    return '<div class="text-center"><span class="badge bg-label-primary">' . date('F Y', strtotime($value . '-01')) . '</span></div>';
                })
                ->html(),

            Column::make('Nama Perusahaan', 'nama_perusahaan')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-start">' . ($value ?: '-') . '</div>')
                ->html(),

            Column::make('Sisa Pinjaman Pokok', 'sisa_ar_pokok')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">Rp ' . number_format($value, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sisa Bunga', 'sisa_bunga') // Updated column
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">Rp ' . number_format($value, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sisa Pinjaman Pokok + Bunga', 'sisa_ar_total')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp ' . number_format($value, 0, ',', '.') . '</strong></div>';
                })
                ->html(),
        ];
    }
}
