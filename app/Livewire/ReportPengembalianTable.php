<?php

namespace App\Livewire;

use App\Models\ReportPengembalian;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ReportPengembalianTable extends DataTableComponent
{
    protected $model = ReportPengembalian::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_report_pengembalian')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari report pengembalian...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('id_report_pengembalian', 'desc')
            ->setTableAttributes([
                'class' => 'table table-hover',
            ])
            ->setTheadAttributes([
                'class' => 'table-light',
            ])
            ->setSearchFieldAttributes([
                'class' => 'form-control',
                'placeholder' => 'Cari report pengembalian...',
            ])
            ->setPerPageFieldAttributes([
                'class' => 'form-select',
            ])
            ->setBulkActionsDisabled();
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

            Column::make('Nomor Peminjaman', 'nomor_peminjaman')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center"><strong>'.($value ?: '-').'</strong></div>')
                ->html(),

            Column::make('Nomor Invoice', 'nomor_invoice')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center"><strong>'.($value ?: '-').'</strong></div>')
                ->html(),

            Column::make('Due Date', 'due_date')
                ->sortable()
                ->format(function ($value) {
                    if (!$value) {
                        return '<div class="text-center">-</div>';
                    }
                    return '<div class="text-center">'.date('d-m-Y', strtotime($value)).'</div>';
                })
                ->html(),

            Column::make('Hari Keterlambatan', 'hari_keterlambatan')
                ->sortable()
                ->format(function ($value) {
                    $badgeClass = $value === '0 Hari' ? 'bg-success' : 'bg-danger';
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.($value ?: '0 Hari').'</span></div>';
                })
                ->html(),

            Column::make('Total Bulan Pemakaian', 'total_bulan_pemakaian')
                ->sortable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?: '-').'</div>')
                ->html(),

            Column::make('Nilai Total Pengembalian', 'nilai_total_pengembalian')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp '.number_format($value, 0, ',', '.').'</strong></div>';
                })
                ->html(),
        ];
    }
}
