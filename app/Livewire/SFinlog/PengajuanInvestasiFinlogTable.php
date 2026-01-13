<?php

namespace App\Livewire\SFinlog;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengajuanInvestasiFinlog;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PengajuanInvestasiFinlogTable extends DataTableComponent
{
    use HasDebiturAuthorization;

    protected $model = PengajuanInvestasiFinlog::class;

    protected $listeners = ['refreshPengajuanInvestasiFinlogTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi_finlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari pengajuan investasi...')
            ->setSearchDebounce(500)

            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)

            // Default Sort
            ->setDefaultSort('id_pengajuan_investasi_finlog', 'desc')

            // Table Styling
            ->setTableAttributes([
                'class' => 'table table-hover',
            ])
            ->setTheadAttributes([
                'class' => 'table-light',
            ])
            ->setSearchFieldAttributes([
                'class' => 'form-control',
                'placeholder' => 'Cari pengajuan investasi...',
            ])
            ->setPerPageFieldAttributes([
                'class' => 'form-select',
            ])

            // Enable Filters
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(true)

            // Disable Bulk Actions
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
                        $builder->whereRaw("MONTH(pengajuan_investasi_finlog.tanggal_investasi) = ?", [$value]);
                    }
                }),

            SelectFilter::make('Tahun')
                ->options([
                    '' => 'Semua Tahun',
                    '2023' => '2023',
                    '2024' => '2024',
                    '2025' => '2025',
                    '2026' => '2026',
                    '2027' => '2027',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->whereRaw("YEAR(pengajuan_investasi_finlog.tanggal_investasi) = ?", [$value]);
                    }
                }),
        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        $query = PengajuanInvestasiFinlog::query()
            ->with(['investor', 'project'])
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_investasi_finlog.id_debitur_dan_investor', '=', 'master_debitur_dan_investor.id_debitur')
            ->select('pengajuan_investasi_finlog.*');

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
                    return '<div class="text-center">'.$number.'</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
                
            Column::make('Nama Investor', 'nama_investor')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-start"><strong>'.($value ?: '-').'</strong></div>')
                ->html(),
                
            Column::make('Cells Bisnis')
                ->label(function ($row) {
                    $namaCellsBisnis = $row->project->nama_cells_bisnis ?? $row->project->nama_cells_bisnis ?? '-';
                    return '<div class="text-start">'.$namaCellsBisnis.'</div>';
                })
                ->html()
                ->searchable(),
                
            Column::make('Tanggal Investasi', 'tanggal_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">'.($value ? \Carbon\Carbon::parse($value)->format('d M Y') : '-').'</div>';
                })
                ->html(),
            Column::make('Tanggal Berakhir', 'tanggal_berakhir_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">'.($value ? \Carbon\Carbon::parse($value)->format('d M Y') : '-').'</div>';
                })
                ->html(),
                
            Column::make('Nominal Investasi', 'nominal_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp '.number_format($value ?? 0, 0, ',', '.').'</strong></div>';
                })
                ->html(),
                
            Column::make('Lama Investasi', 'lama_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">'.($value ? $value . ' Bulan' : '-').'</div>';
                })
                ->html(),
                
            Column::make('Bagi Hasil', 'persentase_bagi_hasil')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">'.($value ? number_format($value, 2) . '%' : '-').'</div>';
                })
                ->html(),
                
            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    $badgeClass = match($value) {
                        'Draft' => 'bg-warning text-dark',
                        'Menunggu Validasi Fia' => 'bg-info',
                        'Menunggu Validasi CEO' => 'bg-info',
                        'Menunggu Informasi Rekening' => 'bg-info',
                        'Menunggu Upload Bukti' => 'bg-info',
                        'Disetujui' => 'bg-success',
                        'Selesai' => 'bg-success',
                        'Ditolak' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.ucfirst($value ?: 'Draft').'</span></div>';
                })
                ->html(),
                
            Column::make('Aksi')
                ->label(fn ($row) => view('livewire.sfinlog.pengajuan-investasi.partials.table-actions', [
                    'id' => $row->id_pengajuan_investasi_finlog,
                    'status' => $row->status,
                    'current_step' => $row->current_step,
                    'data' => $row
                ])->render())
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
