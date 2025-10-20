<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\ConfigMatrixPinjaman;

class ConfigMatrixPinjamanTable extends DataTableComponent
{
    protected $model = ConfigMatrixPinjaman::class;
    protected $listeners = ['refreshConfigMatrixTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_matrix_pinjaman');
        $this->setSearchEnabled();
        $this->setPerPageAccepted([10, 25, 50, 100]);
        $this->setPerPage(10);
        $this->setSortingPillsEnabled();
    }

    // CRITICAL: Must explicitly select primary key for action buttons
    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return ConfigMatrixPinjaman::query()
            ->select('id_matrix_pinjaman', 'nominal', 'approve_oleh');
    }

    public function columns(): array
    {
        return [
            Column::make("No", "id_matrix_pinjaman")
                ->sortable()
                ->format(function($value, $row, Column $column) {
                    static $rowNumber = 0;
                    return ++$rowNumber;
                }),
            Column::make("Nominal", "nominal")
                ->sortable()
                ->searchable()
                ->format(function($value) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                }),
            Column::make("Approve Oleh", "approve_oleh")
                ->sortable()
                ->searchable()
                ->format(function($value) {
                    return $value ?? '-';
                }),
            Column::make("Aksi")
                ->label(function($row) {
                    return view('livewire.config-matrix-pinjaman.partials.table-actions', [
                        'id' => $row->id_matrix_pinjaman
                    ]);
                })
        ];
    }
}
