<?php

namespace App\Livewire;

use App\Models\PengembalianPinjaman;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PengembalianPeminjamanTable extends DataTableComponent
{
    protected $model = PengembalianPinjaman::class;

    protected $listeners = ['refreshPengembalianPeminjamanTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari pengembalian pinjaman...')
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPageVisibilityEnabled()
            ->setPerPage(10)
            ->setDefaultSort('id', 'desc')
            ->setTableAttributes([
                'class' => 'table table-hover',
            ])
            ->setTheadAttributes([
                'class' => 'table-light',
            ])
            ->setSearchFieldAttributes([
                'class' => 'form-control',
                'placeholder' => 'Cari pengembalian pinjaman...',
            ])
            ->setPerPageFieldAttributes([
                'class' => 'form-select',
            ])
            ->setBulkActionsDisabled();
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return PengembalianPinjaman::query()
            ->with('pengajuanPeminjaman')
            ->whereIn('id_pengajuan_peminjaman', function($query) {
                $query->select('id_pengajuan_peminjaman')
                    ->from('pengajuan_peminjaman')
                    ->where('id_debitur', Auth::id());
            })
            ->select('pengembalian_pinjaman.*');
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

            Column::make('Nama perusahaan', 'nama_perusahaan')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center">'.($value ?: '-').'</div>')
                ->html(),

            Column::make('Tanggal Pencairan', 'tanggal_pencairan')
                ->sortable()
                ->format(function ($value) {
                    if (!$value) return '<div class="text-center">-</div>';
                    return '<div class="text-center">'.date('d-m-Y', strtotime($value)).'</div>';
                })
                ->html(),

            Column::make('Nomor peminjaman', 'nomor_peminjaman')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<div class="text-center"><strong>'.($value ?: '-').'</strong></div>')
                ->html(),

            Column::make('Total pinjaman', 'total_pinjaman')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-end">Rp '.number_format($value, 0, ',', '.').'</div>';
                })
                ->html(),

            Column::make('Sisa bayar pokok', 'sisa_bayar_pokok')
                ->sortable()
                ->format(function ($value) {
                    $badgeClass = $value == 0 ? 'bg-success' : 'bg-warning';
                    $formatted = 'Rp '.number_format($value, 0, ',', '.');
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.$formatted.'</span></div>';
                })
                ->html(),

            Column::make('Sisa bagi hasil', 'sisa_bagi_hasil')
                ->sortable()
                ->format(function ($value) {
                    $badgeClass = $value == 0 ? 'bg-success' : 'bg-warning';
                    $formatted = 'Rp '.number_format($value, 0, ',', '.');
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.$formatted.'</span></div>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->sortable()
                ->format(function ($value) {
                    $badgeClass = match($value) {
                        'Lunas' => 'bg-success',
                        'Belum Lunas' => 'bg-warning',
                        'Menunggak' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<div class="text-center"><span class="badge '.$badgeClass.'">'.($value ?: 'Belum Lunas').'</span></div>';
                })
                ->html(),

            // Column::make('Aksi')
            //     ->label(fn ($row) => view('livewire.pengembalian-pinjaman.partials.table-actions', [
            //         'id' => $row->id
            //     ])->render())
            //     ->html()
            //     ->excludeFromColumnSelect(),
        ];
    }
}
