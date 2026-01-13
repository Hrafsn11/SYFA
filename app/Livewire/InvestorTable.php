<?php

namespace App\Livewire;

use App\Livewire\MasterData\DebiturDanInvestor;
use App\Models\MasterDebiturDanInvestor;
use App\Livewire\Traits\HasUniversalFormAction;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

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
            ->setTableAttributes(['class' => 'table table-hover'])
            ->setTheadAttributes(['class' => 'table-light'])
            ->setSearchFieldAttributes(['class' => 'form-control', 'placeholder' => 'Cari...'])
            ->setPerPageFieldAttributes(['class' => 'form-select'])
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(true)
            ->setBulkActionsDisabled();
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Status')
                ->options([
                    '' => 'Semua Status',
                    'active' => 'Active',
                    'non active' => 'Non Active',
                    'locked' => 'Locked',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->where('status', $value);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        return MasterDebiturDanInvestor::query()
            ->with('kol')
            ->where('flagging', 'ya')
            ->select('id_debitur', 'id_kol', 'nama', 'kode_perusahaan', 'alamat', 'email', 'no_telepon', 'status', 'deposito', 'nama_ceo', 'nama_bank', 'no_rek', 'tanda_tangan', 'flagging', 'flagging_investor');
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

            Column::make('Kode Perusahaan', 'kode_perusahaan')
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

            Column::make('Alamat', 'alamat')
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
                    } elseif ($value === 'locked') {
                        return '<div class="text-center"><span class="badge bg-danger"><i class="ti ti-lock me-1"></i>Locked</span></div>';
                    } else {
                        return '<div class="text-center"><span class="badge bg-secondary">Non Active</span></div>';
                    }
                })
                ->html(),

            Column::make('Tipe Investor', 'flagging_investor')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    if (!$value) {
                        return '<div class="text-center">-</div>';
                    }
                    
                    $types = explode(',', $value);
                    $badges = [];
                    
                    foreach ($types as $type) {
                        $type = trim($type);
                        if ($type === 'sfinance') {
                            $badges[] = '<span class="badge bg-primary">SFinance</span>';
                        } elseif ($type === 'sfinlog') {
                            $badges[] = '<span class="badge bg-info">SFinlog</span>';
                        }
                    }
                    
                    return '<div class="text-center">'.implode(' ', $badges).'</div>';
                })
                ->html(),

            Column::make('Tanda Tangan', 'tanda_tangan')
                ->sortable()
                ->format(function ($value) {
                    if ($value) {
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
                ->label(function ($row) {
                    $this->setUrlLoadData('get_data_' . $row->id_debitur, 'master-data.debitur-investor.edit', ['id' => $row->id_debitur, 'callback' => 'editData']);

                    return view('livewire.master-data-debitur-investor.partials.investor-table-actions', [
                        'id' => $row->id_debitur,
                        'status' => $row->status
                    ]);
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
