<?php

namespace App\Livewire;

use App\Livewire\MasterData\DebiturDanInvestor;
use App\Models\MasterDebiturDanInvestor;
use App\Livewire\Traits\HasUniversalFormAction;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class InvestorTable extends DataTableComponent
{
    use HasUniversalFormAction;

    protected $model = MasterDebiturDanInvestor::class;

    protected $listeners = ['refreshInvestorTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_debitur')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari Investor...')
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
            ->where('flagging', 'ya')
            ->select('id_debitur', 'id_kol', 'nama', 'alamat', 'email', 'no_telepon', 'status', 'deposito', 'nama_ceo', 'nama_bank', 'no_rek', 'flagging');
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

            Column::make('Deposito', 'deposito')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    if ($value === 'khusus') {
                        return '<div class="text-center"><span class="badge bg-primary">Khusus</span></div>';
                    } elseif ($value === 'reguler') {
                        return '<div class="text-center"><span class="badge bg-info">Reguler</span></div>';
                    } else {
                        return '<div class="text-center">-</div>';
                    }
                })
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

            Column::make('Aksi')
                ->label(function ($row) {
                    $this->setUrlLoadData('get_data_' . $row->id_debitur, 'master-data.debitur-investor.edit', ['id' => $row->id_debitur, 'callback' => 'editData']);

                    return view('livewire.master-data-debitur-investor.partials.investor-table-actions', [
                        'id' => $row->id_debitur,
                        'status' => $row->status
                    ])->render();
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }

    public function beforeLoadData($payload)
    {
        $this->dispatch('before-load-data', $payload)->to(DebiturDanInvestor::class);
    }
}
