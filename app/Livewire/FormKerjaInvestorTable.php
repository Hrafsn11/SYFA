<?php

namespace App\Livewire;

use App\Models\FormKerjaInvestor;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class FormKerjaInvestorTable extends DataTableComponent
{
    protected $model = FormKerjaInvestor::class;
    protected $listeners = ['refreshFormKerjaInvestorTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_form_kerja_investor')
            // Search
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari data investasi...')
            ->setSearchDebounce(500)
            
            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            
            // Default Sort
            ->setDefaultSort('created_at', 'desc')
            
            // Table Styling
            ->setTableAttributes([
                'class' => 'table table-hover table-bordered',
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
        // Filter by current user's investor data
        $query = FormKerjaInvestor::query();
        
        if (Auth::check()) {
            $query->whereHas('investor', function($q) {
                $q->where('user_id', Auth::id())
                  ->where('flagging', 'ya');
            });
        }
        
        return $query->with('investor')
            ->select(
                'id_form_kerja_investor',
                'id_debitur', 
                'nama_investor',
                'deposito',
                'tanggal_pembayaran',
                'lama_investasi',
                'jumlah_investasi',
                'bagi_hasil',
                'bagi_hasil_keseluruhan',
                'created_at',
                'updated_at'
            );
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
            
            Column::make("Nama Investor", "nama_investor")
                ->sortable()
                ->searchable(),
            
            Column::make("Jenis Deposito", "deposito")
                ->sortable()
                ->searchable()
                ->format(function($value) {
                    $badgeClass = $value === 'reguler' ? 'bg-label-primary' : 'bg-label-info';
                    return '<div class="text-center"><span class="badge ' . $badgeClass . '">' . ucfirst($value) . '</span></div>';
                })
                ->html(),
            
            Column::make("Tanggal Investasi", "tanggal_pembayaran")
                ->sortable()
                ->searchable()
                ->format(function($value) {
                    if (!$value) return '<div class="text-center">-</div>';
                    return '<div class="text-center">' . \Carbon\Carbon::parse($value)->format('d F Y') . '</div>';
                })
                ->html(),
            
            Column::make("Lama Investasi", "lama_investasi")
                ->sortable()
                ->searchable()
                ->format(function($value) {
                    return '<div class="text-center">' . ($value ?? '-') . ' Bulan</div>';
                })
                ->html(),
            
            Column::make("Jumlah Investasi", "jumlah_investasi")
                ->sortable()
                ->searchable()
                ->format(function($value) {
                    return '<div class="text-center">Rp ' . number_format($value, 0, ',', '.') . '</div>';
                })
                ->html(),
            
            Column::make("Bagi Hasil", "bagi_hasil")
                ->sortable()
                ->searchable()
                ->format(function($value) {
                    return '<div class="text-center">' . $value . '%</div>';
                })
                ->html(),
            
            Column::make("Nominal Bagi Hasil Keseluruhan", "bagi_hasil_keseluruhan")
                ->sortable()
                ->searchable()
                ->format(function($value) {
                    return '<div class="text-center">Rp ' . number_format($value, 0, ',', '.') . '</div>';
                })
                ->html(),
            
            Column::make('Aksi')
                ->label(fn ($row) => view('livewire.form-kerja-investor.partials.table-actions', [
                    'id' => $row->id_form_kerja_investor
                ]))
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
