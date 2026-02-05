<?php

namespace App\Livewire;

use App\Livewire\Traits\HasUniversalFormAction;
use App\Models\CellsProject;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CellsProjectTable extends DataTableComponent
{
    use HasUniversalFormAction;

    protected $model = CellsProject::class;

    protected $listeners = [
        'refreshCellsProjectTable' => '$refresh',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id_cells_project')
            // Search
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Project...')
            ->setSearchDebounce(500)

            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)

            // Default Sort
            ->setDefaultSort('created_at', 'desc')

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
        return CellsProject::query()
            ->with('projects')
            ->select('id_cells_project', 'nama_cells_bisnis', 'nama_pic', 'alamat', 'deskripsi_bidang', 'tanda_tangan_pic', 'created_at')
            ->orderBy('created_at', 'desc');
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

            Column::make('Nama Cells Bisnis', 'nama_cells_bisnis')
                ->sortable()
                ->searchable(),

            Column::make('Projects', 'id_cells_project')
                ->label(function ($row) {
                    if ($row->projects->isEmpty()) {
                        return '<span class="badge bg-label-secondary">Belum ada project</span>';
                    }

                    $badges = $row->projects->map(function ($project) {
                        return '<span class="badge bg-label-primary me-1 mb-1">' . e($project->nama_project) . '</span>';
                    })->implode('');

                    return '<div class="d-flex flex-wrap">' . $badges . '</div>';
                })
                ->html(),

            Column::make('Nama PIC', 'nama_pic')
                ->sortable()
                ->searchable(),

            Column::make('Tanda Tangan', 'tanda_tangan_pic')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    if ($value) {
                        return '<div class="text-center"><a href="' . asset('storage/' . $value) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-file-text"></i></a></div>';
                    }
                    return '<div class="text-center"><span class="text-muted">-</span></div>';
                })
                ->html(),

            Column::make('Alamat', 'alamat')
                ->sortable()
                ->searchable(),

            Column::make('Deskripsi Bidang', 'deskripsi_bidang')
                ->sortable()
                ->searchable(),

            Column::make('Aksi')
                ->label(function ($row) {
                    $this->setUrlLoadData('get_data_' . $row->id_cells_project, 'master-data.cells-project.edit', ['id' => $row->id_cells_project, 'callback' => 'editData']);

                    $editBtn = '<button class="btn btn-sm btn-icon btn-text-primary rounded-pill" wire:click=\'' . $this->urlAction['get_data_' . $row->id_cells_project] . '\' type="button" title="Edit"><i class="ti ti-edit"></i></button>';
                    $deleteBtn = '<button class="btn btn-sm btn-icon btn-text-danger rounded-pill cells-project-delete-btn" type="button" data-id="' . $row->id_cells_project . '" title="Hapus"><i class="ti ti-trash"></i></button>';

                    return '<div class="d-flex justify-content-center align-items-center gap-2">' . $editBtn . $deleteBtn . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
