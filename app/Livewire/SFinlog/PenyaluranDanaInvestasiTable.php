<?php

namespace App\Livewire\SFinlog;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Models\PenyaluranDepositoSfinlog;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PenyaluranDanaInvestasiTable extends DataTableComponent
{
    use HasUniversalFormAction, HasDebiturAuthorization;

    protected $model = PenyaluranDepositoSfinlog::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_penyaluran_deposito_sfinlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Penyaluran Dana...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('created_at', 'desc')
            ->setTableAttributes(['class' => 'table table-bordered'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setBulkActionsDisabled();
    }

    public function builder(): Builder
    {
        $query = PenyaluranDepositoSfinlog::query()
            ->leftJoin('pengajuan_investasi_finlog as pif', 'penyaluran_deposito_sfinlog.id_pengajuan_investasi_finlog', '=', 'pif.id_pengajuan_investasi_finlog')
            ->leftJoin('master_debitur_dan_investor', 'pif.id_debitur_dan_investor', '=', 'master_debitur_dan_investor.id_debitur')
            ->leftJoin(
                \DB::raw('(
                    SELECT 
                        id_pengajuan_investasi_finlog, 
                        SUM(nominal_yang_disalurkan) as total_disalurkan_sum
                    FROM penyaluran_deposito_sfinlog 
                    GROUP BY id_pengajuan_investasi_finlog
                ) as pds_sum'),
                'pif.id_pengajuan_investasi_finlog',
                '=',
                'pds_sum.id_pengajuan_investasi_finlog'
            )
            ->select([
                'penyaluran_deposito_sfinlog.id_penyaluran_deposito_sfinlog',
                'penyaluran_deposito_sfinlog.id_pengajuan_investasi_finlog',
                'penyaluran_deposito_sfinlog.id_cells_project',
                'penyaluran_deposito_sfinlog.id_project',
                'penyaluran_deposito_sfinlog.nominal_yang_disalurkan',
                'penyaluran_deposito_sfinlog.tanggal_pengiriman_dana',
                'penyaluran_deposito_sfinlog.tanggal_pengembalian',
                'penyaluran_deposito_sfinlog.bukti_pengembalian',
                'penyaluran_deposito_sfinlog.created_at',
                'penyaluran_deposito_sfinlog.updated_at',
                'pif.nomor_kontrak as pif_nomor_kontrak',
                'pif.nama_investor as pif_nama_investor',
                'pif.nominal_investasi as pif_nominal_investasi',
                'pif.lama_investasi as pif_lama_investasi',
                \DB::raw('COALESCE(pds_sum.total_disalurkan_sum, 0) as total_disalurkan'),
                \DB::raw('(pif.nominal_investasi - COALESCE(pds_sum.total_disalurkan_sum, 0)) as sisa_dana')
            ])
            ->orderBy('penyaluran_deposito_sfinlog.created_at', 'desc');

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

                    return '<div class="text-center">'.$number.'</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('No. Kontrak')
                ->label(fn ($row) => '<div class="text-center">'.($row->pif_nomor_kontrak ?? '-').'</div>')
                ->html(),

            Column::make('Nama Investor')
                ->label(fn ($row) => '<div class="text-center">'.($row->pif_nama_investor ?? '-').'</div>')
                ->html(),

            Column::make('Jumlah Investasi')
                ->label(function ($row) {
                    $jumlah = $row->pif_nominal_investasi ?? null;
                    return '<div class="text-center">'.($jumlah ? 'Rp ' . number_format($jumlah, 0, ',', '.') : '-').'</div>';
                })
                ->html(),

            Column::make('Lama Investasi')
                ->label(function ($row) {
                    $lama = $row->pif_lama_investasi ?? null;
                    return '<div class="text-center">'.($lama ? $lama . ' Bulan' : '-').'</div>';
                })
                ->html(),

            Column::make('Penyaluran Dana', 'nominal_yang_disalurkan')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-end">'.($value ? 'Rp ' . number_format($value, 0, ',', '.') : '-').'</div>';
                })
                ->html(),

            Column::make('Sisa Dana')
                ->label(function ($row) {
                    $sisa = $row->sisa_dana ?? 0;
                    $badgeClass = $sisa > 0 ? 'bg-label-success' : 'bg-label-secondary';
                    return '<div class="text-end">
                        <span class="badge '.$badgeClass.' px-3 py-2">
                            Rp ' . number_format($sisa, 0, ',', '.') . '
                        </span>
                    </div>';
                })
                ->html(),

            Column::make('Tanggal Disalurkan', 'tanggal_pengiriman_dana')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">'.($value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-').'</div>';
                })
                ->html(),

            Column::make('Rencana Tanggal Penagihan', 'tanggal_pengembalian')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">'.($value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-').'</div>';
                })
                ->html(),

            Column::make('Status Pembayaran', 'bukti_pengembalian')
                ->sortable()
                ->format(function ($value) {
                    if ($value) {
                        return '<div class="text-center"><span class="badge bg-label-success">Lunas</span></div>';
                    }
                    return '<div class="text-center"><span class="badge bg-label-danger">Belum Lunas</span></div>';
                })
                ->html(),

            Column::make('Bukti Transfer', 'bukti_pengembalian')
                ->format(function ($value) {
                    if ($value) {
                        return '<div class="text-center">
                            <a href="/storage/' . $value . '" target="_blank" class="btn btn-sm btn-outline-primary action-btn">
                                <i class="ti ti-file-text"></i>
                            </a>
                        </div>';
                    }
                    return '<div class="text-center"><span class="text-muted">-</span></div>';
                })
                ->html(),
        ];
    }
}

