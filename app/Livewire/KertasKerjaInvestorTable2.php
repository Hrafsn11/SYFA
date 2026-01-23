<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PengajuanInvestasi;
use Carbon\Carbon;

class KertasKerjaInvestorTable2 extends DataTableComponent
{
    protected $model = PengajuanInvestasi::class;

    public $year;

    protected $listeners = ['refreshKertasKerjaTable' => '$refresh', 'yearChanged' => 'setYear'];

    public function mount(): void
    {
        $this->year = request()->get('year', date('Y'));
    }

    public function setYear($year)
    {
        $this->year = $year;
        $this->resetPage();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari deposan, nomor kontrak, status...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setTableAttributes(['class' => 'table border-top'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(true)
            ->setBulkActionsDisabled()
            ->setColumnSelectDisabled();
    }

    public function builder(): Builder
    {
        return PengajuanInvestasi::query()
            ->select([
                'id_pengajuan_investasi',
                'tanggal_investasi',
                'jumlah_investasi',
                'lama_investasi',
                'bagi_hasil_pertahun',
                'nomor_kontrak'
            ])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '');
    }

    /**
     * Get CoF per bulan for a row
     */
    private function getCofPerBulan($row): array
    {
        $year = $this->year;

        $bagiHasilPerBulan = $row->bagi_hasil_pertahun / 12;
        $cofBulan = ($row->jumlah_investasi * $bagiHasilPerBulan) / 100;

        $tanggalMulai = Carbon::parse($row->tanggal_investasi);
        $tanggalJatuhTempo = Carbon::parse($row->tanggal_investasi)->addMonths($row->lama_investasi);

        $cofPerBulan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $tanggalBulanIni = Carbon::create($year, $bulan, 1);
            $tanggalAkhirBulanIni = $tanggalBulanIni->copy()->endOfMonth();
            $isAktif = $tanggalMulai->lte($tanggalAkhirBulanIni) && $tanggalJatuhTempo->gte($tanggalBulanIni);
            $cofPerBulan[$bulan] = $isAktif ? $cofBulan : 0;
        }

        return $cofPerBulan;
    }

    public function columns(): array
    {
        return [
            Column::make('Jan')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[1], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Feb')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[2], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Mar')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[3], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Apr')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[4], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Mei')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[5], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Jun')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[6], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Jul')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[7], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Agu')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[8], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sep')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[9], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Okt')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[10], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Nov')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[11], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Des')
                ->label(function ($row) {
                    $cof = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($cof[12], 0, ',', '.') . '</div>';
                })
                ->html(),
        ];
    }
}
