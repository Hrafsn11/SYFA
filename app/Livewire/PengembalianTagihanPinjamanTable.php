<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengembalianTagihanPinjaman;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PengembalianTagihanPinjamanTable extends DataTableComponent
{
    use HasDebiturAuthorization;

    protected $model = PengembalianTagihanPinjaman::class;

    protected $listeners = ['refreshPengembalianTagihanPinjamanTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('ulid')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari pengembalian tagihan pinjaman...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('created_at', 'desc')
            ->setTableAttributes(['class' => 'table table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari pengembalian tagihan pinjaman...'])
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
                        $builder->whereRaw("MONTH(pengembalian_tagihan_pinjaman.created_at) = ?", [$value]);
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
                        $builder->whereRaw("YEAR(pengembalian_tagihan_pinjaman.created_at) = ?", [$value]);
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

        if ($hasUnrestrictedRole) {
            $latestRecords = DB::table('pengembalian_tagihan_pinjaman as pp1')
                ->select('pp1.ulid')
                ->joinSub(
                    DB::table('pengembalian_tagihan_pinjaman')
                        ->select('nomor_peminjaman', 'invoice_dibayarkan', DB::raw('MAX(created_at) as max_created'))
                        ->groupBy('nomor_peminjaman', 'invoice_dibayarkan'),
                    'latest',
                    function ($join) {
                        $join->on('pp1.nomor_peminjaman', '=', 'latest.nomor_peminjaman')
                            ->on('pp1.invoice_dibayarkan', '=', 'latest.invoice_dibayarkan')
                            ->on('pp1.created_at', '=', 'latest.max_created');
                    }
                )
                ->pluck('ulid');
        } else {
            // Restricted users (Debitur) can only see their own records
            $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();

            if (!$debitur) {
                return PengembalianTagihanPinjaman::query()->whereRaw('1 = 0');
            }

            $latestRecords = DB::table('pengembalian_tagihan_pinjaman as pp1')
                ->select('pp1.ulid')
                ->joinSub(
                    DB::table('pengembalian_tagihan_pinjaman')
                        ->select('nomor_peminjaman', 'invoice_dibayarkan', DB::raw('MAX(created_at) as max_created'))
                        ->whereIn('id_pengajuan_peminjaman', function ($subQuery) use ($debitur) {
                            $subQuery->select('id_pengajuan_peminjaman')
                                ->from('pengajuan_tagihan_pinjaman')
                                ->where('id_debitur', $debitur->id_debitur);
                        })
                        ->groupBy('nomor_peminjaman', 'invoice_dibayarkan'),
                    'latest',
                    function ($join) {
                        $join->on('pp1.nomor_peminjaman', '=', 'latest.nomor_peminjaman')
                            ->on('pp1.invoice_dibayarkan', '=', 'latest.invoice_dibayarkan')
                            ->on('pp1.created_at', '=', 'latest.max_created');
                    }
                )
                ->pluck('ulid');
        }

        return PengembalianTagihanPinjaman::query()
            ->with(['pengajuanTagihanPinjaman', 'pengembalianInvoices'])
            ->whereIn('ulid', $latestRecords)
            ->select('pengembalian_tagihan_pinjaman.*')
            ->orderBy('pengembalian_tagihan_pinjaman.created_at', 'desc');
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

            Column::make('Nama perusahaan', 'nama_perusahaan')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?: '-') . '</div>')
                ->html(),

            Column::make('Tanggal Pencairan', 'tanggal_pencairan')
                ->sortable()
                ->format(function ($value) {
                    if (!$value) {
                        return '<div class="text-center">-</div>';
                    }
                    return '<div class="text-center">' . date('d-m-Y', strtotime($value)) . '</div>';
                })
                ->html(),

            Column::make('Nomor peminjaman', 'nomor_peminjaman')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center"><strong>' . ($value ?: '-') . '</strong></div>')
                ->html(),

            Column::make('Nomor Invoice', 'invoice_dibayarkan')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center"><strong>' . ($value ?: '-') . '</strong></div>')
                ->html(),

            Column::make('Total pinjaman', 'total_pinjaman')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">Rp ' . number_format($value, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Nominal Dibayarkan')
                ->label(function ($row) {
                    $totalDibayarkan = $row->pengembalianInvoices->sum('nominal_yg_dibayarkan');
                    $formatted = 'Rp ' . number_format($totalDibayarkan, 0, ',', '.');
                    return '<div class="text-end"><strong>' . $formatted . '</strong></div>';
                })
                ->html(),

            Column::make('Sisa bayar pokok', 'sisa_bayar_pokok')
                ->sortable()
                ->format(function ($value) {
                    $badgeClass = $value == 0 ? 'bg-success' : 'bg-warning';
                    $formatted = 'Rp ' . number_format($value, 0, ',', '.');
                    return '<div class="text-center"><span class="badge ' . $badgeClass . '">' . $formatted . '</span></div>';
                })
                ->html(),

            Column::make('Sisa bunga', 'sisa_bunga')
                ->sortable()
                ->format(function ($value) {
                    $badgeClass = $value == 0 ? 'bg-success' : 'bg-warning';
                    $formatted = 'Rp ' . number_format($value, 0, ',', '.');
                    return '<div class="text-center"><span class="badge ' . $badgeClass . '">' . $formatted . '</span></div>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->format(function ($value) {
                    $badgeClass = match ($value) {
                        'Lunas' => 'bg-success',
                        'Belum Lunas' => 'bg-warning',
                        'Menunggak' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge ' . $badgeClass . '">' . ($value ?: 'Belum Lunas') . '</span></div>';
                })
                ->html(),

            Column::make('Aksi')
                ->label(fn ($row) => view('livewire.pengembalian-tagihan-pinjaman.partials.table-actions', [
                    'route_detail' => route('pengembalian.detail', ['id' => $row->ulid])
                ])->render())
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
