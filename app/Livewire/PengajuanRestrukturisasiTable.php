<?php

namespace App\Livewire;

use App\Models\PengajuanRestrukturisasi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\{DataTableComponent, Views\Column};

class PengajuanRestrukturisasiTable extends DataTableComponent
{
    protected $model = PengajuanRestrukturisasi::class;

    private const BADGE_COLORS_PEMBIAYAAN = [
        'Invoice Financing' => 'primary',
        'PO Financing' => 'success',
        'Installment' => 'warning',
        'Factoring' => 'info',
    ];

    private const BADGE_COLORS_STATUS = [
        'Draft' => 'info',
        'Dalam Proses' => 'secondary',
        'Perbaikan Dokumen' => 'danger',
        'Perlu Evaluasi Ulang' => 'warning',
        'Ditolak' => 'danger',
        'Selesai' => 'success',
    ];

    private const BADGE_COLORS_JENIS = [
        'penurunan' => 'success',
        'perpanjangan' => 'primary',
        'pengurangan' => 'warning',
        'lainnya' => 'secondary',
    ];

    private const EDITABLE_STATUSES = ['Draft', 'Perbaikan Dokumen'];

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_restrukturisasi')
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setSearchStatus(true)
            ->setColumnSelectStatus(true)
            ->setEmptyMessage('Tidak ada data pengajuan restrukturisasi');
    }

    public function builder(): Builder
    {
        return PengajuanRestrukturisasi::query()->select('pengajuan_restrukturisasi.*');
    }

    public function columns(): array
    {
        return [
            $this->numberColumn(),
            $this->namaDebiturColumn(),
            $this->nomorKontrakColumn(),
            $this->jenisPembiayaanColumn(),
            $this->plafonAwalColumn(),
            $this->sisaPokokColumn(),
            $this->statusColumn(),
            $this->jenisRestrukturisasiColumn(),
            $this->actionColumn(),
        ];
    }

    private function numberColumn(): Column
    {
        return Column::make('No')
            ->label(function () {
                static $rowNumber = 0;
                $number = (($this->getPage() - 1) * $this->getPerPage()) + ++$rowNumber;
                return $this->html('<div class="text-center">' . $number . '</div>');
            })
            ->html()
            ->excludeFromColumnSelect();
    }

    private function namaDebiturColumn(): Column
    {
        return Column::make('Nama Debitur', 'nama_perusahaan')
            ->sortable()
            ->searchable()
            ->format(fn($value) => $this->html('<div class="fw-semibold">' . e($value) . '</div>'));
    }

    private function nomorKontrakColumn(): Column
    {
        return Column::make('Nomor Kontrak', 'nomor_kontrak_pembiayaan')
            ->sortable()
            ->searchable()
            ->format(fn($value) => $this->badge($value, 'secondary'));
    }

    private function jenisPembiayaanColumn(): Column
    {
        return Column::make('Jenis Pembiayaan', 'jenis_pembiayaan')
            ->sortable()
            ->format(function ($value) {
                if (!$value) return '-';
                $color = self::BADGE_COLORS_PEMBIAYAAN[$value] ?? 'secondary';
                return $this->badge($value, $color);
            });
    }

    private function plafonAwalColumn(): Column
    {
        return Column::make('Plafon Awal', 'jumlah_plafon_awal')
            ->sortable()
            ->format(fn($value) => $value ? $this->formatCurrency($value) : '-');
    }

    private function sisaPokokColumn(): Column
    {
        return Column::make('Sisa Pokok', 'sisa_pokok_belum_dibayar')
            ->sortable()
            ->format(fn($value) => $value ? $this->formatCurrency($value, 'text-warning') : '-');
    }

    private function statusColumn(): Column
    {
        return Column::make('Status', 'status')
            ->sortable()
            ->format(function ($value) {
                if (!$value) return '-';
                $color = self::BADGE_COLORS_STATUS[$value] ?? 'secondary';
                return $this->badge($value, $color);
            });
    }

    private function jenisRestrukturisasiColumn(): Column
    {
        return Column::make('Jenis Restrukturisasi', 'jenis_restrukturisasi')
            ->label(fn($row) => $this->renderJenisRestrukturisasi($row->jenis_restrukturisasi))
            ->html();
    }

    private function actionColumn(): Column
    {
        return Column::make('Action')
            ->label(fn($row) => $this->renderActions($row))
            ->html()
            ->excludeFromColumnSelect();
    }

    private function renderJenisRestrukturisasi($value): HtmlString
    {
        if (!$value) {
            return $this->html('<span class="text-muted">-</span>');
        }

        $jenisArray = is_array($value) ? $value : json_decode($value, true);
        
        if (!$jenisArray || !is_array($jenisArray) || count($jenisArray) === 0) {
            return $this->html('<span class="text-muted">-</span>');
        }

        $badges = array_map(fn($jenis) => $this->badgeJenisRestrukturisasi($jenis), $jenisArray);
        
        return $this->html('<div class="d-flex flex-wrap gap-1">' . implode('', $badges) . '</div>');
    }

    private function badgeJenisRestrukturisasi(string $jenis): string
    {
        $color = 'info';
        
        foreach (self::BADGE_COLORS_JENIS as $keyword => $badgeColor) {
            if (stripos($jenis, $keyword) !== false) {
                $color = $badgeColor;
                break;
            }
        }

        return '<span class="badge bg-label-' . $color . ' text-wrap" style="max-width: 200px;">' . e($jenis) . '</span>';
    }

    private function renderActions($row): HtmlString
    {
        if (!$row->id_pengajuan_restrukturisasi) {
            return $this->html('<span class="text-muted">-</span>');
        }

        $detailButton = $this->detailButton($row->id_pengajuan_restrukturisasi);
        $editButton = $this->editButton($row->id_pengajuan_restrukturisasi, $row->status);

        return $this->html(
            '<div class="d-flex justify-content-center align-items-center gap-2">' .
            $detailButton . $editButton .
            '</div>'
        );
    }

    private function detailButton(string $id): string
    {
        $url = route('detail-restrukturisasi', ['id' => $id]);
        
        return sprintf(
            '<a href="%s" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" title="Detail">
                <i class="ti ti-file"></i>
            </a>',
            e($url)
        );
    }

    private function editButton(string $id, string $status): string
    {
        $isEditable = in_array($status, self::EDITABLE_STATUSES);

        if ($isEditable) {
            return sprintf(
                '<a href="javascript:void(0);" onclick="editPengajuan(\'%s\')" 
                   class="btn btn-sm btn-icon btn-text-secondary rounded-pill" title="Edit">
                    <i class="ti ti-edit"></i>
                </a>',
                e($id)
            );
        }

        return sprintf(
            '<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                     title="Tidak dapat diedit (Status: %s)" disabled>
                <i class="ti ti-edit-off"></i>
            </button>',
            e($status)
        );
    }

    private function html(string $content): HtmlString
    {
        return new HtmlString($content);
    }

    private function badge(string $text, string $color): HtmlString
    {
        return $this->html('<span class="badge bg-label-' . $color . '">' . e($text) . '</span>');
    }

    private function formatCurrency(float $amount, string $class = 'fw-semibold'): HtmlString
    {
        return $this->html('<span class="' . $class . '">Rp ' . number_format($amount, 0, ',', '.') . '</span>');
    }
}
