<?php

namespace App\Livewire\SFinlog;

use App\Models\ArPerbulanFinlog;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ArPerbulanTable extends DataTableComponent
{
    protected $model = ArPerbulanFinlog::class;

    protected $listeners = ['refreshArPerbulanTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_ar_perbulan_finlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari perusahaan...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('bulan', 'desc')
            ->setTableAttributes([
                'class' => 'table table-hover',
            ])
            ->setTheadAttributes([
                'class' => 'table-light',
            ])
            ->setSearchFieldAttributes([
                'class' => 'form-control',
                'placeholder' => 'Cari perusahaan...',
            ])
            ->setPerPageFieldAttributes([
                'class' => 'form-select',
            ])
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
                        // Format bulan: YYYY-MM, jadi ambil 2 karakter terakhir
                        $builder->where('bulan', 'LIKE', '%-' . $value);
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
                        // Format bulan: YYYY-MM, jadi ambil 4 karakter pertama
                        $builder->where('bulan', 'LIKE', $value . '-%');
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        return ArPerbulanFinlog::query()
            ->orderBy('bulan', 'desc');
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

            Column::make('Bulan', 'bulan')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-center">' . \Carbon\Carbon::parse($value)->translatedFormat('F Y') . '</div>';
                })
                ->html(),

            Column::make('Nama Perusahaan', 'nama_perusahaan')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-start">' . ($value ?: '-') . '</div>')
                ->html(),

            Column::make('Sisa AR Piutang Pokok', 'sisa_ar_pokok')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">Rp ' . number_format($value, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sisa Bagi Hasil', 'sisa_bagi_hasil')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">Rp ' . number_format($value, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sisa AR Pokok + Bagi Hasil', 'sisa_ar_total')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp ' . number_format($value, 0, ',', '.') . '</strong></div>';
                })
                ->html(),
        ];
    }
}
