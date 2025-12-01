<?php

namespace App\Livewire;

use App\Models\PengembalianInvestasi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class PengembalianInvestasiTable extends DataTableComponent
{
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
            ->setDefaultSort('tanggal_pengembalian', 'desc')
            ->setTableAttributes(['class' => 'table table-bordered'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setBulkActionsDisabled();
    }

    public function builder(): Builder
    {
        return PengembalianInvestasi::query()
            ->with('pengajuanInvestasi:id_pengajuan_investasi,nomor_kontrak,nama_investor,jumlah_investasi,status')
            ->select([
                'id_pengembalian_investasi',
                'id_pengajuan_investasi',
                'dana_pokok_dibayar',
                'bagi_hasil_dibayar',
                'total_dibayar',
                'bukti_transfer',
                'tanggal_pengembalian',
                'created_at',
            ]);
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

            Column::make('No. Kontrak')
                ->label(fn ($row) => '<div class="text-center">'.($row->pengajuanInvestasi->nomor_kontrak ?? '-').'</div>')
                ->html()
                ->searchable(function ($builder, $searchTerm) {
                    $builder->orWhereHas('pengajuanInvestasi', function ($query) use ($searchTerm) {
                        $query->where('nomor_kontrak', 'like', '%'.$searchTerm.'%');
                    });
                }),

            Column::make('Nama Investor')
                ->label(fn ($row) => '<div>'.($row->pengajuanInvestasi->nama_investor ?? '-').'</div>')
                ->html()
                ->searchable(function ($builder, $searchTerm) {
                    $builder->orWhereHas('pengajuanInvestasi', function ($query) use ($searchTerm) {
                        $query->where('nama_investor', 'like', '%'.$searchTerm.'%');
                    });
                }),

            Column::make('Tanggal Pengembalian', 'tanggal_pengembalian')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-center">'.Carbon::parse($value)->format('d/m/Y').'</div>';
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
                                <i class="ti ti-circle-check me-1"></i>Lunas
                            </span>
                        </div>';
                    }
                    return '<div class="text-center">
                        <span class="badge bg-label-warning px-3 py-2">
                            <i class="ti ti-clock me-1"></i>Belum Lunas
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
