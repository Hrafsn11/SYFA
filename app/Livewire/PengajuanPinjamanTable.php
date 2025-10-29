<?php

namespace App\Livewire;

use App\Models\PengajuanPeminjaman;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PengajuanPinjamanTable extends DataTableComponent
{
    protected $model = PengajuanPeminjaman::class;

    protected $listeners = ['refreshPengajuanPinjamanTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_peminjaman')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari pengajuan pinjaman...')
            ->setSearchDebounce(500)

            // Pagination
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)

            // Default Sort
            ->setDefaultSort('id_pengajuan_peminjaman', 'desc')

            // Table Styling
            ->setTableAttributes([
                'class' => 'table table-hover',
            ])
            ->setTheadAttributes([
                'class' => 'table-light',
            ])
            ->setSearchFieldAttributes([
                'class' => 'form-control',
                'placeholder' => 'Cari pengajuan pinjaman...',
            ])
            ->setPerPageFieldAttributes([
                'class' => 'form-select',
            ])

            // Disable Bulk Actions
            ->setBulkActionsDisabled();
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return PengajuanPeminjaman::query()
            ->with(['debitur', 'instansi'])
            ->select('pengajuan_peminjaman.id_pengajuan_peminjaman', 
                    'pengajuan_peminjaman.nomor_peminjaman', 
                    'pengajuan_peminjaman.id_debitur', 
                    'pengajuan_peminjaman.jenis_pembiayaan', 
                    'pengajuan_peminjaman.sumber_pembiayaan', 
                    'pengajuan_peminjaman.id_instansi', 
                    'pengajuan_peminjaman.total_pinjaman', 
                    'pengajuan_peminjaman.total_bagi_hasil', 
                    'pengajuan_peminjaman.status', 
                    'pengajuan_peminjaman.harapan_tanggal_pencairan', 
                    'pengajuan_peminjaman.created_at',
                    'pengajuan_peminjaman.lampiran_sid');
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
                
            Column::make('Nomor Peminjaman', 'nomor_peminjaman')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center"><strong>'.($value ?: '-').'</strong></div>')
                ->html(),
                
            Column::make('Nama Perusahaan', 'debitur.nama_debitur')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-start">'.($value ?: '-').'</div>')
                ->html(),
                
            Column::make('Jenis Pembiayaan', 'jenis_pembiayaan')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    $badgeClass = match($value) {
                        'Invoice Financing' => 'bg-primary',
                        'PO Financing' => 'bg-success',
                        'Installment' => 'bg-warning',
                        'Factoring' => 'bg-info',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.($value ?: '-').'</span></div>';
                })
                ->html(),
                
            Column::make('Lampiran SID', 'lampiran_sid')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    if ($value) {
                        return '<div class="text-center"><a href="'.asset('storage/'.$value).'" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-alt"></i></a></div>';
                    }
                    return '<div class="text-center"><span class="text-muted">-</span></div>';
                })
                ->html(),
                
            Column::make('Nilai Kol', 'nilai_kol')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    $kol = $row->debitur->kol->kol ?? null;
                    $displayValue = $kol ? $kol : ($value ? $value : '-');
                    
                    if ($displayValue === '-') {
                        return '<div class="text-center"><span class="text-muted">-</span></div>';
                    }
                    
                    return '<div class="text-center"><span class="badge bg-danger">'.$displayValue.'</span></div>';
                })
                ->html(),
                
            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    $badgeClass = match($value) {
                        'submitted' => 'bg-primary',
                        'approved' => 'bg-success',
                        'rejected' => 'bg-danger',
                        'pending' => 'bg-warning text-dark',
                        'disbursed' => 'bg-info',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.ucfirst($value ?: 'Draft').'</span></div>';
                })
                ->html(),
                
            Column::make('Aksi')
                ->label(fn ($row) => view('livewire.peminjaman.partials.table-actions', ['id' => $row->id_pengajuan_peminjaman])->render())
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
