<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PengembalianPinjamanFinlog;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Database\Eloquent\Builder;

class ReportPengembalianFinlogTable extends DataTableComponent
{
    protected $model = PengembalianPinjamanFinlog::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengembalian_pinjaman_finlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Report Pengembalian...')
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
        // Get current user's debitur
        $currentDebitur = MasterDebiturDanInvestor::where('user_id', auth()->id())->first();

        if (!$currentDebitur) {
            // Return empty query if user is not a debitur
            return PengembalianPinjamanFinlog::query()->whereRaw('1 = 0');
        }

        // Filter by user's debitur - show ALL pengembalian records
        return PengembalianPinjamanFinlog::query()
            ->with(['peminjamanFinlog.debitur', 'cellsProject', 'project'])
            ->whereHas('peminjamanFinlog', function ($query) use ($currentDebitur) {
                $query->where('id_debitur', $currentDebitur->id_debitur);
            });
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

            Column::make('Jatuh Tempo', 'jatuh_tempo')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">' . ($value ? $value->format('d/m/Y') : '-') . '</div>')
                ->html(),

            Column::make('Bukti Pembayaran', 'bukti_pembayaran')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    if ($value) {
                        return '<div class="text-center"><a href="' . asset('storage/' . $value) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-file-text"></i></a></div>';
                    }
                    return '<div class="text-center"><span class="text-muted">-</span></div>';
                })
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
