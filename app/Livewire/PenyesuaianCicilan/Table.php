<?php

namespace App\Livewire\PenyesuaianCicilan;

use App\Models\PenyesuaianCicilan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class Table extends DataTableComponent
{
    protected $model = PenyesuaianCicilan::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_penyesuaian_cicilan')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari penyesuaian cicilan...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('created_at', 'desc')
            ->setTableAttributes(['class' => 'table table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari penyesuaian cicilan...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(true)
            ->setBulkActionsDisabled()
            ->setEmptyMessage('Tidak ada data penyesuaian cicilan');
    }

    public function builder(): Builder
    {
        $user = Auth::user();


        $isAdmin = $user && $user->roles()->where('restriction', 1)->exists();

        $query = PenyesuaianCicilan::query()
            ->with(['PengajuanCicilan.debitur', 'creator']);

        if (!$isAdmin) {
            $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();

            if (!$debitur) {
                return PenyesuaianCicilan::query()->whereRaw('1 = 0');
            }

            $query->whereHas('PengajuanCicilan', function ($q) use ($debitur) {
                $q->where('id_debitur', $debitur->id_debitur);
            });
        }

        $query->when($this->getSearch(), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('PengajuanCicilan.debitur', function ($subQuery) use ($search) {
                    $subQuery->where('nama', 'LIKE', '%' . $search . '%');
                })
                    ->orWhereHas('PengajuanCicilan', function ($subQuery) use ($search) {
                        $subQuery->where('nama_perusahaan', 'LIKE', '%' . $search . '%')
                            ->orWhere('nomor_kontrak_pembiayaan', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhere('metode_perhitungan', 'LIKE', '%' . $search . '%');
            });
        });

        return $query->select('penyesuaian_cicilan.*')
            ->orderBy('penyesuaian_cicilan.created_at', 'desc');
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Metode Perhitungan')
                ->options([
                    '' => 'Semua Metode',
                    'Flat' => 'Flat',
                    'Anuitas' => 'Efektif (Anuitas)',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->where('metode_perhitungan', $value);
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
                        $builder->whereRaw("MONTH(penyesuaian_cicilan.created_at) = ?", [$value]);
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
                        $builder->whereRaw("YEAR(penyesuaian_cicilan.created_at) = ?", [$value]);
                    }
                }),

            SelectFilter::make('Status')
                ->options([
                    '' => 'Semua Status',
                    'Menunggu Generate Kontrak' => 'Menunggu Generate Kontrak',
                    'Berjalan' => 'Berjalan',
                    'Lunas' => 'Lunas',
                    'Tertunda' => 'Tertunda',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->where('status', $value);
                    }
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('No')
                ->label(function ($row) use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;
                    return '<div class="text-center">' . $number . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('Nama Debitur', 'PengajuanCicilan.debitur.nama')
                ->label(fn($row) => $row->PengajuanCicilan?->debitur?->nama ?? $row->PengajuanCicilan?->nama_perusahaan ?? '-')
                ->sortable(),

            Column::make('Nomor Kontrak', 'PengajuanCicilan.nomor_kontrak_pembiayaan')
                ->label(fn($row) => $row->PengajuanCicilan?->nomor_kontrak_pembiayaan ?? '-')
                ->sortable(),

            Column::make('Metode', 'metode_perhitungan')
                ->label(fn($row) => '<span class="badge bg-info">' . $row->metode_perhitungan . '</span>')
                ->html()
                ->sortable(),

            Column::make('Plafon (Rp)', 'plafon_pembiayaan')
                ->label(fn($row) => number_format($row->plafon_pembiayaan, 0, ',', '.'))
                ->sortable(),

            Column::make('Bunga (%)', 'suku_bunga_per_tahun')
                ->label(fn($row) => number_format($row->suku_bunga_per_tahun, 2))
                ->sortable(),

            Column::make('Tenor (bln)', 'jangka_waktu_total')
                ->label(fn($row) => $row->jangka_waktu_total)
                ->sortable(),

            Column::make('Total Dibayar (Rp)', 'total_cicilan')
                ->label(fn($row) => number_format($row->total_cicilan, 0, ',', '.'))
                ->sortable(),

            Column::make('Status', 'status')
                ->label(function ($row) {
                    $status = $row->status ?? 'Menunggu Generate Kontrak';
                    $badgeClass = match ($status) {
                        'Berjalan' => 'bg-success',
                        'Menunggu Generate Kontrak' => 'bg-warning',
                        'Lunas' => 'bg-primary',
                        'Tertunda' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                    return '<span class="badge ' . $badgeClass . '">' . $status . '</span>';
                })
                ->html()
                ->sortable(),

            Column::make('Dibuat', 'created_at')
                ->label(fn($row) => $row->created_at->format('d/m/Y'))
                ->sortable(),

            Column::make('Aksi')
                ->label(function ($row) {
                    $detailUrl = route('penyesuaian-cicilan.show', $row->id_penyesuaian_cicilan);

                    // Edit hanya bisa jika kontrak sudah di-generate
                    $editUrl = null;
                    if (!is_null($row->kontrak_generated_at) && auth()->user()->can('penyesuaian_cicilan.edit')) {
                        $editUrl = route('penyesuaian-cicilan.edit', $row->id_penyesuaian_cicilan);
                    }

                    // Tambah tombol Generate Kontrak jika kontrak belum di-generate
                    $generateKontrakUrl = null;
                    if (is_null($row->kontrak_generated_at) && auth()->user()->can('penyesuaian_cicilan.generate_kontrak')) {
                        $generateKontrakUrl = route('penyesuaian-cicilan.generate-kontrak', $row->id_penyesuaian_cicilan);
                    }

                    return view('components.table-actions', compact('detailUrl', 'editUrl', 'generateKontrakUrl'));
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
