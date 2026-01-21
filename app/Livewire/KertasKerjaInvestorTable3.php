<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasi;

class KertasKerjaInvestorTable3 extends DataTableComponent
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
                'sisa_pokok',
                'sisa_bagi_hasil',
                'nomor_kontrak'
            ])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '');
    }

    /**
     * Get pengembalian data for a row
     */
    private function getPengembalianData($row): array
    {
        $id = $row->id_pengajuan_investasi;

        $total = DB::table('pengembalian_investasi')
            ->select([
                DB::raw('SUM(dana_pokok_dibayar) as total_pokok_all'),
                DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil_all')
            ])
            ->where('id_pengajuan_investasi', $id)
            ->first();

        return [
            'pengembalian_pokok' => $total->total_pokok_all ?? 0,
            'pengembalian_bagi_hasil' => $total->total_bagi_hasil_all ?? 0,
            'total_belum_dikembalikan' => $row->sisa_pokok + $row->sisa_bagi_hasil,
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('Pengembalian Pokok Deposito')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center">Rp ' . number_format($data['pengembalian_pokok'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Pengembalian Bagi Hasil Deposito')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center">Rp ' . number_format($data['pengembalian_bagi_hasil'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sisa Pokok Belum Dikembalikan', 'sisa_pokok')
                ->sortable()
                ->format(fn($value) => '<div class="text-center"><strong class="text-danger">Rp ' . number_format($value, 0, ',', '.') . '</strong></div>')
                ->html(),

            Column::make('Sisa Bagi Hasil Belum Dikembalikan', 'sisa_bagi_hasil')
                ->sortable()
                ->format(fn($value) => '<div class="text-center"><strong class="text-danger">Rp ' . number_format($value, 0, ',', '.') . '</strong></div>')
                ->html(),

            Column::make('Total Belum Dikembalikan')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center"><strong class="text-danger">Rp ' . number_format($data['total_belum_dikembalikan'], 0, ',', '.') . '</strong></div>';
                })
                ->html(),
        ];
    }
}
