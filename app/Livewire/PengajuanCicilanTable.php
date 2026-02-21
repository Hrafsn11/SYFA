<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengajuanCicilan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\{DataTableComponent, Views\Column};
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PengajuanCicilanTable extends DataTableComponent
{
    use HasDebiturAuthorization;

    protected $model = PengajuanCicilan::class;

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

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_cicilan')
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setDefaultSort('created_at', 'desc')
            ->setSearchStatus(true)
            ->setColumnSelectStatus(true)
            ->setFiltersEnabled()
            ->setFiltersVisibilityStatus(true)
            ->setEmptyMessage('Tidak ada data pengajuan restrukturisasi');
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Bulan')
                ->options([
                    '' => 'Semua Bulan',
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->whereRaw("MONTH(pengajuan_cicilan.created_at) = ?", [$value]);
                    }
                }),

            SelectFilter::make('Tahun')
                ->options([
                    '' => 'Semua Tahun',
                    '2023' => '2023',
                    '2024' => '2024',
                    '2025' => '2025',
                    '2026' => '2026',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (!empty($value)) {
                        $builder->whereRaw("YEAR(pengajuan_cicilan.created_at) = ?", [$value]);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        $query = PengajuanCicilan::query()
            ->select([
                'pengajuan_cicilan.*',
                'master_debitur_dan_investor.user_id',
            ])
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_cicilan.id_debitur', '=', 'master_debitur_dan_investor.id_debitur')
            ->orderBy('pengajuan_cicilan.created_at', 'desc');

        return $this->applyDebiturAuthorization($query);
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
        if (!$row->id_pengajuan_cicilan) {
            return $this->html('<span class="text-muted">-</span>');
        }

        $detailButton = $this->detailButton($row->id_pengajuan_cicilan);

        return $this->html(
            '<div class="d-flex justify-content-center align-items-center gap-2">' .
                $detailButton .
                '</div>'
        );
    }

    private function detailButton(string $id): string
    {
        $url = route('detail-restrukturisasi', ['id' => $id]);

        return sprintf(
            '<a href="%s" class="btn btn-sm btn-outline-primary" title="Detail">
                <i class="ti ti-file"></i>
            </a>',
            e($url)
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
