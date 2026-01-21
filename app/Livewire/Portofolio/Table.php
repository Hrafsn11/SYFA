<?php

namespace App\Livewire\Portofolio;

use Livewire\Component;
use App\Models\LaporanInvestasi;
use App\Livewire\Traits\HasUniversalFormAction;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class Table extends DataTableComponent
{   
    use HasUniversalFormAction;

    protected $model = LaporanInvestasi::class;
    protected $listeners = [
        'refreshKolTable' => '$refresh'
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id_laporan_investasi')
            // Search
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari SBU...')
            ->setSearchDebounce(500)
            
            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            
            // Default Sort
            ->setDefaultSort('id_laporan_investasi', 'asc')
            
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
        return LaporanInvestasi::query()->select('id_laporan_investasi', 'nama_sbu', 'tahun');
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
            
            Column::make("Nama SBU", "nama_sbu")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div>' . $value . '</div>')
                ->html(),
            
            Column::make("Tahun", "tahun")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . $value . '</div>')
                ->html(),
            
            Column::make("action")
                ->label(function ($row) {
                    $this->setUrlLoadData('get_data_' . $row->id_laporan_investasi, 'portofolio.get-data', ['id' => $row->id_laporan_investasi, 'callback' => 'editData']);
                    return view('livewire.portofolio.table', ['id' => $row->id_laporan_investasi])->render();
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
