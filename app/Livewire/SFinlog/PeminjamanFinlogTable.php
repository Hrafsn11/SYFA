<?php

namespace App\Livewire\SFinlog;

use App\Models\PeminjamanFinlog;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PeminjamanFinlogTable extends DataTableComponent
{
    protected $model = PeminjamanFinlog::class;

    protected $listeners = ['refreshPeminjamanFinlogTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_peminjaman_finlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari peminjaman finlog...')
            ->setSearchDebounce(500)

            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)

            // Default Sort
            ->setDefaultSort('id_peminjaman_finlog', 'desc')

            // Table Styling
            ->setTableAttributes([
                'class' => 'table table-hover',
            ])
            ->setTheadAttributes([
                'class' => 'table-light',
            ])
            ->setSearchFieldAttributes([
                'class' => 'form-control',
                'placeholder' => 'Cari peminjaman finlog...',
            ])
            ->setPerPageFieldAttributes([
                'class' => 'form-select',
            ])

            // Disable Bulk Actions
            ->setBulkActionsDisabled();
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return PeminjamanFinlog::query()
            ->select('peminjaman_finlog.*');
    }

    public function columns(): array
    {
        return [
            Column::make('No')
                ->label(function ($row) use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;

                    return '<div class="text-center">'.$number.'</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
            Column::make('Nomor peminjaman', 'nomor_peminjaman')
                ->sortable(),
            Column::make('Nama project', 'nama_project')
                ->sortable(),
            Column::make('Durasi project', 'durasi_project')
                ->sortable()
                ->format(function ($value) {
                    return $value . ' bulan';
                }),
            Column::make('Nib perusahaan', 'nib_perusahaan')
                ->sortable(),
            Column::make('Nilai pinjaman', 'nilai_pinjaman')
                ->sortable()
                ->format(function ($value) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                }),
            Column::make('Presentase bagi hasil', 'presentase_bagi_hasil')
                ->sortable()
                ->format(function ($value) {
                    return $value . '%';
                }),
            Column::make('Nilai bagi hasil', 'nilai_bagi_hasil')
                ->sortable()
                ->format(function ($value) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                }),
            Column::make('Total pinjaman', 'total_pinjaman')
                ->sortable()
                ->format(function ($value) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                }),
            Column::make('Harapan tanggal pencairan', 'harapan_tanggal_pencairan')
                ->sortable()
                ->format(function ($value) {
                    return $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-';
                }),
            Column::make('Top', 'top')
                ->sortable()
                ->format(function ($value) {
                    return $value . ' hari';
                }),
            Column::make('Rencana tgl pengembalian', 'rencana_tgl_pengembalian')
                ->sortable()
                ->format(function ($value) {
                    return $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-';
                }),
            Column::make('Status', 'status')
                ->sortable()
                ->format(function ($value) {
                    $badges = [
                        'Draft' => 'secondary',
                        'Menunggu Persetujuan' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        'Dicairkan' => 'info',
                        'Selesai' => 'primary'
                    ];
                    $badge = $badges[$value] ?? 'secondary';
                    return '<span class="badge bg-'.$badge.'">'.$value.'</span>';
                })
                ->html(),
            Column::make('Aksi')
                ->label(function ($row) {
                    $detailUrl = route('sfinlog.peminjaman.detail', ['id' => $row->id_peminjaman_finlog]);
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="'.$detailUrl.'" class="btn btn-sm btn-info"><i class="ti ti-eye"></i></a>';
                    // $btn .= '<button type="button" class="btn btn-sm btn-warning" onclick="editPeminjaman(\''.$row->id_peminjaman_finlog.'\')"><i class="ti ti-edit"></i></button>';
                    // $btn .= '<button type="button" class="btn btn-sm btn-danger" onclick="deletePeminjaman(\''.$row->id_peminjaman_finlog.'\')"><i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
