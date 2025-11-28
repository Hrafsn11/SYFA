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
                ->label(function ($row) {
                    $value = $row->jenis_restrukturisasi;
                    
                    if (!$value) {
                        return new HtmlString('<span class="text-muted">-</span>');
                    }
                    
                    // Parse JSON to array
                    $jenisArray = is_array($value) ? $value : json_decode($value, true);
                    if (!$jenisArray || !is_array($jenisArray) || count($jenisArray) === 0) {
                        return new HtmlString('<span class="text-muted">-</span>');
                    }
                    
                    $html = '<div class="d-flex flex-wrap gap-1">';
                    
                    foreach ($jenisArray as $jenis) {
                        // Badge colors based on type
                        $badgeColor = 'info';
                        if (stripos($jenis, 'penurunan') !== false) {
                            $badgeColor = 'success';
                        } elseif (stripos($jenis, 'perpanjangan') !== false) {
                            $badgeColor = 'primary';
                        } elseif (stripos($jenis, 'pengurangan') !== false) {
                            $badgeColor = 'warning';
                        } elseif (stripos($jenis, 'lainnya') !== false) {
                            $badgeColor = 'secondary';
                        }
                        
                        $html .= '<span class="badge bg-label-' . $badgeColor . ' text-wrap" style="max-width: 200px;">' . e($jenis) . '</span>';
                    }
                    
                    // display keterangan lainnya k=kalu ada
                    // if (!empty($row->jenis_restrukturisasi_lainnya)) {
                    //     $lainnyaText = $row->jenis_restrukturisasi_lainnya;
                    //     $truncated = mb_strlen($lainnyaText) > 30 ? mb_substr($lainnyaText, 0, 30) . '...' : $lainnyaText;
                        
                    //     $html .= '<span class="badge bg-label-dark text-wrap" style="max-width: 200px;" title="' . e($lainnyaText) . '">
                    //         <i class="ti ti-info-circle me-1"></i>' . e($truncated) . '</span>';
                    // }
                    
                    $html .= '</div>';
                    
                    return new HtmlString($html);
                })
                ->html(),
                
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
