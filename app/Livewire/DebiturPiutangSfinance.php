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
        $this->setPrimaryKey('id_pengajuan_peminjaman')
            ->setAdditionalSelects(['pengajuan_peminjaman.id_pengajuan_peminjaman'])
            ->setTableAttributes(['class' => 'table-responsive text-nowrap'])
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setColumnSelectStatus(true)
            ->setSearchEnabled()
            ->setSearchPlaceholder('Cari debitur, objek jaminan...')
            ->setEmptyMessage('Tidak ada data debitur piutang');
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
                'pengajuan_peminjaman.tanggal_jatuh_tempo',
                'master_debitur_dan_investor.nama as nama_debitur',
                'first_bukti.id_bukti_peminjaman',
                'first_bukti.nama_client as objek_jaminan',
                'first_bukti.no_invoice',
                'first_bukti.no_kontrak',
                'pengembalian_pinjaman.lama_pemakaian as masa_penggunaan',
                'pengembalian_pinjaman.sisa_bagi_hasil as kurang_bayar_bagi_hasil',
                'pengembalian_pinjaman.sisa_bayar_pokok as sisa_pokok',
                'pengembalian_pinjaman.ulid as id_pengembalian_pinjaman',
                DB::raw('(SELECT nominal_yang_disetujui FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND validasi_dokumen = "disetujui" ORDER BY created_at DESC LIMIT 1) as nilai_dicairkan'),
                DB::raw('(SELECT id_history_status_pengajuan_pinjaman FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND validasi_dokumen = "disetujui" ORDER BY created_at DESC LIMIT 1) as id_history_dicairkan'),
                DB::raw('(SELECT tanggal_pencairan FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND tanggal_pencairan IS NOT NULL ORDER BY created_at DESC LIMIT 1) as tanggal_pencairan'),
                DB::raw('(SELECT SUM(nilai_total_pengembalian) FROM report_pengembalian WHERE id_pengembalian = pengembalian_pinjaman.ulid) as nilai_bayar_total'),
                DB::raw('(SELECT MAX(created_at) FROM report_pengembalian WHERE id_pengembalian = pengembalian_pinjaman.ulid) as tanggal_bayar_terakhir'),
                DB::raw('(SELECT COALESCE(SUM(nilai_total_pengembalian), 0) FROM report_pengembalian WHERE nomor_peminjaman = pengajuan_peminjaman.nomor_peminjaman) as total_bayar'),
                DB::raw('(SELECT COALESCE(MAX(hari_keterlambatan), 0) FROM report_pengembalian WHERE nomor_peminjaman = pengajuan_peminjaman.nomor_peminjaman) as telat_hari'),
            ])
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_peminjaman.id_debitur', '=', 'master_debitur_dan_investor.id_debitur')
           
            ->leftJoin(DB::raw('(
                SELECT bp1.* 
                FROM bukti_peminjaman bp1
                INNER JOIN (
                    SELECT id_pengajuan_peminjaman, MIN(id_bukti_peminjaman) as first_id
                    FROM bukti_peminjaman
                    GROUP BY id_pengajuan_peminjaman
                ) bp2 ON bp1.id_bukti_peminjaman = bp2.first_id
            ) as first_bukti'), 'pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'first_bukti.id_pengajuan_peminjaman')
            ->leftJoin($this->getLatestPengembalianSubquery(), 'pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'pengembalian_pinjaman.id_pengajuan_peminjaman')
            ->whereIn('pengajuan_peminjaman.status', ['Aktif', 'Dana Sudah Dicairkan', 'Lunas', 'Tertunda', 'Ditolak']);

        if ($search = $this->getSearch()) {
            $query->where(function ($q) use ($search) {
                $q->where('master_debitur_dan_investor.nama', 'LIKE', '%' . $search . '%')
                    ->orWhere('first_bukti.nama_client', 'LIKE', '%' . $search . '%')
                    ->orWhere('first_bukti.no_invoice', 'LIKE', '%' . $search . '%')
                    ->orWhere('first_bukti.no_kontrak', 'LIKE', '%' . $search . '%')
                    ->orWhere('pengajuan_peminjaman.nomor_peminjaman', 'LIKE', '%' . $search . '%')
                    ->orWhere('pengajuan_peminjaman.status', 'LIKE', '%' . $search . '%');
            });
        }

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

            Column::make('Nama Debitur', 'nama_debitur')
                ->label(fn($row) => $row->nama_debitur)
                ->sortable(),

            Column::make('Objek Jaminan', 'objek_jaminan')
                ->label(fn($row) => $row->objek_jaminan)
                ->sortable(),

            Column::make('Tgl Pengajuan', 'harapan_tanggal_pencairan')
                ->label(fn($row) => $row->harapan_tanggal_pencairan ? Carbon::parse($row->harapan_tanggal_pencairan)->format('d/m/Y') : '-')
                ->sortable(),

            Column::make('Nilai Yang Diajukan', 'total_pinjaman')
                ->label(fn($row) => 'Rp ' . number_format($row->total_pinjaman ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('Nilai Yang Dicairkan', 'nilai_dicairkan')
                ->label(fn($row) => 'Rp ' . number_format($row->nilai_dicairkan ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('Tanggal Pencairan', 'tanggal_pencairan')
                ->label(fn($row) => $row->tanggal_pencairan ? Carbon::parse($row->tanggal_pencairan)->format('d/m/Y') : '-')
                ->sortable(),

            Column::make('Masa Penggunaan', 'masa_penggunaan')
                ->label(fn($row) => ($row->masa_penggunaan ?? 0) . ' hari')
                ->sortable(),

            Column::make('Tanggal Jatuh Tempo', 'tanggal_jatuh_tempo')
                ->label(function ($row) {
                    if (!$row->tanggal_jatuh_tempo) return '-';
                    $jatuhTempo = Carbon::parse($row->tanggal_jatuh_tempo);
                    $isLate = $jatuhTempo->isPast();
                    $formatted = $jatuhTempo->format('d/m/Y');
                    if ($isLate) {
                        return '<span class="text-danger">' . $formatted . ' <span class="badge bg-danger">Jatuh Tempo</span></span>';
                    }
                    return $formatted;
                })
                ->html()
                ->sortable(),

            Column::make('Bagi Hasil Oleh Debitur', 'total_bagi_hasil')
                ->label(fn($row) => 'Rp ' . number_format($row->total_bagi_hasil ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('Nilai Yang Harus Dibayar')->label(function ($row) {
                $nilaiDicairkan = $row->nilai_dicairkan ?? 0;
                $bagiHasil = $row->total_bagi_hasil ?? 0;
                return 'Rp ' . number_format($nilaiDicairkan + $bagiHasil, 0, ',', '.');
            }),

            Column::make('Status', 'status')->sortable(),

            Column::make('Tanggal Bayar', 'tanggal_bayar_terakhir')
                ->label(fn($row) => $row->tanggal_bayar_terakhir ? Carbon::parse($row->tanggal_bayar_terakhir)->format('d/m/Y') : '-')
                ->sortable(),

            Column::make('Lama Pinjaman', 'masa_penggunaan')
                ->label(fn($row) => ($row->masa_penggunaan ?? 0) . ' hari')
                ->sortable(),

            Column::make('Nilai Bayar', 'nilai_bayar_total')
                ->label(fn($row) => 'Rp ' . number_format($row->nilai_bayar_total ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('Total Sisa Pokok + Bagi Hasil')->label(function ($row) {
                $sisaPokok = $row->sisa_pokok ?? 0;
                $sisaBagiHasil = $row->kurang_bayar_bagi_hasil ?? 0;
                return 'Rp ' . number_format($sisaPokok + $sisaBagiHasil, 0, ',', '.');
            }),

            Column::make('Total Kurang Bayar Bagi Hasil', 'kurang_bayar_bagi_hasil')
                ->label(fn($row) => 'Rp ' . number_format($row->kurang_bayar_bagi_hasil ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('Nilai Pokok Yang Belum Bayar', 'sisa_pokok')
                ->label(fn($row) => 'Rp ' . number_format($row->sisa_pokok ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('% Bagi Hasil', 'persentase_bagi_hasil')
                ->label(fn($row) => ($row->persentase_bagi_hasil ?? 0) . '%')
                ->sortable(),

            Column::make('Bagi Hasil/Bulan')->label(function ($row) {
                $bagiHasil = $row->total_bagi_hasil ?? 0;
                $masaPenggunaanHari = $row->masa_penggunaan ?? 0;
                // Konversi hari ke bulan (30 hari = 1 bulan), minimal 1 bulan
                $masaPenggunaanBulan = $masaPenggunaanHari > 0 ? max(1, ceil($masaPenggunaanHari / 30)) : 1;
                $bagiHasilPerBulan = $bagiHasil / $masaPenggunaanBulan;
                return 'Rp ' . number_format($bagiHasilPerBulan, 0, ',', '.');
            }),

            Column::make('Aksi')
                ->label(function ($row) {
                    if (!auth()->user()->can('debitur_piutang.edit')) {
                        return '-';
                    }

                    $data = json_encode([
                        'id_pengajuan' => $row->id_pengajuan_peminjaman,
                        'id_bukti' => $row->id_bukti_peminjaman,
                        'id_history' => $row->id_history_dicairkan,
                        'id_pengembalian' => $row->id_pengembalian_pinjaman,
                        'objek_jaminan' => $row->objek_jaminan,
                        'nilai_dicairkan' => $row->nilai_dicairkan,
                        'persentase_bagi_hasil' => $row->persentase_bagi_hasil,
                        'kurang_bayar_bagi_hasil' => $row->kurang_bayar_bagi_hasil,
                    ]);

                    return '<button type="button" class="btn btn-sm btn-primary edit-debitur-piutang-btn" 
                                data-row=\'' . htmlspecialchars($data, ENT_QUOTES) . '\'>
                                <i class="ti ti-edit"></i>
                            </button>';
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('Subtotal Sisa Pokok + Bagi Hasil')
                ->label(function ($row) {
                    // Jika belum ada pengembalian, gunakan nilai awal
                    $sisaPokok = $row->sisa_pokok ?? $row->nilai_dicairkan ?? 0;
                    $sisaBagiHasil = $row->kurang_bayar_bagi_hasil ?? $row->total_bagi_hasil ?? 0;
                    $subtotal = $sisaPokok + $sisaBagiHasil;
                    return '<strong>Rp ' . number_format($subtotal, 0, ',', '.') . '</strong>';
                })
                ->html(),

            Column::make('Sisa Pokok', 'sisa_pokok')
                ->label(function ($row) {
                    // Jika belum ada pengembalian, gunakan nilai_dicairkan
                    $sisaPokok = $row->sisa_pokok ?? $row->nilai_dicairkan ?? 0;
                    return 'Rp ' . number_format($sisaPokok, 0, ',', '.');
                })
                ->html()
                ->sortable(),

            Column::make('Sisa Bagi Hasil', 'kurang_bayar_bagi_hasil')
                ->label(function ($row) {
                    // Jika belum ada pengembalian, gunakan total_bagi_hasil
                    $sisaBagiHasil = $row->kurang_bayar_bagi_hasil ?? $row->total_bagi_hasil ?? 0;
                    return 'Rp ' . number_format($sisaBagiHasil, 0, ',', '.');
                })
                ->html()
                ->sortable(),

            Column::make('Telat Hari', 'telat_hari')
                ->label(function ($row) {
                    // hari_keterlambatan berisi string seperti "14 Hari", extract angkanya
                    $telatHariRaw = $row->telat_hari ?? '0';
                    // Extract angka dari string
                    $telatHari = intval(preg_replace('/[^0-9]/', '', $telatHariRaw));
                    
                    // Cek juga berdasarkan tanggal_jatuh_tempo jika ada
                    if ($row->tanggal_jatuh_tempo) {
                        $jatuhTempo = \Carbon\Carbon::parse($row->tanggal_jatuh_tempo);
                        $today = \Carbon\Carbon::today();
                        
                        // Jika belum melewati tanggal jatuh tempo, tidak telat
                        if ($today->lte($jatuhTempo)) {
                            return '<span class="badge bg-success">0 hari</span>';
                        }
                        
                        // Hitung hari keterlambatan dari tanggal jatuh tempo
                        $telatHari = $today->diffInDays($jatuhTempo);
                    }
                    
                    $badgeClass = $telatHari > 0 ? 'danger' : 'success';
                    return '<span class="badge bg-' . $badgeClass . '">' . $telatHari . ' hari</span>';
                })
                ->html()
                ->sortable(),
        ];
    }
}
