<?php

namespace App\Livewire\SFinlog;

use App\Models\PenyaluranDepositoSfinlog;
use App\Livewire\Traits\HasUniversalFormAction;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;

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
            ->setDefaultSort('created_at', 'desc')
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

            SelectFilter::make('Status Pengembalian')
                ->options([
                    '' => 'Semua Status',
                    'lunas' => 'Lunas',
                    'sebagian' => 'Sebagian Lunas',
                    'belum' => 'Belum Lunas',
                ])
                ->filter(function (\Illuminate\Database\Eloquent\Builder $builder, string $value) {
                    if (!empty($value)) {
                        switch ($value) {
                            case 'lunas':
                                // Lunas: total_dikembalikan >= total_disalurkan AND total_disalurkan > 0
                                $builder->havingRaw('SUM(penyaluran_deposito_sfinlog.nominal_yang_dikembalikan) >= SUM(penyaluran_deposito_sfinlog.nominal_yang_disalurkan) AND SUM(penyaluran_deposito_sfinlog.nominal_yang_disalurkan) > 0');
                                break;
                            case 'sebagian':
                                // Sebagian Lunas: total_dikembalikan > 0 AND total_dikembalikan < total_disalurkan
                                $builder->havingRaw('SUM(penyaluran_deposito_sfinlog.nominal_yang_dikembalikan) > 0 AND SUM(penyaluran_deposito_sfinlog.nominal_yang_dikembalikan) < SUM(penyaluran_deposito_sfinlog.nominal_yang_disalurkan)');
                                break;
                            case 'belum':
                                // Belum Lunas: total_dikembalikan = 0 OR total_dikembalikan IS NULL
                                $builder->havingRaw('COALESCE(SUM(penyaluran_deposito_sfinlog.nominal_yang_dikembalikan), 0) = 0');
                                break;
                        }
                    }
                }),
        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        // Group by nomor kontrak
        return PenyaluranDepositoSfinlog::query()
            ->with(['pengajuanInvestasiFinlog.investor'])
            ->leftJoin('pengajuan_investasi_finlog', 'penyaluran_deposito_sfinlog.id_pengajuan_investasi_finlog', '=', 'pengajuan_investasi_finlog.id_pengajuan_investasi_finlog')
            ->selectRaw('
                MIN(penyaluran_deposito_sfinlog.id_penyaluran_deposito_sfinlog) as id_penyaluran_deposito_sfinlog,
                penyaluran_deposito_sfinlog.id_pengajuan_investasi_finlog,
                COUNT(*) as jumlah_penyaluran,
                COALESCE(SUM(penyaluran_deposito_sfinlog.nominal_yang_disalurkan), 0) as total_disalurkan,
                (
                    SELECT COALESCE(SUM(r.nominal_dikembalikan), 0)
                    FROM riwayat_pengembalian_deposito_sfinlog r
                    INNER JOIN penyaluran_deposito_sfinlog p2 ON r.id_penyaluran_deposito_sfinlog = p2.id_penyaluran_deposito_sfinlog
                    WHERE p2.id_pengajuan_investasi_finlog = penyaluran_deposito_sfinlog.id_pengajuan_investasi_finlog
                ) as total_dikembalikan,
                MAX(penyaluran_deposito_sfinlog.created_at) as latest_created_at
            ')
            ->groupBy('penyaluran_deposito_sfinlog.id_pengajuan_investasi_finlog')
            ->orderBy('latest_created_at', 'desc');
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

            Column::make('No Kontrak', 'pengajuan_investasi_finlog.nomor_kontrak')
                ->label(function ($row) {
                    $noKontrak = $row->pengajuanInvestasiFinlog?->nomor_kontrak ?? '-';
                    return '<div class="text-center"><strong>' . $noKontrak . '</strong></div>';
                })
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhere('pengajuan_investasi_finlog.nomor_kontrak', 'LIKE', '%' . $searchTerm . '%');
                }),

            Column::make('Nama Investor', 'pengajuan_investasi_finlog.nama_investor')
                ->label(function ($row) {
                    $namaInvestor = $row->pengajuanInvestasiFinlog?->nama_investor ?? '-';
                    return '<div class="text-start">' . $namaInvestor . '</div>';
                })
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhere('pengajuan_investasi_finlog.nama_investor', 'LIKE', '%' . $searchTerm . '%');
                }),

            Column::make('Jumlah Investasi')
                ->label(function ($row) {
                    $jumlahInvestasi = $row->pengajuanInvestasiFinlog?->nominal_investasi ?? 0;
                    return '<div class="text-end">Rp ' . number_format($jumlahInvestasi, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Lama Investasi', 'pengajuan_investasi_finlog.lama_investasi')
                ->label(function ($row) {
                    $lamaInvestasi = $row->pengajuanInvestasiFinlog?->lama_investasi ?? 0;
                    return '<div class="text-center">' . $lamaInvestasi . ' Bulan</div>';
                })
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhere('pengajuan_investasi_finlog.lama_investasi', 'LIKE', '%' . $searchTerm . '%');
                }),

            Column::make('Penyaluran Dana')
                ->label(function ($row) {
                    $total = $row->total_disalurkan ?? 0;
                    return '<div class="text-end"><strong>Rp ' . number_format($total, 0, ',', '.') . '</strong></div>';
                })
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->havingRaw('SUM(penyaluran_deposito_sfinlog.nominal_yang_disalurkan) LIKE ?', ['%' . $searchTerm . '%']);
                }),

            Column::make('Total Dikembalikan')
                ->label(function ($row) {
                    $idPengajuan = $row->id_pengajuan_investasi_finlog;
                    $total = \DB::table('riwayat_pengembalian_deposito_sfinlog as r')
                        ->join('penyaluran_deposito_sfinlog as p', 'r.id_penyaluran_deposito_sfinlog', '=', 'p.id_penyaluran_deposito_sfinlog')
                        ->where('p.id_pengajuan_investasi_finlog', $idPengajuan)
                        ->sum('r.nominal_dikembalikan');

                    return '<div class="text-end"><strong class="text-success">Rp ' . number_format($total, 0, ',', '.') . '</strong></div>';
                })
                ->html(),

            Column::make('Status Pengembalian')
                ->label(function ($row) {
                    $totalDisalurkan = floatval($row->total_disalurkan ?? 0);

                    $idPengajuan = $row->id_pengajuan_investasi_finlog;
                    $totalDikembalikan = floatval(\DB::table('riwayat_pengembalian_deposito_sfinlog as r')
                        ->join('penyaluran_deposito_sfinlog as p', 'r.id_penyaluran_deposito_sfinlog', '=', 'p.id_penyaluran_deposito_sfinlog')
                        ->where('p.id_pengajuan_investasi_finlog', $idPengajuan)
                        ->sum('r.nominal_dikembalikan'));

                    if ($totalDikembalikan >= $totalDisalurkan && $totalDisalurkan > 0) {
                        $badge = '<span class="badge bg-label-success">Lunas (100%)</span>';
                    } elseif ($totalDikembalikan > 0) {
                        $percentage = $totalDisalurkan > 0 ? round(($totalDikembalikan / $totalDisalurkan) * 100) : 0;
                        $badge = '<span class="badge bg-label-warning">Sebagian Lunas (' . $percentage . '%)</span>';
                    } else {
                        $badge = '<span class="badge bg-label-danger">Belum Lunas (0%)</span>';
                    }

                    return '<div class="text-center">' . $badge . '</div>';
                })
                ->html(),

            Column::make('Action')
                ->label(function ($row) {
                    $nomorKontrak = $row->pengajuanInvestasiFinlog?->nomor_kontrak ?? '';

                    return '<div class="text-center">
                        <button type="button" class="btn btn-sm btn-info" 
                            wire:click="showKontrakDetail(\'' . $nomorKontrak . '\')"
                            title="Lihat Detail Penyaluran">
                            <i class="ti ti-eye me-1"></i>Lihat Detail
                        </button>
                    </div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }

    /**
     * Show detail kontrak
     */
    public function showKontrakDetail($nomorKontrak)
    {
        \Log::info('showKontrakDetail called with: ' . $nomorKontrak);

        $pengajuan = \App\Models\PengajuanInvestasiFinlog::where('nomor_kontrak', $nomorKontrak)->first();

        if (!$pengajuan) {
            \Log::warning('Pengajuan not found for contract: ' . $nomorKontrak);
            return;
        }

        $penyaluranList = PenyaluranDepositoSfinlog::where('id_pengajuan_investasi_finlog', $pengajuan->id_pengajuan_investasi_finlog)
            ->with(['cellsProject', 'project'])
            ->orderBy('tanggal_pengiriman_dana', 'desc')
            ->get();

        $kontrakData = [
            'nomor_kontrak' => $pengajuan->nomor_kontrak,
            'nama_investor' => $pengajuan->nama_investor,
            'nominal_investasi' => $pengajuan->nominal_investasi,
            'lama_investasi' => $pengajuan->lama_investasi,
            'details' => $penyaluranList->map(function ($item) {
                $penyaluranModel = PenyaluranDepositoSfinlog::find($item->id_penyaluran_deposito_sfinlog);
                $sisaBelumDikembalikan = $penyaluranModel ? $penyaluranModel->sisa_belum_dikembalikan : ($item->nominal_yang_disalurkan - ($item->nominal_yang_dikembalikan ?? 0));
                $totalDikembalikan = $penyaluranModel ? $penyaluranModel->total_dikembalikan : ($item->nominal_yang_dikembalikan ?? 0);

                // Calculate status
                if ($sisaBelumDikembalikan <= 0) {
                    $status = 'Lunas';
                } elseif ($totalDikembalikan > 0) {
                    $status = 'Sebagian Lunas';
                } else {
                    $status = 'Belum Lunas';
                }

                return [
                    'id' => $item->id_penyaluran_deposito_sfinlog,
                    'cell_bisnis' => $item->cellsProject?->nama_cells_bisnis ?? '-',
                    'project' => $item->project?->nama_project ?? '-',
                    'nominal_yang_disalurkan' => floatval($item->nominal_yang_disalurkan ?? 0),
                    'nominal_yang_dikembalikan' => $totalDikembalikan,
                    'sisa_belum_dikembalikan' => $sisaBelumDikembalikan,
                    'tanggal_pengiriman_dana' => $item->tanggal_pengiriman_dana?->format('Y-m-d'),
                    'tanggal_pengembalian' => $item->tanggal_pengembalian?->format('Y-m-d'),
                    'status' => $status,
                ];
            })->toArray()
        ];

        \Log::info('Dispatching kontrak-detail-loaded with data', $kontrakData);
        $this->dispatch('kontrak-detail-loaded', data: $kontrakData);
    }
}
