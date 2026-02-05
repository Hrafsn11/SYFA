<?php

namespace App\Livewire\SFinlog;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengembalianInvestasiFinlog;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PengembalianInvestasiFinlogTable extends DataTableComponent
{
    use HasDebiturAuthorization;

    protected $model = PengembalianInvestasiFinlog::class;

    protected $listeners = ['refreshPengembalianInvestasiFinlogTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengembalian_investasi_finlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Pengembalian...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('tanggal_pengembalian', 'desc')
            ->setTableAttributes(['class' => 'table table-bordered'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setBulkActionsDisabled();
    }

    public function builder(): Builder
    {
        $query = PengembalianInvestasiFinlog::query()
            ->leftJoin(
                'pengajuan_investasi_finlog as pif',
                'pengembalian_investasi_finlog.id_pengajuan_investasi_finlog',
                '=',
                'pif.id_pengajuan_investasi_finlog'
            )
            ->leftJoin('master_debitur_dan_investor', 'pif.id_debitur_dan_investor', '=', 'master_debitur_dan_investor.id_debitur')
            ->select([
                'pengembalian_investasi_finlog.*',
                'pif.nomor_kontrak',
                'pif.nama_investor',
                'pif.nominal_investasi',
            ])
            ->orderBy('pengembalian_investasi_finlog.created_at', 'desc');

        return $this->applyDebiturAuthorization($query);
    }

    public function columns(): array
    {
        $rowNumber = 0;

        return [
            Column::make('No')
                ->label(function () use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;
                    return '<div class="text-center">' . $number . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('No. Kontrak', 'pif.nomor_kontrak')
                ->label(fn($row) => '<div class="text-center">' . ($row->nomor_kontrak ?? '-') . '</div>')
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhere('pif.nomor_kontrak', 'LIKE', '%' . $searchTerm . '%');
                }),

            Column::make('Nama Investor', 'pif.nama_investor')
                ->label(fn($row) => '<div>' . ($row->nama_investor ?? '-') . '</div>')
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhere('pif.nama_investor', 'LIKE', '%' . $searchTerm . '%');
                }),

            Column::make('Tanggal Pengembalian', 'tanggal_pengembalian')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-center">' . ($value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-') . '</div>';
                })
                ->html(),

            Column::make('Dana Pokok', 'dana_pokok_dibayar')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp ' . number_format($value ?? 0, 0, ',', '.') . '</strong></div>';
                })
                ->html(),

            Column::make('Bagi Hasil', 'bagi_hasil_dibayar')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp ' . number_format($value ?? 0, 0, ',', '.') . '</strong></div>';
                })
                ->html(),

            Column::make('Total Dibayar', 'total_dibayar')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">
                        <span class="badge bg-label-success px-3 py-2">
                            Rp ' . number_format($value ?? 0, 0, ',', '.') . '
                        </span>
                    </div>';
                })
                ->html(),

            Column::make('Bukti Transfer', 'bukti_transfer')
                ->format(function ($value) {
                    if ($value) {
                        return '<div class="text-center">
                            <a href="/storage/' . $value . '" target="_blank" class="text-primary text-decoration-none">
                                <i class="ti ti-file-text me-1"></i> Lihat Dokumen
                            </a>
                        </div>';
                    }
                    return '<div class="text-center text-muted">-</div>';
                })
                ->html(),
        ];
    }
}


