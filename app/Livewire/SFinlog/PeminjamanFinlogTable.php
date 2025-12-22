<?php

namespace App\Livewire\SFinlog;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PeminjamanFinlog;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PeminjamanFinlogTable extends DataTableComponent
{
    use HasDebiturAuthorization;

    protected $model = PeminjamanFinlog::class;

    protected $listeners = ['refreshPeminjamanFinlogTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_peminjaman_finlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari peminjaman finlog...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('id_peminjaman_finlog', 'desc')
            ->setTableAttributes(['class' => 'table table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari peminjaman finlog...'])
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
                        $builder->whereRaw("MONTH(peminjaman_finlog.created_at) = ?", [$value]);
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
                        $builder->whereRaw("YEAR(peminjaman_finlog.created_at) = ?", [$value]);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        $query = PeminjamanFinlog::query()
            ->select([
                'peminjaman_finlog.*',
                'master_debitur_dan_investor.user_id',
            ])
            ->leftJoin('master_debitur_dan_investor', 'peminjaman_finlog.id_debitur', '=', 'master_debitur_dan_investor.id_debitur');

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

            Column::make('Nomor peminjaman', 'nomor_peminjaman')
                ->sortable(),

            Column::make('Nama project', 'nama_project')
                ->sortable(),

            Column::make('Durasi project', 'durasi_project')
                ->sortable()
                ->label(function ($row) {
                    $bulan = $row->durasi_project ?? 0;
                    $hari = $row->durasi_project_hari ?? 0;
                    
                    return $bulan . ' Bulan ' . $hari . ' Hari';
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
                    return '<span class="badge bg-' . $badge . '">' . $value . '</span>';
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
