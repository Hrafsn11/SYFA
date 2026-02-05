<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengajuanInvestasi;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PengajuanInvestasiTable extends DataTableComponent
{
    use HasDebiturAuthorization;

    protected $model = PengajuanInvestasi::class;

    protected $listeners = ['refreshPengajuanInvestasiTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari pengajuan investasi...')
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
            SelectFilter::make('Deposito')
                ->options([
                    '' => 'Semua Tipe',
                    'Reguler' => 'Reguler',
                    'Khusus' => 'Khusus',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->where('deposito', $value);
                    }
                }),

            SelectFilter::make('Status')
                ->options([
                    '' => 'Semua Status',
                    'Draft' => 'Draft',
                    'Submitted' => 'Submitted',
                    'Submit Dokumen' => 'Submit Dokumen',
                    'Dokumen Tervalidasi' => 'Dokumen Tervalidasi',
                    'Investor Setuju' => 'Investor Setuju',
                    'Disetujui oleh CEO SKI' => 'Disetujui oleh CEO SKI',
                    'Disetujui oleh Direktur SKI' => 'Disetujui oleh Direktur SKI',
                    'Dana Sudah Dicairkan' => 'Dana Sudah Dicairkan',
                    'Ditolak' => 'Ditolak',
                    'Rejected' => 'Rejected',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->where('status', $value);
                    }
                }),

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
                        $builder->whereRaw("MONTH(tanggal_investasi) = ?", [$value]);
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
                        $builder->whereRaw("YEAR(tanggal_investasi) = ?", [$value]);
                    }
                }),
        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        $query = PengajuanInvestasi::query()
            ->with(['investor'])
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_investasi.id_debitur_dan_investor', '=', 'master_debitur_dan_investor.id_debitur')
            ->select('pengajuan_investasi.*')
            ->orderBy('pengajuan_investasi.created_at', 'desc');

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

            Column::make('Nama Perusahaan')
                ->label(function ($row) {
                    $namaInvestor = $row->investor->nama ?? '-';
                    return '<div class="text-start">' . $namaInvestor . '</div>';
                })
                ->html()
                ->searchable(),

            Column::make('Deposito', 'deposito')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    $badgeClass = match ($value) {
                        'Reguler' => 'bg-primary',
                        'Khusus' => 'bg-warning',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge ' . $badgeClass . '">' . ($value ?: '-') . '</span></div>';
                })
                ->html(),

            Column::make('Jumlah Investasi', 'jumlah_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp. ' . number_format($value ?? 0, 0, ',', '.') . '</strong></div>';
                })
                ->html(),

            Column::make('Lama Investasi', 'lama_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">' . ($value ? $value . ' Bulan' : '-') . '</div>';
                })
                ->html(),

            Column::make('Bagi Hasil/Tahun', 'bagi_hasil_pertahun')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">' . ($value ? $value . '%' : '-') . '</div>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    $badgeClass = match ($value) {
                        'Draft' => 'bg-warning text-dark',
                        'Submitted' => 'bg-info',
                        'Submit Dokumen' => 'bg-info',
                        'Dokumen Tervalidasi' => 'bg-success',
                        'Investor Setuju' => 'bg-success',
                        'Disetujui oleh CEO SKI' => 'bg-success',
                        'Disetujui oleh Direktur SKI' => 'bg-success',
                        'Dana Sudah Dicairkan' => 'bg-primary',
                        'Ditolak' => 'bg-danger',
                        'Rejected' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge ' . $badgeClass . '">' . ucfirst($value ?: 'Draft') . '</span></div>';
                })
                ->html(),

            Column::make('Aksi')
                ->label(fn($row) => view('livewire.pengajuan-investasi.partials.table-actions', [
                    'id' => $row->id_pengajuan_investasi,
                    'status' => $row->status,
                    'current_step' => $row->current_step
                ])->render())
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
