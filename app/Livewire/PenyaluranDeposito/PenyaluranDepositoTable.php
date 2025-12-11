<?php

namespace App\Livewire\PenyaluranDeposito;

use App\Models\PenyaluranDeposito;
use App\Livewire\Traits\HasUniversalFormAction;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class PenyaluranDepositoTable extends DataTableComponent
{
    use HasUniversalFormAction;

    protected $model = PenyaluranDeposito::class;

    protected $listeners = ['refreshPenyaluranDepositoTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_penyaluran_deposito')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari data penyaluran...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('id_penyaluran_deposito', 'desc')
            ->setTableAttributes(['class' => 'table table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setBulkActionsDisabled();
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return PenyaluranDeposito::query()
            ->with(['pengajuanInvestasi', 'debitur'])
            ->select('penyaluran_deposito.*');
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

            Column::make('No Kontrak')
                ->label(function ($row) {
                    $noKontrak = $row->pengajuanInvestasi->nomor_kontrak ?? '-';
                    return '<div class="text-center">'.$noKontrak.'</div>';
                })
                ->html()
                ->searchable(),

            Column::make('Nama Perusahaan')
                ->label(function ($row) {
                    $namaPerusahaan = $row->debitur->nama ?? '-';
                    return '<div class="text-start">'.$namaPerusahaan.'</div>';
                })
                ->html()
                ->searchable(),

            Column::make('Nominal Disalurkan', 'nominal_yang_disalurkan')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp. '.number_format($value ?? 0, 0, ',', '.').'</strong></div>';
                })
                ->html(),

            Column::make('Tgl Pengiriman', 'tanggal_pengiriman_dana')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-center">'.\Carbon\Carbon::parse($value)->format('d/m/Y').'</div>';
                })
                ->html(),

            Column::make('Tgl Pengembalian', 'tanggal_pengembalian')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-center">'.\Carbon\Carbon::parse($value)->format('d/m/Y').'</div>';
                })
                ->html(),

            Column::make('Bukti Pengembalian', 'bukti_pengembalian')
                ->format(function ($value, $row) {
                    if ($value) {
                        $fileExtension = pathinfo($value, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']);
                        
                        return '<div class="text-center">
                            <button type="button" class="btn btn-sm btn-success" onclick="previewBukti(\''.$row->id_penyaluran_deposito.'\', \''.$value.'\', '.($isImage ? 'true' : 'false').')">
                                <i class="ti ti-eye me-1"></i>Preview
                            </button>
                        </div>';
                    } else {
                        return '<div class="text-center">
                            <button type="button" class="btn btn-sm btn-primary" onclick="uploadBukti(\''.$row->id_penyaluran_deposito.'\')">
                                <i class="ti ti-upload me-1"></i>Upload
                            </button>
                        </div>';
                    }
                })
                ->html(),

            Column::make('Aksi')
                ->label(function ($row) {
                    $data = [
                        'id' => $row->id_penyaluran_deposito,
                        'id_pengajuan_investasi' => $row->id_pengajuan_investasi,
                        'id_debitur' => $row->id_debitur,
                        'nominal_yang_disalurkan' => $row->nominal_yang_disalurkan,
                        'tanggal_pengiriman_dana' => $row->tanggal_pengiriman_dana->format('Y-m-d'),
                        'tanggal_pengembalian' => $row->tanggal_pengembalian->format('Y-m-d'),
                    ];
                    
                    $jsonData = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');

                    return '<div class="text-center">
                        <button type="button" class="btn btn-sm btn-warning" onclick="editDataDirect(this)" data-item=\'' . $jsonData . '\'>
                            <i class="ti ti-edit"></i>
                        </button>
                    </div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
