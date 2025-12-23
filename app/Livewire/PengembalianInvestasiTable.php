<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengembalianInvestasi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PengembalianInvestasiTable extends DataTableComponent
{
    use HasDebiturAuthorization;

    protected $model = PengembalianInvestasi::class;

    protected $listeners = ['refreshPengembalianInvestasiTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengembalian_investasi')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Pengembalian...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('pengembalian_investasi.created_at', 'desc')
            ->setTableAttributes(['class' => 'table table-hover'])
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
                        $builder->whereRaw("MONTH(pengembalian_investasi.tanggal_pengembalian) = ?", [$value]);
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
                        $builder->whereRaw("YEAR(pengembalian_investasi.tanggal_pengembalian) = ?", [$value]);
                    }
                }),

            SelectFilter::make('Status')
                ->options([
                    '' => 'Semua Status',
                    'lunas' => 'Lunas',
                    'belum_lunas' => 'Belum Lunas',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'lunas') {
                        $builder->whereHas('pengajuanInvestasi', function ($q) {
                            $q->where('status', 'Lunas');
                        });
                    } elseif ($value === 'belum_lunas') {
                        $builder->whereHas('pengajuanInvestasi', function ($q) {
                            $q->where('status', '!=', 'Lunas');
                        });
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        $query = PengembalianInvestasi::query()
            ->with('pengajuanInvestasi:id_pengajuan_investasi,nomor_kontrak,nama_investor,jumlah_investasi,status')
            ->leftJoin('pengajuan_investasi as pi', 'pengembalian_investasi.id_pengajuan_investasi', '=', 'pi.id_pengajuan_investasi')
            ->select([
                'pengembalian_investasi.*',
                'pi.nomor_kontrak as pi_nomor_kontrak',
                'pi.nama_investor as pi_nama_investor',
            ]);

        // Custom search for joined tables
        if ($search = $this->getSearch()) {
            $query->where(function ($q) use ($search) {
                $q->where('pi.nomor_kontrak', 'LIKE', '%' . $search . '%')
                    ->orWhere('pi.nama_investor', 'LIKE', '%' . $search . '%')
                    ->orWhere('pengembalian_investasi.dana_pokok_dibayar', 'LIKE', '%' . $search . '%')
                    ->orWhere('pengembalian_investasi.bagi_hasil_dibayar', 'LIKE', '%' . $search . '%')
                    ->orWhere('pengembalian_investasi.total_dibayar', 'LIKE', '%' . $search . '%')
                    ->orWhereRaw("DATE_FORMAT(pengembalian_investasi.tanggal_pengembalian, '%d/%m/%Y') LIKE ?", ['%' . $search . '%']);
            });
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
                    return '<div class="text-center">' . $number . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('No. Kontrak', 'pi_nomor_kontrak')
                ->sortable()
                ->label(fn($row) => '<div class="text-center">' . ($row->pi_nomor_kontrak ?? '-') . '</div>')
                ->html(),

            Column::make('Nama Investor', 'pi_nama_investor')
                ->sortable()
                ->label(fn($row) => '<div>' . ($row->pi_nama_investor ?? '-') . '</div>')
                ->html(),

            Column::make('Tanggal Pengembalian', 'tanggal_pengembalian')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-center">' . Carbon::parse($value)->format('d/m/Y') . '</div>';
                })
                ->html(),

            Column::make('Dana Pokok', 'dana_pokok_dibayar')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp ' . number_format($value, 0, ',', '.') . '</strong></div>';
                })
                ->html(),

            Column::make('Bagi Hasil', 'bagi_hasil_dibayar')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp ' . number_format($value, 0, ',', '.') . '</strong></div>';
                })
                ->html(),

            Column::make('Total Dibayar', 'total_dibayar')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">
                        <span class="badge bg-label-success px-3 py-2">
                            Rp ' . number_format($value, 0, ',', '.') . '
                        </span>
                    </div>';
                })
                ->html(),

            Column::make('Status')
                ->label(function ($row) {
                    $status = $row->pengajuanInvestasi->status ?? '';
                    if ($status === 'Lunas') {
                        return '<div class="text-center">
                            <span class="badge bg-label-success px-3 py-2">
                                Lunas
                            </span>
                        </div>';
                    }
                    return '<div class="text-center">
                        <span class="badge bg-label-warning px-3 py-2">
                            Belum Lunas
                        </span>
                    </div>';
                })
                ->html(),

            Column::make('Bukti Transfer', 'bukti_transfer')
                ->format(function ($value) {
                    if ($value) {
                        return '<div class="text-center">
                            <a href="/storage/' . $value . '" target="_blank" class="text-success text-decoration-none">
                                <i class="ti ti-file-text me-1"></i>
                            </a>
                        </div>';
                    }
                    return '<div class="text-center text-muted">-</div>';
                })
                ->html(),
        ];
    }
}
