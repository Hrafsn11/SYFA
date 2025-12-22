<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDebiturAuthorization;
use App\Models\PengajuanPeminjaman;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DebiturPiutangSfinance extends DataTableComponent
{
    use HasDebiturAuthorization;

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengajuan_peminjaman');
        $this->setAdditionalSelects(['pengajuan_peminjaman.id_pengajuan_peminjaman']);
        $this->setTableAttributes(['class' => 'table-responsive text-nowrap']);
        $this->setPerPageAccepted([10, 25, 50, 100]);
    }

    public function builder(): Builder
    {
        $query = PengajuanPeminjaman::query()
            ->select([
                'pengajuan_peminjaman.id_pengajuan_peminjaman',
                'pengajuan_peminjaman.nomor_peminjaman',
                'pengajuan_peminjaman.total_pinjaman',
                'pengajuan_peminjaman.harapan_tanggal_pencairan',
                'pengajuan_peminjaman.status',
                'pengajuan_peminjaman.persentase_bagi_hasil',
                'pengajuan_peminjaman.total_bagi_hasil',
                'master_debitur_dan_investor.nama as nama_debitur',
                'bukti_peminjaman.nama_client as objek_jaminan',
                'bukti_peminjaman.no_invoice',
                'bukti_peminjaman.no_kontrak',
                'pengembalian_pinjaman.lama_pemakaian as masa_penggunaan',
                'pengembalian_pinjaman.sisa_bagi_hasil as kurang_bayar_bagi_hasil',
                'pengembalian_pinjaman.sisa_bayar_pokok as sisa_pokok',
                'pengembalian_pinjaman.ulid as id_pengembalian_pinjaman',
                DB::raw('(SELECT nominal_yang_disetujui FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND validasi_dokumen = "disetujui" ORDER BY created_at DESC LIMIT 1) as nilai_dicairkan'),
                DB::raw('(SELECT tanggal_pencairan FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND tanggal_pencairan IS NOT NULL ORDER BY created_at DESC LIMIT 1) as tanggal_pencairan'),
                DB::raw('(SELECT SUM(nilai_total_pengembalian) FROM report_pengembalian WHERE id_pengembalian = pengembalian_pinjaman.ulid) as nilai_bayar_total'),
                DB::raw('(SELECT MAX(created_at) FROM report_pengembalian WHERE id_pengembalian = pengembalian_pinjaman.ulid) as tanggal_bayar_terakhir'),
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
            Column::make('No')
                ->label(function ($row) use (&$rowNumber) {
                    $rowNumber++;
                    $number = (($this->getPage() - 1) * $this->getPerPage()) + $rowNumber;

                    return '<div class="text-center">' . $number . '</div>';
                })
                ->html()
                ->excludeFromColumnSelect(),
            Column::make('Nama Debitur')->label(fn($row) => $row->nama_debitur)->searchable(),

            Column::make('Objek Jaminan')->label(fn($row) => $row->objek_jaminan),

            Column::make('Tgl Pengajuan')->label(fn($row) => $row->harapan_tanggal_pencairan ? Carbon::parse($row->harapan_tanggal_pencairan)->format('d/m/Y') : '-'),

            Column::make('Nilai Yang Diajukan')->label(fn($row) => 'Rp ' . number_format($row->total_pinjaman ?? 0, 0, ',', '.')),

            Column::make('Nilai Yang Dicairkan')->label(fn($row) => 'Rp ' . number_format($row->nilai_dicairkan ?? 0, 0, ',', '.')),

            Column::make('Tanggal Pencairan')->label(fn($row) => $row->tanggal_pencairan ? Carbon::parse($row->tanggal_pencairan)->format('d/m/Y') : '-'),

            Column::make('Masa Penggunaan')->label(fn($row) => ($row->masa_penggunaan ?? 0) . ' bulan'),

            Column::make('Bagi Hasil Oleh Debitur')->label(fn($row) => 'Rp ' . number_format($row->total_bagi_hasil ?? 0, 0, ',', '.')),

            Column::make('Nilai Yang Harus Dibayar')->label(function ($row) {
                $nilaiDicairkan = $row->nilai_dicairkan ?? 0;
                $bagiHasil = $row->total_bagi_hasil ?? 0;
                return 'Rp ' . number_format($nilaiDicairkan + $bagiHasil, 0, ',', '.');
            }),

            Column::make('Status', 'status')->sortable(),

            Column::make('Tanggal Bayar')->label(fn($row) => $row->tanggal_bayar_terakhir ? Carbon::parse($row->tanggal_bayar_terakhir)->format('d/m/Y') : '-'),

            Column::make('Lama Pinjaman')->label(fn($row) => ($row->masa_penggunaan ?? 0) . ' bulan'),

            Column::make('Nilai Bayar')->label(fn($row) => 'Rp ' . number_format($row->nilai_bayar_total ?? 0, 0, ',', '.')),

            Column::make('Total Sisa Pokok + Bagi Hasil')->label(function ($row) {
                $sisaPokok = $row->sisa_pokok ?? 0;
                $sisaBagiHasil = $row->kurang_bayar_bagi_hasil ?? 0;
                return 'Rp ' . number_format($sisaPokok + $sisaBagiHasil, 0, ',', '.');
            }),

            Column::make('Total Kurang Bayar Bagi Hasil')->label(fn($row) => 'Rp ' . number_format($row->kurang_bayar_bagi_hasil ?? 0, 0, ',', '.')),

            Column::make('Nilai Pokok Yang Belum Bayar')->label(fn($row) => 'Rp ' . number_format($row->sisa_pokok ?? 0, 0, ',', '.')),

            Column::make('% Bagi Hasil')->label(fn($row) => ($row->persentase_bagi_hasil ?? 0) . '%'),

            Column::make('Bagi Hasil/Bulan')->label(function ($row) {
                $bagiHasil = $row->total_bagi_hasil ?? 0;
                $masaPenggunaan = $row->masa_penggunaan ?? 1;
                $bagiHasilPerBulan = $masaPenggunaan > 0 ? $bagiHasil / $masaPenggunaan : 0;
                return 'Rp ' . number_format($bagiHasilPerBulan, 0, ',', '.');
            }),
        ];
    }
}
