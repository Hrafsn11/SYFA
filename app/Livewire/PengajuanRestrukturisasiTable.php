<?php

namespace App\Livewire;

use App\Models\PengajuanRestrukturisasi;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class PengajuanRestrukturisasiTable extends DataTableComponent
{
    protected $model = PengajuanRestrukturisasi::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_restrukturisasi');
        $this->setPerPageAccepted([10, 25, 50, 100]);
        $this->setPerPage(10);
        $this->setSearchStatus(true);
        $this->setColumnSelectStatus(true);
        $this->setEmptyMessage('Tidak ada data pengajuan restrukturisasi');
    }

    public function builder(): Builder
    {
        return PengajuanRestrukturisasi::query()
            ->select('pengajuan_restrukturisasi.*');
    }

    public function columns(): array
    {
        return [
            Column::make('No')
                ->label(function ($row, Column $column) {
                    static $rowNumber = 0;
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;
                    return new HtmlString('<div class="text-center">' . $number . '</div>');
                })
                ->html()
                ->excludeFromColumnSelect(),
                
            Column::make('Nama Debitur', 'nama_perusahaan')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return new HtmlString('<div class="fw-semibold">' . e($value) . '</div>');
                }),
                
            Column::make('Nomor Kontrak', 'nomor_kontrak_pembiayaan')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return new HtmlString('<span class="badge bg-label-secondary">' . e($value) . '</span>');
                }),
                
            Column::make('Jenis Pembiayaan', 'jenis_pembiayaan')
                ->sortable()
                ->format(function ($value) {
                    if (!$value) return '-';
                    
                    $badgeColors = [
                        'Invoice Financing' => 'primary',
                        'PO Financing' => 'success',
                        'Installment' => 'info',
                        'Factoring' => 'warning'
                    ];
                    
                    $color = $badgeColors[$value] ?? 'secondary';
                    return new HtmlString('<span class="badge bg-label-' . $color . '">' . e($value) . '</span>');
                }),
                
            Column::make('Plafon Awal', 'jumlah_plafon_awal')
                ->sortable()
                ->format(function ($value) {
                    if (!$value) return '-';
                    return new HtmlString('<span class="fw-semibold">Rp ' . number_format($value, 0, ',', '.') . '</span>');
                }),
                
            Column::make('Sisa Pokok', 'sisa_pokok_belum_dibayar')
                ->sortable()
                ->format(function ($value) {
                    if (!$value) return '-';
                    return new HtmlString('<span class="text-warning fw-semibold">Rp ' . number_format($value, 0, ',', '.') . '</span>');
                }),
                
            Column::make('Jenis Restrukturisasi', 'jenis_restrukturisasi')
                ->format(function ($value) {
                    if (!$value) return '-';
                    
                    $jenisArray = is_array($value) ? $value : json_decode($value, true);
                    if (!$jenisArray || !is_array($jenisArray)) return '-';
                    
                    $badges = array_map(function ($jenis) {
                        return '<span class="badge bg-label-info me-1">' . e($jenis) . '</span>';
                    }, $jenisArray);
                    
                    return new HtmlString(implode('', $badges));
                }),
                
            Column::make('Action')
                ->label(function ($row) {
                    $id = $row->id_pengajuan_restrukturisasi;
                    
                    if (!$id) {
                        return new HtmlString('<span class="text-muted">-</span>');
                    }
                    
                    $detailUrl = route('detail-restrukturisasi', ['id' => $id]);
                    
                    return new HtmlString('
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <a href="' . e($detailUrl) . '" 
                               class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                               title="Detail">
                                <i class="ti ti-file"></i>
                            </a>
                            <a href="javascript:void(0);" 
                               onclick="editPengajuan(\'' . e($id) . '\')"
                               class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                               title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                        </div>
                    ');
                })
                ->html()
                ->excludeFromColumnSelect(),
        ];
    }
}
