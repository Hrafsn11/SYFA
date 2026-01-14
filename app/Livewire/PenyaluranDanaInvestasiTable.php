<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PenyaluranDeposito;
use App\Livewire\Traits\HasUniversalFormAction;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PenyaluranDanaInvestasiTable extends DataTableComponent
{
    use HasUniversalFormAction, HasDebiturAuthorization;

    protected $model = PenyaluranDeposito::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_penyaluran_deposito')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari penyaluran dana...')
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
                    '' => 'Semua Tahun',
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

    public function showKontrakDetail($nomorKontrak)
    {
        // Load all penyaluran deposito for this contract number
        $details = PenyaluranDeposito::query()
            ->leftJoin('pengajuan_investasi as pi', 'penyaluran_deposito.id_pengajuan_investasi', '=', 'pi.id_pengajuan_investasi')
            ->where('pi.nomor_kontrak', $nomorKontrak)
            ->select([
                'penyaluran_deposito.*',
                'pi.nomor_kontrak',
                'pi.nama_investor',
                'pi.jumlah_investasi',
                'pi.lama_investasi'
            ])
            ->orderBy('penyaluran_deposito.created_at', 'desc')
            ->get();

        if ($details->isNotEmpty()) {
            $firstDetail = $details->first();

            $kontrakData = [
                'nomor_kontrak' => $firstDetail->nomor_kontrak,
                'nama_investor' => $firstDetail->nama_investor,
                'jumlah_investasi' => $firstDetail->jumlah_investasi,
                'lama_investasi' => $firstDetail->lama_investasi,
                'details' => $details->map(function ($item) {
                    return [
                        'nominal_yang_disalurkan' => $item->nominal_yang_disalurkan,
                        'tanggal_pengiriman_dana' => $item->tanggal_pengiriman_dana,
                        'tanggal_pengembalian' => $item->tanggal_pengembalian,
                        'bukti_pengembalian' => $item->bukti_pengembalian,
                    ];
                })->toArray()
            ];

            $this->dispatch('kontrakDetailLoaded', $kontrakData);
        }
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->user();

        $query = PenyaluranDeposito::query()
            ->leftJoin('pengajuan_investasi as pi', 'penyaluran_deposito.id_pengajuan_investasi', '=', 'pi.id_pengajuan_investasi')
            ->leftJoin('master_debitur_dan_investor', 'pi.id_debitur_dan_investor', '=', 'master_debitur_dan_investor.id_debitur')
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
                \DB::raw('MIN(penyaluran_deposito.id_penyaluran_deposito) as id_penyaluran_deposito'),
                \DB::raw('MIN(penyaluran_deposito.id_pengajuan_investasi) as id_pengajuan_investasi'),
                \DB::raw('MIN(penyaluran_deposito.created_at) as created_at'),
                'pi.nomor_kontrak as pi_nomor_kontrak',
                'pi.nama_investor as pi_nama_investor',
                'pi.jumlah_investasi as pi_jumlah_investasi',
                'pi.lama_investasi as pi_lama_investasi',
                'pi.id_debitur_dan_investor as pi_id_investor',
                \DB::raw('COALESCE(pd_sum.total_disalurkan_sum, 0) as total_disalurkan'),
                \DB::raw('(pi.jumlah_investasi - COALESCE(pd_sum.total_disalurkan_sum, 0)) as sisa_dana'),
                \DB::raw('COUNT(penyaluran_deposito.id_penyaluran_deposito) as jumlah_penyaluran'),
                \DB::raw('SUM(CASE WHEN penyaluran_deposito.bukti_pengembalian IS NOT NULL THEN 1 ELSE 0 END) as jumlah_lunas')
            ])
            ->groupBy('pi.nomor_kontrak', 'pi.nama_investor', 'pi.jumlah_investasi', 'pi.lama_investasi', 'pi.id_debitur_dan_investor', 'pd_sum.total_disalurkan_sum');

        // Restricted data access based on user role
        $isUnrestricted = $user->hasRole('super-admin') ||
            $user->roles()->where('restriction', 1)->exists();

        if (!$isUnrestricted) {
            // Get id_debitur (investor) from user's debitur relation
            $debiturInvestor = $user->debitur;
            $idInvestor = $debiturInvestor ? $debiturInvestor->id_debitur : null;

            // Investor: only see penyaluran from their own pengajuan investasi
            if ($idInvestor) {
                $query->where('pi.id_debitur_dan_investor', $idInvestor);
            } else {
                // User is not Investor and not unrestricted - show nothing
                $query->whereRaw('1 = 0');
            }
        }

        // Custom search for joined tables
        if ($search = $this->getSearch()) {
            $query->havingRaw("pi.nomor_kontrak LIKE ?", ['%' . $search . '%'])
                ->orHavingRaw("pi.nama_investor LIKE ?", ['%' . $search . '%']);
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
                ->label(fn($row) => '<div class="text-center">' . ($row->pi_nama_investor ?? '-') . '</div>')
                ->html(),

            Column::make('Jumlah Investasi', 'pi_jumlah_investasi')
                ->sortable()
                ->label(function ($row) {
                    $jumlah = $row->pi_jumlah_investasi ?? null;
                    return '<div class="text-end">' . ($jumlah ? 'Rp ' . number_format($jumlah, 0, ',', '.') : '-') . '</div>';
                })
                ->html(),

            Column::make('Lama Investasi', 'pi_lama_investasi')
                ->sortable()
                ->label(function ($row) {
                    $lama = $row->pi_lama_investasi ?? null;
                    return '<div class="text-center">' . ($lama ? $lama . ' Bulan' : '-') . '</div>';
                })
                ->html(),

            Column::make('Penyaluran Dana', 'total_disalurkan')
                ->sortable()
                ->label(function ($row) {
                    $total = $row->total_disalurkan ?? 0;
                    return '<div class="text-end">Rp ' . number_format($total, 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sisa Dana')
                ->label(function ($row) {
                    $sisa = $row->sisa_dana ?? 0;
                    $badgeClass = $sisa > 0 ? 'bg-label-success' : 'bg-label-secondary';
                    return '<div class="text-end">
                        <span class="badge ' . $badgeClass . ' px-3 py-2">
                            Rp ' . number_format($sisa, 0, ',', '.') . '
                        </span>
                    </div>';
                })
                ->html(),

            Column::make('Status Pembayaran')
                ->label(function ($row) {
                    $jumlahLunas = $row->jumlah_lunas ?? 0;
                    $jumlahTotal = $row->jumlah_penyaluran ?? 0;

                    if ($jumlahLunas == $jumlahTotal && $jumlahTotal > 0) {
                        return '<div class="text-center"><span class="badge bg-label-success">Lunas (' . $jumlahLunas . '/' . $jumlahTotal . ')</span></div>';
                    } elseif ($jumlahLunas > 0) {
                        return '<div class="text-center"><span class="badge bg-label-warning">Sebagian Lunas (' . $jumlahLunas . '/' . $jumlahTotal . ')</span></div>';
                    } else {
                        return '<div class="text-center"><span class="badge bg-label-danger">Belum Lunas (0/' . $jumlahTotal . ')</span></div>';
                    }
                })
                ->html(),

            Column::make('Action')
                ->label(function ($row) {
                    $nomorKontrak = $row->pi_nomor_kontrak ?? '';
                    return '<div class="text-center">
                        <button wire:click="showKontrakDetail(\'' . $nomorKontrak . '\')" 
                                class="btn btn-sm btn-primary">
                            <i class="ti ti-eye me-1"></i> Lihat Detail
                        </button>
                    </div>';
                })
                ->html(),
        ];
    }
}
