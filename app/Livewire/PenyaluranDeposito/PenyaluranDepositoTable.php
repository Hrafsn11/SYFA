<?php

namespace App\Livewire\PenyaluranDeposito;

use App\Models\PenyaluranDeposito;
use App\Livewire\Traits\HasUniversalFormAction;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

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
            ->setDefaultSort('penyaluran_deposito.created_at', 'desc')
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
                        $builder->whereRaw("MONTH(penyaluran_deposito.tanggal_pengiriman_dana) = ?", [$value]);
                    }
                }),

            SelectFilter::make('Tahun')
                ->options([
                    '' => 'SemTahun',
                    '2023' => '2023',
                    '2024' => '2024',
                    '2025' => '2025',
                    '2026' => '2026',
                    '2027' => '2027',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->whereRaw("YEAR(penyaluran_deposito.tanggal_pengiriman_dana) = ?", [$value]);
                    }
                }),

            SelectFilter::make('Status Pembayaran')
                ->options([
                    '' => 'Semua Status',
                    'lunas' => 'Lunas',
                    'belum_lunas' => 'Belum Lunas',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'lunas') {
                        $builder->whereNotNull('penyaluran_deposito.bukti_pengembalian');
                    } elseif ($value === 'belum_lunas') {
                        $builder->whereNull('penyaluran_deposito.bukti_pengembalian');
                    }
                }),
        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->user();

        $query = PenyaluranDeposito::query()
            ->with(['pengajuanInvestasi', 'debitur'])
            ->leftJoin('pengajuan_investasi as pi', 'penyaluran_deposito.id_pengajuan_investasi', '=', 'pi.id_pengajuan_investasi')
            ->leftJoin('master_debitur_dan_investor as mdi', 'penyaluran_deposito.id_debitur', '=', 'mdi.id_debitur')
            ->select([
                'penyaluran_deposito.*',
                'pi.nomor_kontrak as pi_nomor_kontrak',
                'mdi.nama as mdi_nama'
            ]);

        // Restricted data access based on user role
        $isUnrestricted = $user->hasRole('super-admin') ||
            $user->roles()->where('restriction', 1)->exists();

        if (!$isUnrestricted) {
            // Get id_debitur from user's debitur relation
            $debitur = $user->debitur;
            $idDebitur = $debitur ? $debitur->id_debitur : null;

            // Debitur: only see their own data (penyaluran yang ditujukan kepada mereka)
            if ($idDebitur) {
                $query->where('penyaluran_deposito.id_debitur', $idDebitur);
            } else {
                // User is not Debitur and not unrestricted - show nothing
                $query->whereRaw('1 = 0');
            }
        }

        // Custom search for joined tables
        if ($search = $this->getSearch()) {
            $query->where(function ($q) use ($search) {
                $q->where('pi.nomor_kontrak', 'LIKE', '%' . $search . '%')
                    ->orWhere('mdi.nama', 'LIKE', '%' . $search . '%')
                    ->orWhere('penyaluran_deposito.nominal_yang_disalurkan', 'LIKE', '%' . $search . '%')
                    ->orWhereRaw("DATE_FORMAT(penyaluran_deposito.tanggal_pengiriman_dana, '%d/%m/%Y') LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("DATE_FORMAT(penyaluran_deposito.tanggal_pengembalian, '%d/%m/%Y') LIKE ?", ['%' . $search . '%']);
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

            Column::make('No Kontrak', 'pi_nomor_kontrak')
                ->sortable()
                ->label(function ($row) {
                    $noKontrak = $row->pi_nomor_kontrak ?? '-';
                    return '<div class="text-center">' . $noKontrak . '</div>';
                })
                ->html(),

            Column::make('Nama Perusahaan', 'mdi_nama')
                ->sortable()
                ->label(function ($row) {
                    $namaPerusahaan = $row->mdi_nama ?? '-';
                    return '<div class="text-start">' . $namaPerusahaan . '</div>';
                })
                ->html(),

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
                            <button type="button" class="btn btn-sm btn-success" onclick="previewBukti(\'' . $row->id_penyaluran_deposito . '\', \'' . $value . '\', ' . ($isImage ? 'true' : 'false') . ')">
                                <i class="ti ti-file me-1"></i>Preview
                            </button>
                        </div>';
                    } else {
                        if (!auth()->user()->can('penyaluran_deposito.upload_bukti')) {
                            return '<div class="text-center"><span class="badge bg-secondary">Belum Upload</span></div>';
                        }

                        return '<div class="text-center">
                            <button type="button" class="btn btn-sm btn-primary" onclick="uploadBukti(\'' . $row->id_penyaluran_deposito . '\')">
                                <i class="ti ti-upload me-1"></i>Upload
                            </button>
                        </div>';
                    }
                })
                ->html(),

            Column::make('Aksi')
                ->label(function ($row) {
                    if (!auth()->user()->can('penyaluran_deposito.edit')) {
                        return ''; // Return empty for Debitur
                    }

                    $data = [
                        'id' => $row->id_penyaluran_deposito,
                        'id_pengajuan_investasi' => $row->id_pengajuan_investasi,
                        'id_debitur' => $row->id_debitur,
                        'nominal_yang_disalurkan' => (int) $row->nominal_yang_disalurkan,  // Cast to int!
                        'tanggal_pengiriman_dana' => $row->tanggal_pengiriman_dana->format('Y-m-d'),
                        'tanggal_pengembalian' => $row->tanggal_pengembalian->format('Y-m-d'),
                    ];

                    $jsonData = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');

                    return '<div class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="editDataDirect(this)" data-item=\'' . $jsonData . '\'>
                            <i class="ti ti-edit"></i>
                        </button>
                    </div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
