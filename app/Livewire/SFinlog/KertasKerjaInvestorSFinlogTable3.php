<?php

namespace App\Livewire\SFinlog;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasiFinlog;

class KertasKerjaInvestorSFinlogTable3 extends DataTableComponent
{
    protected $model = PengajuanInvestasiFinlog::class;

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
        $this->setPrimaryKey('id_pengajuan_investasi_finlog')
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
        return PengajuanInvestasiFinlog::query()
            ->select([
                'id_pengajuan_investasi_finlog',
                'tanggal_investasi',
                'nama_investor',
                'nominal_investasi',
                'lama_investasi',
                'persentase_bagi_hasil',
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
        $id = $row->id_pengajuan_investasi_finlog;

        // Get total pengembalian
        $total = DB::table('pengembalian_investasi_finlog')
            ->select([
                DB::raw('SUM(dana_pokok_dibayar) as total_pokok_all'),
                DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil_all')
            ])
            ->where('id_pengajuan_investasi_finlog', $id)
            ->first();

        // Calculate bagi hasil per nominal
        $bagiHasilNominalPa = ($row->persentase_bagi_hasil * $row->nominal_investasi) / 100;
        $cofBulan = $bagiHasilNominalPa / 12;
        $bagiHasilPerNominal = $row->lama_investasi * $cofBulan;

        $totalPokokDikembalikan = $total->total_pokok_all ?? 0;
        $totalBagiHasilDibayar = $total->total_bagi_hasil_all ?? 0;

        // Sisa pokok = nominal investasi - total pokok yang sudah dikembalikan
        $sisaPokok = max(0, $row->nominal_investasi - $totalPokokDikembalikan);

        // Sisa bagi hasil = bagi hasil per nominal - total bagi hasil yang sudah dibayar
        $sisaBagiHasil = max(0, $bagiHasilPerNominal - $totalBagiHasilDibayar);

        return [
            'pengembalian_pokok' => $totalPokokDikembalikan,
            'pengembalian_bagi_hasil' => $totalBagiHasilDibayar,
            'sisa_pokok' => $sisaPokok,
            'sisa_bagi_hasil' => $sisaBagiHasil,
            'total_belum_dikembalikan' => $sisaPokok + $sisaBagiHasil,
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

            Column::make('Sisa Pokok Belum Dikembalikan')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center"><strong class="text-danger">Rp ' . number_format($data['sisa_pokok'], 0, ',', '.') . '</strong></div>';
                })
                ->html(),

            Column::make('Sisa Bagi Hasil Belum Dikembalikan')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center"><strong class="text-danger">Rp ' . number_format($data['sisa_bagi_hasil'], 0, ',', '.') . '</strong></div>';
                })
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
