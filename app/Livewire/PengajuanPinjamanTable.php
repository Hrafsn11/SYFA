<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengajuanPeminjaman;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Support\Facades\DB;

class PengajuanPinjamanTable extends DataTableComponent
{
    use HasDebiturAuthorization;

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
        $query = PengajuanPeminjaman::query()
            ->with(['debitur', 'instansi'])
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_peminjaman.id_debitur', '=', 'master_debitur_dan_investor.id_debitur')
            ->select('pengajuan_peminjaman.*', 
                    DB::raw('master_debitur_dan_investor.nama as nama_perusahaan'));

        return $this->applyDebiturAuthorization($query);
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
                
            Column::make('Nama Perusahaan')
                ->label(function ($row) {
                    $namaDebitur = $row->nama_perusahaan ?: ($row->debitur->nama ?? '-');
                    return '<div class="text-start">'.$namaDebitur.'</div>';
                })
                ->sortable(function ($builder, $direction) {
                    return $builder->orderBy('master_debitur_dan_investor.nama', $direction);
                })
                ->searchable(function ($builder, $term) {
                    return $builder->orWhere('master_debitur_dan_investor.nama', 'like', '%'.$term.'%');
                })
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
                    $displayValue = isset($kol) ? $kol : '-';
                    
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
                        'Draft' => 'bg-warning text-dark',
                        'Submitted' => 'bg-success',
                        'rejected' => 'bg-danger',
                        'pending' => 'bg-warning text-dark',
                        'disbursed' => 'bg-info',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.ucfirst($value ?: 'Draft').'</span></div>';
                })
                ->html(),
                
            Column::make('Aksi')
                ->label(fn ($row) => view('livewire.peminjaman.partials.table-actions', [
                    'id' => $row->id_pengajuan_peminjaman,
                    'status' => $row->status,
                    'is_active' => $row->is_active
                ])->render())
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
