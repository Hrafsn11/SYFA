<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PengembalianPinjamanFinlog;
use Illuminate\Database\Eloquent\Builder;

class PengembalianPeminjamanFinlogTable extends DataTableComponent
{
    protected $model = PengembalianPinjamanFinlog::class;

    protected $listeners = ['refreshPengembalianPeminjamanFinlogTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengembalian_pinjaman_finlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Pengembalian...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('tanggal_pengembalian', 'desc')
            ->setTableAttributes(['class' => 'table table-bordered table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setBulkActionsDisabled();
    }

    public function builder(): Builder
    {
        return PengembalianPinjamanFinlog::query()
            ->with(['peminjamanFinlog', 'cellsProject', 'project']);
    }

    public function columns(): array
    {
        return [
            Column::make('No')
                ->label(function ($row, Column $column) {
                    static $rowNumber = 0;
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;
                    return '<div class="text-center fw-semibold">' . $number . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('Kode Peminjaman', 'peminjamanFinlog.nomor_peminjaman')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<span class="badge bg-primary">' . ($value ?? '-') . '</span>')
                ->html(),

            Column::make('Jumlah Pengembalian', 'jumlah_pengembalian')
                ->sortable()
                ->format(fn($value) => '<div class="text-end">Rp ' . number_format($value, 0, ',', '.') . '</div>')
                ->html(),

            Column::make('Sisa Pinjaman', 'sisa_pinjaman')
                ->sortable()
                ->format(fn($value) => '<div class="text-end text-danger fw-semibold">Rp ' . number_format($value, 0, ',', '.') . '</div>')
                ->html(),

            Column::make('Sisa Bagi Hasil', 'sisa_bagi_hasil')
                ->sortable()
                ->format(fn($value) => '<div class="text-end text-warning fw-semibold">Rp ' . number_format($value, 0, ',', '.') . '</div>')
                ->html(),

            Column::make('Total Sisa', 'total_sisa_pinjaman')
                ->sortable()
                ->format(fn($value) => '<div class="text-end text-primary fw-bold">Rp ' . number_format($value, 0, ',', '.') . '</div>')
                ->html(),

            Column::make('Tanggal Pengembalian', 'tanggal_pengembalian')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">' . ($value ? $value->format('d/m/Y') : '-') . '</div>')
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->format(function ($value) {
                    $badges = [
                        'Lunas' => 'success',
                        'Belum Lunas' => 'warning',
                        'Terlambat' => 'danger',
                    ];
                    $badgeClass = $badges[$value] ?? 'secondary';
                    return '<div class="text-center"><span class="badge bg-' . $badgeClass . '">' . $value . '</span></div>';
                })
                ->html(),
        ];
    }
}
