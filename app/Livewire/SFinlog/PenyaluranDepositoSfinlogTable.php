<?php

namespace App\Livewire\SFinlog;

use App\Models\PenyaluranDepositoSfinlog;
use App\Livewire\Traits\HasUniversalFormAction;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class PenyaluranDepositoSfinlogTable extends DataTableComponent
{
    use HasUniversalFormAction;

    protected $model = PenyaluranDepositoSfinlog::class;

    protected $listeners = ['refreshPenyaluranDepositoSfinlogTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_penyaluran_deposito_sfinlog')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari data penyaluran...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('id_penyaluran_deposito_sfinlog', 'desc')
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
                ->filter(function (\Illuminate\Database\Eloquent\Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->whereRaw("MONTH(penyaluran_deposito_sfinlog.tanggal_pengiriman_dana) = ?", [$value]);
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
                ->filter(function (\Illuminate\Database\Eloquent\Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->whereRaw("YEAR(penyaluran_deposito_sfinlog.tanggal_pengiriman_dana) = ?", [$value]);
                    }
                }),
        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return PenyaluranDepositoSfinlog::query()
            ->with(['pengajuanInvestasiFinlog.project.projects', 'pengajuanInvestasiFinlog.investor', 'cellsProject', 'project'])
            ->select('penyaluran_deposito_sfinlog.*');
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

            Column::make('No Kontrak')
                ->label(function ($row) {
                    $noKontrak = $row->pengajuanInvestasiFinlog?->nomor_kontrak ?? '-';
                    return '<div class="text-center">' . $noKontrak . '</div>';
                })
                ->html()
                ->searchable(),

            Column::make('Cell Bisnis')
                ->label(function ($row) {
                    $cellBisnis = $row->cellsProject?->nama_cells_bisnis ?? '-';
                    return '<div class="text-start">' . $cellBisnis . '</div>';
                })
                ->html()
                ->searchable(),

            Column::make('Project')
                ->label(function ($row) {
                    $projectName = $row->project?->nama_project ?? '-';
                    return '<div class="text-start">' . $projectName . '</div>';
                })
                ->html()
                ->searchable(),

            Column::make('Nominal Disalurkan', 'nominal_yang_disalurkan')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp. ' . number_format($value ?? 0, 0, ',', '.') . '</strong></div>';
                })
                ->html(),

            Column::make('Tgl Pengiriman', 'tanggal_pengiriman_dana')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-center">' . \Carbon\Carbon::parse($value)->format('d/m/Y') . '</div>';
                })
                ->html(),

            Column::make('Tgl Pengembalian', 'tanggal_pengembalian')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-center">' . \Carbon\Carbon::parse($value)->format('d/m/Y') . '</div>';
                })
                ->html(),

            Column::make('Bukti Pengembalian', 'bukti_pengembalian')
                ->format(function ($value, $row) {
                    if ($value) {
                        $fileExtension = pathinfo($value, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']);

                        return '<div class="text-center">
                            <button type="button" class="btn btn-sm btn-success" onclick="previewBukti(\'' . $row->id_penyaluran_deposito_sfinlog . '\', \'' . $value . '\', ' . ($isImage ? 'true' : 'false') . ')">
                                <i class="ti ti-eye me-1"></i>Preview
                            </button>
                        </div>';
                    } else {
                        // Check if user has upload_bukti permission
                        if (auth()->user() && auth()->user()->can('penyaluran_deposito_finlog.upload_bukti')) {
                            return '<div class="text-center">
                                <button type="button" class="btn btn-sm btn-primary" onclick="uploadBukti(\'' . $row->id_penyaluran_deposito_sfinlog . '\')">
                                    <i class="ti ti-upload me-1"></i>Upload
                                </button>
                            </div>';
                        }
                        return '<div class="text-center"><span class="text-muted">-</span></div>';
                    }
                })
                ->html(),

            Column::make('Aksi')
                ->label(function ($row) {
                    if (!auth()->user() || !auth()->user()->can('penyaluran_deposito_finlog.edit')) {
                        return '';
                    }

                    $data = [
                        'id' => $row->id_penyaluran_deposito_sfinlog,
                        'id_pengajuan_investasi_finlog' => $row->id_pengajuan_investasi_finlog,
                        'id_cells_project' => $row->id_cells_project,
                        'id_project' => $row->id_project,
                        'nominal_yang_disalurkan' => (int) $row->nominal_yang_disalurkan,
                        'tanggal_pengiriman_dana' => $row->tanggal_pengiriman_dana?->format('Y-m-d'),
                        'tanggal_pengembalian' => $row->tanggal_pengembalian?->format('Y-m-d'),
                    ];

                    $encodedData = base64_encode(json_encode($data));

                    return '<div class="text-center">
                        <button type="button" class="btn btn-sm btn-warning" onclick="editDataDirect(this)" data-item="' . $encodedData . '">
                            <i class="ti ti-edit"></i>
                        </button>
                    </div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
