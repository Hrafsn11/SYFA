<?php

namespace App\Livewire\SFinlog;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PeminjamanFinlog;
use Illuminate\Database\Eloquent\Builder;
use App\Services\DebiturPiutangFinlogService;

class DebiturPiutangFinlogTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id_peminjaman_finlog');
        $this->setAdditionalSelects(['peminjaman_finlog.id_peminjaman_finlog']);
        $this->setTableAttributes([
            'class' => 'table-responsive text-nowrap',
        ]);
    }

    public function builder(): Builder
    {
        return app(DebiturPiutangFinlogService::class)->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('Cells Bisnis', 'cellsProject.nama_cells_bisnis')
                ->searchable()
                ->sortable(),

            Column::make('Nama Client', 'debitur.nama')
                ->searchable()
                ->sortable(),

            Column::make('Status', 'status')
                ->sortable()
                ->format(function ($value) {
                    $color = match ($value) {
                        'Lunas' => 'success',
                        'Terlambat' => 'danger',
                        default => 'primary',
                    };
                    return '<span class="badge bg-label-' . $color . '">' . $value . '</span>';
                })->html(),

            Column::make('Pencairan (Pokok)', 'nilai_pinjaman')
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),

            Column::make('Tgl Pencairan', 'harapan_tanggal_pencairan')
                ->format(fn($val) => $val ? $val->format('d/m/Y') : '-'),

            Column::make('Bagi Hasil (%)')
                ->label(fn() => '2.00%'),

            Column::make('Bagi Hasil (TOP)', 'nilai_bagi_hasil')
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),

            Column::make('Bagi Hasil / Minggu', 'nilai_bagi_hasil')
                ->format(fn($val) => 'Rp ' . number_format($val / 4, 0, ',', '.')),

            Column::make('Bagi Hasil per Minggu (Keterlambatan)')
                ->label(function ($row) {
                    // Tidak dihitung jika belum jatuh tempo atau sudah lunas
                    if (
                        !$row->rencana_tgl_pengembalian ||
                        $row->status === 'Lunas' ||
                        now()->lte($row->rencana_tgl_pengembalian)
                    ) {
                        return '-';
                    }

                    // Jumlah minggu keterlambatan
                    $mingguTerlambat = abs(
                        now()->diffInWeeks($row->rencana_tgl_pengembalian)
                    );

                    // Bagi hasil per minggu
                    $bagiHasilPerMinggu = $row->nilai_bagi_hasil / 4;

                    // Bagi hasil TOP (awal)
                    $bagiHasilTOP = $row->nilai_bagi_hasil;

                    // Total yang harus dibayar
                    $total = ($bagiHasilPerMinggu * $mingguTerlambat) + $bagiHasilTOP;

                    return 'Rp ' . number_format($total, 0, ',', '.');
                }),


            Column::make('Jumlah Minggu Keterlambatan')
                ->label(function ($row) {
                    if (!$row->rencana_tgl_pengembalian || $row->status === 'Lunas') {
                        return 0;
                    }

                    if (now()->gt($row->rencana_tgl_pengembalian)) {
                        return abs(now()->diffInWeeks($row->rencana_tgl_pengembalian));
                    }

                    return 0;
                }),

            Column::make('Total Bagi Hasil', 'nilai_bagi_hasil')
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),

            Column::make('Total Tagihan', 'total_pinjaman')
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),

            Column::make('TOP (Hari)', 'top'),

            Column::make('Jatuh Tempo', 'rencana_tgl_pengembalian')
                ->format(fn($val) => $val ? $val->format('d/m/Y') : '-'),

            // Calculated Columns
            Column::make('Bayar Pokok')
                ->label(fn($row) => 'Rp ' . number_format($this->getBayarPokok($row), 0, ',', '.')),

            Column::make('Bayar Bagi Hasil')
                ->label(fn($row) => 'Rp ' . number_format($this->getBayarBagiHasil($row), 0, ',', '.')),

            Column::make('Total Bayar')
                ->label(fn($row) => 'Rp ' . number_format($this->getBayarPokok($row) + $this->getBayarBagiHasil($row), 0, ',', '.')),

            Column::make('Sisa Pokok')
                ->label(fn($row) => 'Rp ' . number_format($this->getSisaPokok($row), 0, ',', '.')),

            Column::make('Sisa Bagi Hasil')
                ->label(fn($row) => 'Rp ' . number_format($this->getSisaBagiHasil($row), 0, ',', '.')),

            Column::make('Total Sisa')
                ->label(fn($row) => 'Rp ' . number_format($this->getSisaPokok($row) + $this->getSisaBagiHasil($row), 0, ',', '.')),
        ];
    }

    public function filters(): array
    {
        return [
            \Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter::make('Bulan (Pencairan)')
                ->options([
                    '' => 'Semua',
                    '1' => 'Januari',
                    '2' => 'Februari',
                    '3' => 'Maret',
                    '4' => 'April',
                    '5' => 'Mei',
                    '6' => 'Juni',
                    '7' => 'Juli',
                    '8' => 'Agustus',
                    '9' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value) $builder->whereMonth('peminjaman_finlog.harapan_tanggal_pencairan', $value);
                }),

            \Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter::make('Tahun (Pencairan)')
                ->options(
                    ['' => 'Semua'] + PeminjamanFinlog::selectRaw('YEAR(harapan_tanggal_pencairan) as year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year', 'year')
                        ->toArray()
                )
                ->filter(function (Builder $builder, string $value) {
                    if ($value) $builder->whereYear('peminjaman_finlog.harapan_tanggal_pencairan', $value);
                }),
        ];
    }

    private function getSisaPokok($row)
    {
        return $row->latestPengembalian ? $row->latestPengembalian->sisa_pinjaman : $row->nilai_pinjaman;
    }

    private function getSisaBagiHasil($row)
    {
        return $row->latestPengembalian ? $row->latestPengembalian->sisa_bagi_hasil : $row->nilai_bagi_hasil;
    }

    private function getBayarPokok($row)
    {
        return $row->nilai_pinjaman - $this->getSisaPokok($row);
    }

    private function getBayarBagiHasil($row)
    {
        return $row->nilai_bagi_hasil - $this->getSisaBagiHasil($row);
    }
}
