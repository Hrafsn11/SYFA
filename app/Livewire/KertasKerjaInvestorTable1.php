<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasi;
use Carbon\Carbon;

class KertasKerjaInvestorTable1 extends DataTableComponent
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

    public function filters(): array
    {
        $years = [];
        for ($y = date('Y'); $y >= date('Y') - 10; $y--) {
            $years[(string) $y] = (string) $y;
        }

        return [
            SelectFilter::make('Tahun', 'year')
                ->options(array_merge(['' => 'Semua Tahun'], $years))
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $this->year = (int) $value;
                        // Dispatch event to sync other tables
                        $this->dispatch('yearChanged', $this->year);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        return PengajuanInvestasi::query()
            ->select([
                'id_pengajuan_investasi',
                'tanggal_investasi',
                'deposito',
                'nama_investor',
                'jumlah_investasi',
                'lama_investasi',
                'bagi_hasil_pertahun',
                'nominal_bagi_hasil_yang_didapatkan',
                'sisa_pokok',
                'sisa_bagi_hasil',
                'status',
                'nomor_kontrak'
            ])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '');
    }

    /**
     * Get calculated data for a row
     */
    private function getCalculatedData($row): array
    {
        $year = $this->year;
        $id = $row->id_pengajuan_investasi;

        // Get tanggal pengembalian terakhir
        $tglTerakhir = DB::table('pengembalian_investasi')
            ->where('id_pengajuan_investasi', $id)
            ->max('tanggal_pengembalian');

        $bagiHasilPerBulan = $row->bagi_hasil_pertahun / 12;
        $cofBulan = ($row->jumlah_investasi * $bagiHasilPerBulan) / 100;

        $tanggalMulai = Carbon::parse($row->tanggal_investasi);
        $tanggalAkhirPeriode = Carbon::create($year, 12, 31);

        // Hitung CoF akhir periode
        if ($tanggalMulai->year > $year) {
            $cofAkhirPeriode = 0;
        } else {
            $tanggalSekarang = Carbon::now();
            $tanggalBatas = $tanggalAkhirPeriode->lt($tanggalSekarang) ? $tanggalAkhirPeriode : $tanggalSekarang;

            $bulanBerjalan = max(0, $tanggalMulai->diffInMonths($tanggalBatas) + 1);

            $totalSeharusnya = $cofBulan * $bulanBerjalan;

            $totalDibayar = DB::table('pengembalian_investasi')
                ->where('id_pengajuan_investasi', $id)
                ->where('tanggal_pengembalian', '<=', $tanggalBatas)
                ->sum('bagi_hasil_dibayar');

            $cofAkhirPeriode = max(0, $totalSeharusnya - $totalDibayar);
        }

        return [
            'bagi_hasil_per_bulan' => $bagiHasilPerBulan,
            'cof_bulan' => $cofBulan,
            'cof_akhir_periode' => $cofAkhirPeriode,
            'tgl_pengembalian' => $tglTerakhir,
        ];
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

            Column::make('Tanggal Uang Masuk', 'tanggal_investasi')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">' . Carbon::parse($value)->format('d-m-Y') . '</div>')
                ->html(),

            Column::make('Deposito', 'deposito')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?? '-') . '</div>')
                ->html(),

            Column::make('Deposan', 'nama_investor')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . $value . '</div>')
                ->html(),

            Column::make('Nominal Deposit', 'jumlah_investasi')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">Rp ' . number_format($value, 0, ',', '.') . '</div>')
                ->html(),

            Column::make('Lama Deposito', 'lama_investasi')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">' . $value . ' Bulan</div>')
                ->html(),

            Column::make('Bagi Hasil (%PA)', 'bagi_hasil_pertahun')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">' . number_format($value, 2) . '%</div>')
                ->html(),

            Column::make('Bagi Hasil Nominal', 'nominal_bagi_hasil_yang_didapatkan')
                ->sortable()
                ->format(fn($value) => '<div class="text-center">Rp ' . number_format($value, 0, ',', '.') . '</div>')
                ->html(),

            Column::make('Bagi Hasil (%Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">' . number_format($calc['bagi_hasil_per_bulan'], 2) . '%</div>';
                })
                ->html(),

            Column::make('Bagi Hasil (COF/Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['cof_bulan'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('CoF Per Akhir Des')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['cof_akhir_periode'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    if ($value === 'Lunas') {
                        return '<div class="text-center"><span class="badge bg-label-success">Lunas</span></div>';
                    }
                    return '<div class="text-center"><span class="badge bg-label-warning">Aktif</span></div>';
                })
                ->html(),

            Column::make('Tgl Pengembalian')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    $tgl = $calc['tgl_pengembalian'];
                    return '<div class="text-center">' . ($tgl ? Carbon::parse($tgl)->format('d-m-Y') : '-') . '</div>';
                })
                ->html(),
        ];
    }
}
