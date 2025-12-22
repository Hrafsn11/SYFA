<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\ReportPengembalian;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ReportPengembalianTable extends DataTableComponent
{
    use HasDebiturAuthorization;

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
            ->setTableAttributes(['class' => 'table table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari report pengembalian...'])
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
                        $builder->whereRaw("MONTH(report_pengembalian.created_at) = ?", [$value]);
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
                        $builder->whereRaw("YEAR(report_pengembalian.created_at) = ?", [$value]);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        $query = ReportPengembalian::query()
            ->select([
                'report_pengembalian.*',
                'pengajuan_peminjaman.id_debitur',
                'master_debitur_dan_investor.user_id',
            ])
            ->leftJoin('pengajuan_peminjaman', 'report_pengembalian.nomor_peminjaman', '=', 'pengajuan_peminjaman.nomor_peminjaman')
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_peminjaman.id_debitur', '=', 'master_debitur_dan_investor.id_debitur');

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

            Column::make('Nomor Peminjaman', 'nomor_peminjaman')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center"><strong>' . ($value ?: '-') . '</strong></div>')
                ->html(),

            Column::make('Nomor Invoice', 'nomor_invoice')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center"><strong>' . ($value ?: '-') . '</strong></div>')
                ->html(),

            Column::make('Due Date', 'due_date')
                ->sortable()
                ->format(function ($value) {
                    if (!$value) {
                        return '<div class="text-center">-</div>';
                    }
                    return '<div class="text-center">' . date('d-m-Y', strtotime($value)) . '</div>';
                })
                ->html(),

            Column::make('Hari Keterlambatan', 'hari_keterlambatan')
                ->sortable()
                ->format(function ($value) {
                    $badgeClass = $value === '0 Hari' ? 'bg-success' : 'bg-danger';
                    return '<div class="text-center"><span class="badge ' . $badgeClass . '">' . ($value ?: '0 Hari') . '</span></div>';
                })
                ->html(),

            Column::make('Total Bulan Pemakaian', 'total_bulan_pemakaian')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?: '-') . '</div>')
                ->html(),

            Column::make('Nilai Total Pengembalian', 'nilai_total_pengembalian')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp ' . number_format($value, 0, ',', '.') . '</strong></div>';
                })
                ->html(),
        ];
    }
}
