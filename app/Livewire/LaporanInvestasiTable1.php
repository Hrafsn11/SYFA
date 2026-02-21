<?php

namespace App\Livewire;

use App\Models\PengajuanInvestasi;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanInvestasiTable1 extends DataTableComponent
{
    public $year;

    protected $model = PengajuanInvestasi::class;

    protected $listeners = ['refreshKertasKerjaTable' => '$refresh', 'yearChanged', 'globalSearchChanged'];

    public $globalSearch = '';

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi')
            ->setSearchEnabled() // Disable default search, use global
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('created_at', 'desc')
            ->setTableAttributes(['class' => 'table table-hover table-bordered table-sm'])
            ->setTheadAttributes(['class' => 'table-light text-center'])
            ->setSearchFieldAttributes(['class' => 'd-none']) // Hide default search box
            ->setPerPageFieldAttributes(['class' => 'd-none']) // Hide default per page
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(false)
            ->setBulkActionsDisabled();
    }

    public function yearChanged($year)
    {
        $this->year = $year;
    }

    public function globalSearchChanged($value)
    {
        $this->globalSearch = $value;
    }

    public function builder(): Builder
    {
        $query = PengajuanInvestasi::query()
            ->select([
                'id_pengajuan_investasi',
                'tanggal_investasi',
                'jenis_investasi',
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

        // Apply global search filter
        if (!empty($this->globalSearch)) {
            $search = $this->globalSearch;
            $query->where(function ($q) use ($search) {
                $q->where('nama_investor', 'like', '%' . $search . '%')
                    ->orWhere('nomor_kontrak', 'like', '%' . $search . '%')
                    ->orWhere('jenis_investasi', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        // Apply year filter - filter by year of tanggal_investasi
        if (!empty($this->year)) {
            $query->whereYear('tanggal_investasi', $this->year);
        }

        return $query;
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
                ->label(function ($row) {
                    $value = Carbon::parse($row->tanggal_investasi)->format('d-m-Y');
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'tanggal_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Jenis Investasi', 'jenis_investasi')
                ->sortable()
                ->searchable(function (Builder $builder, $term) {
                    $builder->orWhere('jenis_investasi', 'like', '%' . $term . '%')
                        ->orWhere('nomor_kontrak', 'like', '%' . $term . '%');
                })
                ->label(function ($row) {
                    $value = $row->jenis_investasi ?? '-';
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'jenis_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Nama Investor', 'nama_investor')
                ->sortable()
                ->searchable(function (Builder $builder, $term) {
                    $builder->orWhere('nama_investor', 'like', '%' . $term . '%');
                })
                ->label(function ($row) {
                    $value = $row->nama_investor;
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'nama_investor\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Nominal Investasi', 'jumlah_investasi')
                ->sortable()
                ->label(function ($row) {
                    $value = 'Rp ' . number_format($row->jumlah_investasi, 0, ',', '.');
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'jumlah_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Lama Investasi', 'lama_investasi')
                ->sortable()
                ->label(function ($row) {
                    $value = $row->lama_investasi . ' Bulan';
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'lama_investasi\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Bagi Hasil (%PA)', 'bagi_hasil_pertahun')
                ->sortable()
                ->label(function ($row) {
                    $value = number_format($row->bagi_hasil_pertahun, 2) . '%';
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'bagi_hasil_pertahun\'})"></i>
                    </div>';
                })
                ->html(),

            Column::make('Bagi Hasil Nominal', 'nominal_bagi_hasil_yang_didapatkan')
                ->sortable()
                ->label(function ($row) {
                    $value = 'Rp ' . number_format($row->nominal_bagi_hasil_yang_didapatkan, 0, ',', '.');
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell">' . $value . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'nominal_bagi_hasil_yang_didapatkan\'})"></i>
                    </div>';
                })
                ->html(),

            // Calculated field - NO EDIT
            Column::make('Bagi Hasil (%Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">' . number_format($calc['bagi_hasil_per_bulan'], 2) . '%</div>';
                })
                ->html(),

            // Calculated field - NO EDIT
            Column::make('Bagi Hasil (COF/Bulan)')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['cof_bulan'], 0, ',', '.') . '</div>';
                })
                ->html(),

            // Calculated field - NO EDIT
            Column::make('CoF Per Akhir Des')
                ->label(function ($row) {
                    $calc = $this->getCalculatedData($row);
                    return '<div class="text-center">Rp ' . number_format($calc['cof_akhir_periode'], 0, ',', '.') . '</div>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable(function (Builder $builder, $term) {
                    $builder->orWhere('status', 'like', '%' . $term . '%');
                })
                ->label(function ($row) {
                    $value = $row->status;
                    $id = $row->id_pengajuan_investasi;
                    $badge = $value === 'Lunas'
                        ? '<span class="badge bg-label-success">Lunas</span>'
                        : '<span class="badge bg-label-warning">Aktif</span>';
                    return '<div class="text-center editable-cell">' . $badge . '
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'status\'})"></i>
                    </div>';
                })
                ->html(),

            // Tgl Pengembalian from another table - NO EDIT
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
