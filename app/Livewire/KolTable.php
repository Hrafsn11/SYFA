<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\MasterKol;

class KolTable extends DataTableComponent
{
    protected $model = MasterKol::class;
    protected $listeners = ['refreshKolTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_kol')
            // Search
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari KOL...')
            ->setSearchDebounce(500)
            
            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            
            // Default Sort
            ->setDefaultSort('id_kol', 'asc')
            
            // Table Styling
            ->setTableAttributes([
                'class' => 'table table-hover',
            ])
            ->setTheadAttributes([
                'class' => 'table-light',
            ])
            ->setSearchFieldAttributes([
                'class' => 'form-control',
                'placeholder' => 'Cari...',
            ])
            ->setPerPageFieldAttributes([
                'class' => 'form-select',
            ])
            
            // Disable Bulk Actions
            ->setBulkActionsDisabled();
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return MasterKol::query()->select('id_kol', 'kol', 'persentase_pencairan', 'jmlh_hari_keterlambatan');
    }

    public function columns(): array
    {
        $rowNumber = 0;
        return [
            Column::make("No")
                ->label(function($row) use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;
                    return '<div class="text-center">' . $number . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
            
            Column::make("KOL", "kol")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . $value . '</div>')
                ->html(),
            
            Column::make("Persentase Pencairan", "persentase_pencairan")
                ->sortable()
                ->format(function($value, $row) {
                    $percentage = $row->persentase_label ?? ($value ? $value . '%' : '-');
                    return '<div class="text-center">' . $percentage . '</div>';
                })
                ->html(),
            
            Column::make("Jumlah Hari Keterlambatan", "jmlh_hari_keterlambatan")
                ->sortable()
                ->format(function($value, $row) {
                    $days = $row->tanggal_tenggat_label ?? ($value ? $value . ' Hari' : '-');
                    return '<div class="text-center">' . $days . '</div>';
                })
                ->html(),
            
            Column::make("Aksi")
                ->label(fn($row) => view('livewire.master-data-kol.table-actions', ['id' => $row->id_kol]))
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
