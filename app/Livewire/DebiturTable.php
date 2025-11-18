<?php

namespace App\Livewire;

use App\Models\MasterDebiturDanInvestor;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DebiturTable extends DataTableComponent
{
    protected $model = MasterDebiturDanInvestor::class;

    protected $listeners = ['refreshDebiturTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_debitur')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Debitur...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('id_debitur', 'asc')
            ->setTableAttributes(['class' => 'table table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setBulkActionsDisabled();
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return MasterDebiturDanInvestor::query()
            ->with('kol')
            ->where('flagging', 'tidak')
            ->select('id_debitur', 'id_kol', 'nama', 'alamat', 'email', 'no_telepon', 'status', 'nama_ceo', 'nama_bank', 'no_rek', 'npwp', 'flagging', 'tanda_tangan');
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

            Column::make('Nama Perusahaan', 'nama')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?? '-').'</div>')
                ->html(),

            Column::make('Nama CEO', 'nama_ceo')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?? '-').'</div>')
                ->html(),

            Column::make('Alamat', 'alamat')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?? '-').'</div>')
                ->html(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?? '-').'</div>')
                ->html(),

            Column::make('No. Telepon', 'no_telepon')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?? '-').'</div>')
                ->html(),

            Column::make('KOL Perusahaan', 'id_kol')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    $kolValue = optional($row->kol)->kol ?? '-';

                    return '<div class="text-center">'.$kolValue.'</div>';
                })
                ->html(),

            Column::make('Nama Bank', 'nama_bank')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?? '-').'</div>')
                ->html(),

            Column::make('No Rekening', 'no_rek')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?? '-').'</div>')
                ->html(),

            Column::make('NPWP', 'npwp')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?? '-').'</div>')
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    if ($value === 'active') {
                        return '<div class="text-center"><span class="badge bg-success">Active</span></div>';
                    } else {
                        return '<div class="text-center"><span class="badge bg-secondary">Non Active</span></div>';
                    }
                })
                ->html(),

            Column::make('Tanda Tangan', 'tanda_tangan')
                ->sortable()
                ->format(function ($value) {
                    if ($value) {
                        $filename = basename($value);
                        return '<div class="text-center">
                            <a href="/storage/' . $value . '" target="_blank" class="text-primary text-decoration-none">
                                <i class="ti ti-file-text me-1"></i>
                            </a>
                        </div>';
                    } else {
                        return '<div class="text-center"><span class="text-muted">-</span></div>';
                    }
                })
                ->html(),

            Column::make('Aksi')
                ->label(fn ($row) => view('livewire.master-data-debitur-investor.partials.debitur-table-actions', [
                    'id' => $row->id_debitur,
                    'status' => $row->status
                ]))
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
