<?php

namespace App\Livewire\SFinlog;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PengajuanInvestasiFinlog;
use Carbon\Carbon;

class KertasKerjaInvestorSFinlogTable2 extends DataTableComponent
{
    protected $model = PengajuanInvestasiFinlog::class;

    public $year;
    public $externalSearch = '';

    protected $listeners = [
        'refreshKertasKerjaTable' => '$refresh',
        'yearChanged' => 'setYear',
        'searchChanged' => 'setExternalSearch',
        'perPageChanged' => 'setExternalPerPage',
    ];

    public function mount(): void
    {
        $this->year = request()->get('year', date('Y'));
        $this->externalSearch = request()->get('search', '');
    }

    public function setYear($year)
    {
        $this->year = $year;
        $this->resetPage();
    }

    public function setExternalSearch($search)
    {
        $this->externalSearch = $search;
        $this->resetPage();
    }

    public function setExternalPerPage($perPage)
    {
        $this->setPerPage($perPage);
        $this->resetPage();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi_finlog')
            ->setSearchDisabled() // Disable built-in search, use parent's search
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityDisabled() // Disable, controlled by parent
            ->setPerPage(10)
            ->setTableAttributes(['class' => 'table border-top'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setFiltersDisabled() // No filters for this table
            ->setBulkActionsDisabled()
            ->setColumnSelectDisabled()
            ->setPaginationDisabled(); // Disable pagination, controlled by parent
    }

    public function builder(): Builder
    {
        $query = PengajuanInvestasiFinlog::query()
            ->select([
                'id_pengajuan_investasi_finlog',
                'tanggal_investasi',
                'nama_investor',
                'nominal_investasi',
                'lama_investasi',
                'persentase_bagi_hasil',
                'nomor_kontrak',
                'status' // Added for search
            ])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '');

        // Apply external search
        if (!empty($this->externalSearch)) {
            $search = $this->externalSearch;
            $query->where(function ($q) use ($search) {
                $q->where('nama_investor', 'like', '%' . $search . '%')
                    ->orWhere('nomor_kontrak', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    /**
     * Get CoF per bulan for a row (based on active investment period)
     */
    private function getCofPerBulan($row): array
    {
        $year = $this->year;

        // Bagi hasil (nominal/pa) = bagi hasil(%pa) × nominal deposito / 100
        $bagiHasilNominalPa = ($row->persentase_bagi_hasil * $row->nominal_investasi) / 100;

        // Bagi hasil (COF/bulan) = bagi hasil (nominal/pa) / 12
        $cofBulan = $bagiHasilNominalPa / 12;

        $tanggalMulai = Carbon::parse($row->tanggal_investasi);
        $tanggalJatuhTempo = Carbon::parse($row->tanggal_investasi)->addMonths($row->lama_investasi);

        $cofPerBulan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $tanggalBulanIni = Carbon::create($year, $bulan, 1);
            $tanggalAkhirBulanIni = $tanggalBulanIni->copy()->endOfMonth();

            // Cek apakah investasi aktif di bulan ini
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
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[1], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Feb')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[2], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Mar')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[3], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Apr')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[4], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Mei')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[5], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Jun')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[6], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Jul')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[7], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Agu')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[8], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Sep')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[9], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Okt')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[10], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Nov')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[11], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Des')
                ->label(function ($row) {
                    $data = $this->getCofPerBulan($row);
                    return '<div class="text-center">Rp ' . number_format($data[12], 0, ',', '.') . '</div>';
                })
                ->html(),
        ];
    }
}
