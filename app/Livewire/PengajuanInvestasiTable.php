<?php

namespace App\Livewire;

use App\Models\PengajuanInvestasi;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PengajuanInvestasiTable extends DataTableComponent
{
    protected $model = PengajuanInvestasi::class;

    protected $listeners = ['refreshPengajuanInvestasiTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_investasi')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari pengajuan investasi...')
            ->setSearchDebounce(500)

            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)

            // Default Sort
            ->setDefaultSort('id_pengajuan_investasi', 'desc')

            // Table Styling
            ->setTableAttributes([
                'class' => 'table table-hover',
            ])
            ->setTheadAttributes([
                'class' => 'table-light',
            ])
            ->setSearchFieldAttributes([
                'class' => 'form-control',
                'placeholder' => 'Cari pengajuan investasi...',
            ])
            ->setPerPageFieldAttributes([
                'class' => 'form-select',
            ])

            // Disable Bulk Actions
            ->setBulkActionsDisabled();
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return PengajuanInvestasi::query()
            ->with(['investor'])
            ->select('pengajuan_investasi.*');
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
                
            Column::make('Nama Investor', 'nama_investor')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-start"><strong>'.($value ?: '-').'</strong></div>')
                ->html(),
                
            Column::make('Nama Perusahaan')
                ->label(function ($row) {
                    $namaInvestor = $row->investor->nama ?? '-';
                    return '<div class="text-start">'.$namaInvestor.'</div>';
                })
                ->html()
                ->searchable(),
                
            Column::make('Deposito', 'deposito')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    $badgeClass = match($value) {
                        'Reguler' => 'bg-primary',
                        'Khusus' => 'bg-warning',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.($value ?: '-').'</span></div>';
                })
                ->html(),
                
            Column::make('Jumlah Investasi', 'jumlah_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-end"><strong>Rp. '.number_format($value ?? 0, 0, ',', '.').'</strong></div>';
                })
                ->html(),
                
            Column::make('Lama Investasi', 'lama_investasi')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">'.($value ? $value . ' Bulan' : '-').'</div>';
                })
                ->html(),
                
            Column::make('Bagi Hasil/Tahun', 'bagi_hasil_pertahun')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-center">'.($value ? $value . '%' : '-').'</div>';
                })
                ->html(),
                
            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    $badgeClass = match($value) {
                        'Draft' => 'bg-warning text-dark',
                        'Submitted' => 'bg-info',
                        'Submit Dokumen' => 'bg-info',
                        'Dokumen Tervalidasi' => 'bg-success',
                        'Investor Setuju' => 'bg-success',
                        'Disetujui oleh CEO SKI' => 'bg-success',
                        'Disetujui oleh Direktur SKI' => 'bg-success',
                        'Dana Sudah Dicairkan' => 'bg-primary',
                        'Ditolak' => 'bg-danger',
                        'Rejected' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.ucfirst($value ?: 'Draft').'</span></div>';
                })
                ->html(),
                
            Column::make('Aksi')
                ->label(fn ($row) => view('livewire.pengajuan-investasi.partials.table-actions', [
                    'id' => $row->id_pengajuan_investasi,
                    'status' => $row->status,
                    'current_step' => $row->current_step
                ])->render())
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
