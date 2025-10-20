<?php

namespace App\Livewire;

use App\Models\MasterSumberPendanaanEksternal;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SumberPendanaanEksternalTable extends DataTableComponent
{
    protected $model = MasterSumberPendanaanEksternal::class;

    protected $listeners = ['refreshSumberPendanaanEksternalTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_instansi')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari...')
            ->setSearchDebounce(500)

        // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)

            // Default Sort
            ->setDefaultSort('id_instansi', 'asc')

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
        return MasterSumberPendanaanEksternal::query()
            ->select('id_instansi', 'nama_instansi', 'persentase_bagi_hasil');
    }

    public function columns(): array
    {
        $rowNumber = 0;
        return [
            Column::make('No')
                ->label(function ($row) use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;

                    return '<div class="text-center">'.$number.'</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
            Column::make('Nama instansi', 'nama_instansi')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.$value.'</div>')
                ->html(), 
            Column::make('Persentase bagi hasil', 'persentase_bagi_hasil')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ? $value.'%' : '-').'</div>')
                ->html(),
            Column::make('Aksi')
                ->label(fn ($row) => view('livewire.master-sumber-pendanaan-eksternal.partials.table-actions', ['id' => $row->id_instansi]))
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
