<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Models\PeminjamanFinlog;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengembalianPeminjamanFinlogTable extends DataTableComponent
{
    protected $model = PeminjamanFinlog::class;

    protected $listeners = ['refreshPengembalianPeminjamanFinlogTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_peminjaman_finlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Pengembalian...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('id_peminjaman_finlog', 'desc')
            ->setTableAttributes(['class' => 'table table-bordered table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
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
                        $builder->whereRaw("MONTH(peminjaman_finlog.harapan_tanggal_pencairan) = ?", [$value]);
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
                        $builder->whereRaw("YEAR(peminjaman_finlog.harapan_tanggal_pencairan) = ?", [$value]);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        $user = Auth::user();

        $hasUnrestrictedRole = false;
        if ($user) {
            if ($user->hasRole('super-admin')) {
                $hasUnrestrictedRole = true;
            } else {
                $roles = $user->roles;
                $hasUnrestrictedRole = $roles->contains(function ($role) {
                    return $role->restriction == 1;
                });
            }
        }

        $query = PeminjamanFinlog::query()
            ->with(['debitur', 'latestPengembalian'])
            ->where('peminjaman_finlog.status', 'Selesai')
            ->select('peminjaman_finlog.*');

        if (!$hasUnrestrictedRole) {
            $debitur = MasterDebiturDanInvestor::where('user_id', Auth::id())->first();

            if (!$debitur) {
                return PeminjamanFinlog::query()->whereRaw('1 = 0');
            }

            $query->where('id_debitur', $debitur->id_debitur);
        }

        return $query;
    }

    public function columns(): array
    {
        $rowNumber = 0;

        return [
            Column::make('No')
                ->label(function ($row) use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;
                    return '<div class="text-center fw-semibold">' . $number . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('Nama Perusahaan', 'debitur.nama')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?: '-') . '</div>')
                ->html(),

            Column::make('Kode Peminjaman', 'nomor_peminjaman')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<span class="badge bg-primary">' . ($value ?? '-') . '</span>')
                ->html(),

            Column::make('Nama Project', 'nama_project')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?: '-') . '</div>')
                ->html(),

            Column::make('Total Pinjaman', 'total_pinjaman')
                ->sortable()
                ->format(fn($value) => '<div class="text-end">Rp ' . number_format($value, 0, ',', '.') . '</div>')
                ->html(),

            Column::make('Jumlah Pengembalian')
                ->label(function ($row) {
                    $totalPengembalian = $row->pengembalianPinjaman->sum('jumlah_pengembalian');
                    return '<div class="text-end">Rp ' . number_format($totalPengembalian, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sisa Pinjaman')
                ->label(function ($row) {
                    $latest = $row->latestPengembalian;
                    $sisa = $latest ? $latest->sisa_pinjaman : $row->nilai_pinjaman;
                    return '<div class="text-end text-danger fw-semibold">Rp ' . number_format($sisa, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sisa Bagi Hasil')
                ->label(function ($row) {
                    $latest = $row->latestPengembalian;
                    $sisa = $latest ? $latest->sisa_bagi_hasil : $row->nilai_bagi_hasil;
                    return '<div class="text-end text-warning fw-semibold">Rp ' . number_format($sisa, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Status')
                ->label(function ($row) {
                    $latest = $row->latestPengembalian;
                    $status = $latest ? $latest->status : 'Belum Lunas';
                    $badges = [
                        'Lunas' => 'success',
                        'Belum Lunas' => 'warning',
                        'Terlambat' => 'danger',
                    ];
                    $badgeClass = $badges[$status] ?? 'secondary';
                    return '<div class="text-center"><span class="badge bg-' . $badgeClass . '">' . $status . '</span></div>';
                })
                ->html(),

            Column::make('Aksi')
                ->label(fn($row) => view('livewire.sfinlog.pengembalian-pinjaman.partials.table-actions', [
                    'id' => $row->id_peminjaman_finlog
                ])->render())
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
