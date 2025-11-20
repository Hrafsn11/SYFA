<?php

namespace App\Livewire;

use App\Models\ArPerbulan;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ArPerbulanTable extends DataTableComponent
{
    protected $model = ArPerbulan::class;

    protected $listeners = ['refreshArPerbulanTable' => '$refresh', 'filterByMonth'];

    public $selectedMonth = '';

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
            ->setBulkActionsDisabled();
    }

    public function filterByMonth($month)
    {
        $this->selectedMonth = $month;
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        $query = ArPerbulan::query();

        if ($this->selectedMonth) {
            $query->where('bulan', $this->selectedMonth);
        }

        return $query;
    }

    public function columns(): array
    {
        $rowNumber = 0;

        return [
            Column::make('No')
                ->label(function ($row) use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;

                    return '<div class="text-center">'.$number.'</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('Nama Perusahaan', 'nama_perusahaan')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-start">'.($value ?: '-').'</div>')
                ->html(),

            Column::make('Sisa AR Piutang Pokok', 'sisa_ar_pokok')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">Rp '.number_format($value, 0, ',', '.').'</div>';
                })
                ->html(),

            Column::make('Sisa Bagi Hasil', 'sisa_bagi_hasil')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">Rp '.number_format($value, 0, ',', '.').'</div>';
                })
                ->html(),

            Column::make('Sisa AR Pokok + Bagi Hasil', 'sisa_ar_total')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp '.number_format($value, 0, ',', '.').'</strong></div>';
                })
                ->html(),
        ];
    }
}
