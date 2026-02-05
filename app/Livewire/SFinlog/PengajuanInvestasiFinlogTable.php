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
            ->setSearchPlaceholder('Cari nama investor, nomor kontrak, cells bisnis, nominal...')
            ->setSearchDebounce(500)

            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)

            // Default Sort
            ->setDefaultSort('created_at', 'desc')

            // Table Styling
            ->setTableAttributes([
                'class' => 'table table-hover',
            ])
            ->setTheadAttributes([
                'class' => 'table-light',
            ])
            ->setSearchFieldAttributes([
                'class' => 'form-control',
                'placeholder' => 'Cari nama investor, nomor kontrak, cells bisnis, nominal...',
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

    /**
     * Custom search for flexible searching across all fields
     */
    public function applySearch(): Builder
    {
        $searchTerm = $this->getSearch();

        if (empty($searchTerm)) {
            return $this->builder();
        }

        // Clean search term for nominal search - remove "Rp", dots, spaces
        $cleanedSearch = preg_replace('/[Rp\s\.]/i', '', $searchTerm);
        $isNumericSearch = is_numeric($cleanedSearch) && strlen($cleanedSearch) > 0;

        return $this->builder()->where(function ($query) use ($searchTerm, $cleanedSearch, $isNumericSearch) {
            // Search by nama_investor
            $query->where('pengajuan_investasi_finlog.nama_investor', 'like', '%' . $searchTerm . '%');

            // Search by nomor_kontrak
            $query->orWhere('pengajuan_investasi_finlog.nomor_kontrak', 'like', '%' . $searchTerm . '%');

            // Search by status
            $query->orWhere('pengajuan_investasi_finlog.status', 'like', '%' . $searchTerm . '%');

            // Search by tanggal_investasi
            $query->orWhere('pengajuan_investasi_finlog.tanggal_investasi', 'like', '%' . $searchTerm . '%');

            // Search by tanggal_berakhir_investasi
            $query->orWhere('pengajuan_investasi_finlog.tanggal_berakhir_investasi', 'like', '%' . $searchTerm . '%');

            // Search by cells bisnis name (through relation)
            $query->orWhereHas('project', function ($q) use ($searchTerm) {
                $q->where('nama_cells_bisnis', 'like', '%' . $searchTerm . '%');
            });

            // Search by investor name (through relation)
            $query->orWhereHas('investor', function ($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%');
            });

            // If numeric, also search by nominal and other numeric fields
            if ($isNumericSearch) {
                $query->orWhere('pengajuan_investasi_finlog.nominal_investasi', 'like', '%' . $cleanedSearch . '%');
                $query->orWhere('pengajuan_investasi_finlog.lama_investasi', '=', $cleanedSearch);
                $query->orWhere('pengajuan_investasi_finlog.persentase_bagi_hasil', 'like', '%' . $cleanedSearch . '%');
            }
        });
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
            ->leftJoin('cells_projects', 'pengajuan_investasi_finlog.id_cells_project', '=', 'cells_projects.id_cells_project')
            ->select('pengajuan_investasi_finlog.*')
            ->orderBy('pengajuan_investasi_finlog.created_at', 'desc');

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

            Column::make('Nama Investor', 'nama_investor')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-start"><strong>' . ($value ?: '-') . '</strong></div>')
                ->html(),

            Column::make('Cells Bisnis')
                ->label(function ($row) {
                    $namaCellsBisnis = $row->project->nama_cells_bisnis ?? $row->project->nama_cells_bisnis ?? '-';
                    return '<div class="text-start">' . $namaCellsBisnis . '</div>';
                })
                ->html()
                ->searchable(),

            Column::make('Tanggal Investasi', 'tanggal_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">' . ($value ? \Carbon\Carbon::parse($value)->format('d M Y') : '-') . '</div>';
                })
                ->html(),
            Column::make('Tanggal Berakhir', 'tanggal_berakhir_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">' . ($value ? \Carbon\Carbon::parse($value)->format('d M Y') : '-') . '</div>';
                })
                ->html(),

            Column::make('Nominal Investasi', 'nominal_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp ' . number_format($value ?? 0, 0, ',', '.') . '</strong></div>';
                })
                ->html(),

            Column::make('Lama Investasi', 'lama_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">' . ($value ? $value . ' Bulan' : '-') . '</div>';
                })
                ->html(),

            Column::make('Bagi Hasil', 'persentase_bagi_hasil')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">' . ($value ? number_format($value, 2) . '%' : '-') . '</div>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    // Determine badge color based on status
                    if (str_contains($value ?? '', 'Ditolak')) {
                        // Rejection statuses - show in danger/warning
                        if (str_contains($value, 'Perlu Revisi')) {
                            $badgeClass = 'bg-warning text-dark';
                        } else {
                            $badgeClass = 'bg-danger';
                        }
                    } else {
                        $badgeClass = match ($value) {
                            'Draft' => 'bg-warning text-dark',
                            'Menunggu Validasi Finance SKI' => 'bg-info',
                            'Menunggu Persetujuan CEO Finlog' => 'bg-info',
                            'Menunggu Upload Bukti Transfer' => 'bg-info',
                            'Menunggu Generate Kontrak' => 'bg-info',
                            'Disetujui' => 'bg-success',
                            'Selesai' => 'bg-success',
                            default => 'bg-secondary'
                        };
                    }
                    return '<div class="text-center"><span class="badge ' . $badgeClass . '">' . ucfirst($value ?: 'Draft') . '</span></div>';
                })
                ->html(),

            Column::make('Aksi')
                ->label(fn($row) => view('livewire.sfinlog.pengajuan-investasi.partials.table-actions', [
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
