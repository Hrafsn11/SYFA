<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\MasterDebiturDanInvestor;

class DebiturinvestorTable extends DataTableComponent
{
    protected $model = MasterDebiturDanInvestor::class;

    // Event listener untuk refresh dari luar
    protected $listeners = ['refreshDebiturTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_debitur')
            // Search
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Debitur/Investor...')
            ->setSearchDebounce(500)
            
            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            
            // Default Sort
            ->setDefaultSort('id_debitur', 'asc')
            
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
        // PENTING: Pastikan primary key (id_debitur) selalu di-select
        return MasterDebiturDanInvestor::query()
            ->with('kol')
            ->select('id_debitur', 'id_kol', 'nama_debitur', 'alamat', 'email', 'nama_ceo', 'nama_bank', 'no_rek', 'flagging');
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
            
            Column::make("Nama Debitur/Investor", "nama_debitur")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?? '-') . '</div>')
                ->html(),
            
            Column::make("Flagging", "flagging")
                ->sortable()
                ->searchable()
                ->format(function($value) {
                    $badge = ($value === 'ya') 
                        ? '<span class="badge bg-label-success">Investor</span>' 
                        : '<span class="badge bg-label-secondary">-</span>';
                    return '<div class="text-center">' . $badge . '</div>';
                })
                ->html(),
            
            Column::make("Nama CEO", "nama_ceo")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?? '-') . '</div>')
                ->html(),
            
            Column::make("Alamat", "alamat")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?? '-') . '</div>')
                ->html(),
            
            Column::make("Email", "email")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?? '-') . '</div>')
                ->html(),
            
            Column::make("KOL Perusahaan", "id_kol")
                ->sortable()
                ->searchable()
                ->format(function($value, $row) {
                    $kolValue = optional($row->kol)->kol ?? '-';
                    return '<div class="text-center">' . $kolValue . '</div>';
                })
                ->html(),
            
            Column::make("Nama Bank", "nama_bank")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?? '-') . '</div>')
                ->html(),
            
            Column::make("No Rekening", "no_rek")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="text-center">' . ($value ?? '-') . '</div>')
                ->html(),
            
            Column::make("Aksi")
                ->label(fn($row) => view('livewire.master-data-debitur-investor.partials.table-actions', ['id' => $row->id_debitur]))
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
