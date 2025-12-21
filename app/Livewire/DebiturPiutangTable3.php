<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengajuanPeminjaman;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DebiturPiutangTable3 extends DataTableComponent
{
    use HasDebiturAuthorization;

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_peminjaman');
        $this->setTableAttributes(['class' => 'table-bordered table-hover']);
        $this->setPaginationStatus(false);
        $this->setPerPageVisibilityStatus(false);
        $this->setSearchVisibilityStatus(false);
        $this->setFiltersVisibilityStatus(false);
    }

    public function builder(): Builder
    {
        $query = PengajuanPeminjaman::query()
            ->select([
                'pengajuan_peminjaman.id_pengajuan_peminjaman',
                'pengajuan_peminjaman.nomor_peminjaman',
                'pengembalian_pinjaman.sisa_bagi_hasil',
                'pengembalian_pinjaman.lama_pemakaian',
                DB::raw('COALESCE((SELECT nominal_yang_disetujui FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND validasi_dokumen = "disetujui" ORDER BY created_at DESC LIMIT 1), pengajuan_peminjaman.total_pinjaman) as nilai_dicairkan'),
                DB::raw('(SELECT COALESCE(SUM(nilai_total_pengembalian), 0) FROM report_pengembalian WHERE nomor_peminjaman = pengajuan_peminjaman.nomor_peminjaman) as total_bayar'),
                DB::raw('(SELECT COALESCE(MAX(hari_keterlambatan), 0) FROM report_pengembalian WHERE nomor_peminjaman = pengajuan_peminjaman.nomor_peminjaman) as telat_hari'),
            ])
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_peminjaman.id_debitur', '=', 'master_debitur_dan_investor.id_debitur')
            ->leftJoin('bukti_peminjaman', 'pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'bukti_peminjaman.id_pengajuan_peminjaman')
            ->leftJoin($this->getLatestPengembalianSubquery(), 'pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'pengembalian_pinjaman.id_pengajuan_peminjaman')
            ->whereIn('pengajuan_peminjaman.status', ['Aktif', 'Dana Sudah Dicairkan', 'Lunas', 'Tertunda', 'Ditolak']);

        return $this->applyDebiturAuthorization($query);
    }

    public function columns(): array
    {
        return [
            Column::make('Subtotal Sisa Pokok')->label(function ($row) {
                $sisaPokok = max(0, $row->nilai_dicairkan - $row->total_bayar);
                $subtotal = $sisaPokok + ($row->sisa_bagi_hasil ?? 0);
                return 'Rp ' . number_format($subtotal, 0, ',', '.');
            })->html(),

            Column::make('Pokok')->label(function ($row) {
                $sisaPokok = max(0, $row->nilai_dicairkan - $row->total_bayar);
                return 'Rp ' . number_format($sisaPokok, 0, ',', '.');
            })->html(),

            Column::make('Sisa Bagi Hasil')->label(fn($row) => 'Rp ' . number_format($row->sisa_bagi_hasil ?? 0, 0, ',', '.'))->html(),

            Column::make('Telat Hari')->label(function ($row) {
                $badgeClass = ($row->telat_hari ?? 0) > 0 ? 'danger' : 'success';
                return '<span class="badge bg-' . $badgeClass . '">' . ($row->telat_hari ?? 0) . '</span>';
            })->html(),
        ];
    }
}
