<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasi;

class LaporanInvestasiTable3 extends DataTableComponent
{
    protected $model = PengajuanInvestasi::class;

    public $year;
    public $globalSearch = '';

    protected $listeners = ['refreshKertasKerjaTable' => '$refresh', 'yearChanged' => 'setYear', 'globalSearchChanged' => 'setGlobalSearch'];

    public function mount(): void
    {
        $this->year = request()->get('year', date('Y'));
    }

    public function setYear($year)
    {
        $this->year = $year;
        $this->resetPage();
    }

    public function setGlobalSearch($search)
    {
        $this->globalSearch = $search;
        $this->resetPage();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi')
            ->setSearchDisabled()
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setTableAttributes(['class' => 'table border-top'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setFiltersDisabled()
            ->setBulkActionsDisabled()
            ->setColumnSelectDisabled();
    }

    public function builder(): Builder
    {
        $query = PengajuanInvestasi::query()
            ->select([
                'id_pengajuan_investasi',
                'tanggal_investasi',
                'sisa_pokok',
                'sisa_bagi_hasil',
                'nomor_kontrak',
                'nama_investor',
                'jenis_investasi',
                'status'
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
            // Aggregated from another table - NO EDIT
            Column::make('Pengembalian Pokok Deposito')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center">Rp ' . number_format($data['pengembalian_pokok'], 0, ',', '.') . '</div>';
                })
                ->html(),

            // Aggregated from another table - NO EDIT
            Column::make('Pengembalian Bagi Hasil Deposito')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center">Rp ' . number_format($data['pengembalian_bagi_hasil'], 0, ',', '.') . '</div>';
                })
                ->html(),

            // EDITABLE
            Column::make('Sisa Pokok Belum Dikembalikan', 'sisa_pokok')
                ->sortable()
                ->label(function ($row) {
                    $value = 'Rp ' . number_format($row->sisa_pokok, 0, ',', '.');
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell"><strong class="text-danger">' . $value . '</strong>
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'sisa_pokok\'})"></i>
                    </div>';
                })
                ->html(),

            // EDITABLE
            Column::make('Sisa Bagi Hasil Belum Dikembalikan', 'sisa_bagi_hasil')
                ->sortable()
                ->label(function ($row) {
                    $value = 'Rp ' . number_format($row->sisa_bagi_hasil, 0, ',', '.');
                    $id = $row->id_pengajuan_investasi;
                    return '<div class="text-center editable-cell"><strong class="text-danger">' . $value . '</strong>
                        <i class="ti ti-pencil edit-icon" onclick="Livewire.dispatch(\'openEditModal\', {id: \'' . $id . '\', field: \'sisa_bagi_hasil\'})"></i>
                    </div>';
                })
                ->html(),

            // Calculated - NO EDIT
            Column::make('Total Belum Dikembalikan')
                ->label(function ($row) {
                    $data = $this->getPengembalianData($row);
                    return '<div class="text-center"><strong class="text-danger">Rp ' . number_format($data['total_belum_dikembalikan'], 0, ',', '.') . '</strong></div>';
                })
                ->html(),
        ];
    }
}
