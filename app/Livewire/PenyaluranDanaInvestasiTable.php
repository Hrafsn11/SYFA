<?php

namespace App\Livewire;

use App\Models\PenyaluranDeposito;
use App\Livewire\Traits\HasUniversalFormAction;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class PenyaluranDanaInvestasiTable extends DataTableComponent
{
    use HasUniversalFormAction;

    protected $model = PenyaluranDeposito::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_penyaluran_deposito')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Penyaluran Dana...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setTableAttributes(['class' => 'table table-bordered'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setBulkActionsDisabled();
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return PenyaluranDeposito::query()
            ->leftJoin('pengajuan_investasi as pi', 'penyaluran_deposito.id_pengajuan_investasi', '=', 'pi.id_pengajuan_investasi')
            ->leftJoin(
                \DB::raw('(
                    SELECT 
                        id_pengajuan_investasi, 
                        SUM(nominal_yang_disalurkan) as total_disalurkan_sum
                    FROM penyaluran_deposito 
                    GROUP BY id_pengajuan_investasi
                ) as pd_sum'),
                'pi.id_pengajuan_investasi', 
                '=', 
                'pd_sum.id_pengajuan_investasi'
            )
            ->select([
                'penyaluran_deposito.id_penyaluran_deposito',
                'penyaluran_deposito.id_pengajuan_investasi',
                'penyaluran_deposito.id_debitur',
                'penyaluran_deposito.nominal_yang_disalurkan',
                'penyaluran_deposito.tanggal_pengiriman_dana',
                'penyaluran_deposito.tanggal_pengembalian',
                'penyaluran_deposito.bukti_pengembalian',
                'penyaluran_deposito.created_at',
                'penyaluran_deposito.updated_at',
                'pi.nomor_kontrak as pi_nomor_kontrak',
                'pi.nama_investor as pi_nama_investor',
                'pi.jumlah_investasi as pi_jumlah_investasi',
                'pi.lama_investasi as pi_lama_investasi',
                \DB::raw('COALESCE(pd_sum.total_disalurkan_sum, 0) as total_disalurkan'),
                \DB::raw('(pi.jumlah_investasi - COALESCE(pd_sum.total_disalurkan_sum, 0)) as sisa_dana')
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
                ->label(fn ($row) => '<div class="text-center">'.($row->pi_nomor_kontrak ?? '-').'</div>')
                ->html(),

            Column::make('Nama Investor')
                ->label(fn ($row) => '<div class="text-center">'.($row->pi_nama_investor ?? '-').'</div>')
                ->html(),

            Column::make('Jumlah Investasi')
                ->label(function ($row) {
                    $jumlah = $row->pi_jumlah_investasi ?? null;
                    return '<div class="text-center">'.($jumlah ? 'Rp ' . number_format($jumlah, 0, ',', '.') : '-').'</div>';
                })
                ->html(),

            Column::make('Lama Investasi')
                ->label(function ($row) {
                    $lama = $row->pi_lama_investasi ?? null;
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
                    } else {
                        return '<div class="text-center"><span class="badge bg-label-danger">Belum Lunas</span></div>';
                    }
                })
                ->html(),

            Column::make('Bukti Transfer', 'bukti_pengembalian')
                ->format(function ($value) {
                    if ($value) {
                        return '<div class="text-center">
                            <a href="/storage/' . $value . '" target="_blank" class="text-primary text-decoration-none">
                                <i class="ti ti-file-text me-1"></i> Lihat Dokumen
                            </a>
                        </div>';
                    } else {
                        return '<div class="text-center"><span class="text-muted">-</span></div>';
                    }
                })
                ->html(),
        ];
    }
}
