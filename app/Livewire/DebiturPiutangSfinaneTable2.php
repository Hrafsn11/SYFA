<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengajuanPeminjaman;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class DebiturPiutangSfinaneTable2 extends DataTableComponent
{
    use HasDebiturAuthorization;

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_peminjaman');
        $this->setTableAttributes(['class' => 'table-bordered table-hover']);
        $this->setPaginationStatus(false);
        $this->setPerPageVisibilityStatus(false);
        $this->setSearchVisibilityStatus(false);
        $this->setFiltersVisibilityStatus(true);
        $this->setFiltersEnabled();
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Bulan')->options([
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
            ]),
            SelectFilter::make('Tahun')->options([
                '' => 'Semua Tahun',
                '2023' => '2023',
                '2024' => '2024',
                '2025' => '2025',
                '2026' => '2026',
            ]),
        ];
    }

    public function builder(): Builder
    {
        $dateFilter = $this->buildDateFilter($this->getAppliedFilters());

        $query = PengajuanPeminjaman::query()
            ->select([
                'pengajuan_peminjaman.id_pengajuan_peminjaman',
                'pengajuan_peminjaman.nomor_peminjaman',
                'pengajuan_peminjaman.total_bagi_hasil',
                'pengembalian_pinjaman.lama_pemakaian',
                DB::raw('COALESCE((SELECT nominal_yang_disetujui FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND validasi_dokumen = "disetujui" ORDER BY created_at DESC LIMIT 1), pengajuan_peminjaman.total_pinjaman) as nilai_pinjaman'),
                DB::raw("(SELECT MAX(created_at) FROM report_pengembalian WHERE nomor_peminjaman = pengajuan_peminjaman.nomor_peminjaman {$dateFilter}) as tanggal_bayar_terakhir"),
                DB::raw("(SELECT COALESCE(SUM(nilai_total_pengembalian), 0) FROM report_pengembalian WHERE nomor_peminjaman = pengajuan_peminjaman.nomor_peminjaman {$dateFilter}) as total_nilai_bayar"),
                DB::raw('(COALESCE(pengembalian_pinjaman.lama_pemakaian, 0) * 0.02 * COALESCE((SELECT nominal_yang_disetujui FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND validasi_dokumen = "disetujui" ORDER BY created_at DESC LIMIT 1), pengajuan_peminjaman.total_pinjaman)) as calculated_bagi_hasil'),
            ])
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_peminjaman.id_debitur', '=', 'master_debitur_dan_investor.id_debitur')
            ->leftJoin('bukti_peminjaman', 'pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'bukti_peminjaman.id_pengajuan_peminjaman')
            ->leftJoin($this->getLatestPengembalianSubquery(), 'pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'pengembalian_pinjaman.id_pengajuan_peminjaman')
            ->whereIn('pengajuan_peminjaman.status', ['Aktif', 'Dana Sudah Dicairkan', 'Lunas', 'Tertunda', 'Ditolak']);

        return $this->applyDebiturAuthorization($query);
    }

    private function buildDateFilter(array $filters): string
    {
        $bulan = $filters['Bulan'] ?? '';
        $tahun = $filters['Tahun'] ?? '';

        if ($bulan && $tahun) {
            return " AND DATE_FORMAT(created_at, '%Y-%m') = '{$tahun}-{$bulan}'";
        } elseif ($bulan) {
            return " AND MONTH(created_at) = '{$bulan}'";
        } elseif ($tahun) {
            return " AND YEAR(created_at) = '{$tahun}'";
        }

        return '';
    }

    public function columns(): array
    {
        return [
            Column::make('Tanggal Bayar')->label(fn($row) => $row->tanggal_bayar_terakhir ? Carbon::parse($row->tanggal_bayar_terakhir)->format('d/m/Y') : '-')->html(),
            Column::make('Nilai Bayar')->label(fn($row) => 'Rp ' . number_format($row->total_nilai_bayar, 0, ',', '.'))->html(),
            Column::make('Pembayaran Pokok')->label(fn($row) => 'Rp ' . number_format(max(0, $row->total_nilai_bayar - $row->calculated_bagi_hasil), 0, ',', '.'))->html(),
            Column::make('Pokok Bulan Selanjutnya')->label(function ($row) {
                $sisaPokok = max(0, $row->nilai_pinjaman - max(0, $row->total_nilai_bayar - $row->calculated_bagi_hasil));
                return '<strong class="text-primary">Rp ' . number_format($sisaPokok, 0, ',', '.') . '</strong>';
            })->html(),
        ];
    }
}
