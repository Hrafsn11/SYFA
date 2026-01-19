<?php

namespace App\Livewire\ProgramRestrukturisasi;

use App\Models\ProgramRestrukturisasi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class Table extends DataTableComponent
{
    protected $model = ProgramRestrukturisasi::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_program_restrukturisasi')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari program restrukturisasi...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('id_program_restrukturisasi', 'desc')
            ->setTableAttributes(['class' => 'table table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari program restrukturisasi...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(true)
            ->setBulkActionsDisabled()
            ->setEmptyMessage('Tidak ada data program restrukturisasi');
    }

    public function builder(): Builder
    {
        $user = Auth::user();


        $isAdmin = $user && $user->roles()->where('restriction', 1)->exists();

        $query = ProgramRestrukturisasi::query()
            ->with(['pengajuanRestrukturisasi.debitur', 'creator']);

        if (!$isAdmin) {
            $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();

            if (!$debitur) {
                return ProgramRestrukturisasi::query()->whereRaw('1 = 0');
            }

            $query->whereHas('pengajuanRestrukturisasi', function ($q) use ($debitur) {
                $q->where('id_debitur', $debitur->id_debitur);
            });
        }

        $query->when($this->getSearch(), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('pengajuanRestrukturisasi.debitur', function ($subQuery) use ($search) {
                    $subQuery->where('nama', 'LIKE', '%' . $search . '%');
                })
                    ->orWhereHas('pengajuanRestrukturisasi', function ($subQuery) use ($search) {
                        $subQuery->where('nama_perusahaan', 'LIKE', '%' . $search . '%')
                            ->orWhere('nomor_kontrak_pembiayaan', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhere('metode_perhitungan', 'LIKE', '%' . $search . '%');
            });
        });

        return $query->select('program_restrukturisasi.*');
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Metode Perhitungan')
                ->options([
                    '' => 'Semua Metode',
                    'Flat' => 'Flat',
                    'Efektif (Anuitas)' => 'Efektif (Anuitas)',
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
                        $builder->whereRaw("MONTH(program_restrukturisasi.created_at) = ?", [$value]);
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
                        $builder->whereRaw("YEAR(program_restrukturisasi.created_at) = ?", [$value]);
                    }
                }),

            SelectFilter::make('Status')
                ->options([
                    '' => 'Semua Status',
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

            Column::make('Nama Debitur', 'pengajuanRestrukturisasi.debitur.nama')
                ->label(fn($row) => $row->pengajuanRestrukturisasi?->debitur?->nama ?? $row->pengajuanRestrukturisasi?->nama_perusahaan ?? '-')
                ->sortable(),

            Column::make('Nomor Kontrak', 'pengajuanRestrukturisasi.nomor_kontrak_pembiayaan')
                ->label(fn($row) => $row->pengajuanRestrukturisasi?->nomor_kontrak_pembiayaan ?? '-')
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
                    $status = $row->status ?? 'Berjalan';
                    $badgeClass = match ($status) {
                        'Lunas' => 'bg-success',
                        'Tertunda' => 'bg-warning',
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
                    $detailUrl = route('program-restrukturisasi.show', $row->id_program_restrukturisasi);
                    $editUrl   = auth()->user()->can('program_restrukturisasi.edit')
                        ? route('program-restrukturisasi.edit', $row->id_program_restrukturisasi)
                        : null;

                    return view('components.table-actions', compact('detailUrl', 'editUrl'));
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
