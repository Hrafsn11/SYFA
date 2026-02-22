<?php

namespace App\Exports;

use App\Models\PengajuanPeminjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DebiturPiutangExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $searchTerm;

    public function __construct($searchTerm = null)
    {
        $this->searchTerm = $searchTerm;
    }

    public function collection()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->hasRole(['super-admin', 'admin', 'sfinance']);

        $query = PengajuanPeminjaman::query()
            ->select([
                'pengajuan_peminjaman.id_pengajuan_peminjaman',
                'pengajuan_peminjaman.nomor_peminjaman',
                'pengajuan_peminjaman.total_pinjaman',
                'pengajuan_peminjaman.harapan_tanggal_pencairan',
                'pengajuan_peminjaman.status',
                'pengajuan_peminjaman.persentase_bunga',
                'pengajuan_peminjaman.total_bunga',
                'master_debitur_dan_investor.nama as nama_debitur',
                'bukti_peminjaman.nama_client as objek_jaminan',
                'bukti_peminjaman.no_invoice',
                'bukti_peminjaman.no_kontrak',
                'pengembalian_pinjaman.lama_pemakaian as masa_penggunaan',
                'pengembalian_pinjaman.sisa_bunga as kurang_bayar_bunga',
                'pengembalian_pinjaman.sisa_bayar_pokok as sisa_pokok',
                DB::raw('(SELECT nominal_yang_disetujui FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND validasi_dokumen = "disetujui" ORDER BY created_at DESC LIMIT 1) as nilai_dicairkan'),
                DB::raw('(SELECT tanggal_pencairan FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND tanggal_pencairan IS NOT NULL ORDER BY created_at DESC LIMIT 1) as tanggal_pencairan'),
                DB::raw('(SELECT SUM(nilai_total_pengembalian) FROM report_pengembalian WHERE id_pengembalian = pengembalian_pinjaman.ulid) as nilai_bayar_total'),
                DB::raw('(SELECT MAX(created_at) FROM report_pengembalian WHERE id_pengembalian = pengembalian_pinjaman.ulid) as tanggal_bayar_terakhir'),
            ])
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_peminjaman.id_debitur', '=', 'master_debitur_dan_investor.id_debitur')
            ->leftJoin('bukti_peminjaman', 'pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'bukti_peminjaman.id_pengajuan_peminjaman')
            ->leftJoin(
                DB::raw('(SELECT pp.* FROM pengembalian_pinjaman pp INNER JOIN (SELECT id_pengajuan_peminjaman, MAX(created_at) as max_created FROM pengembalian_pinjaman GROUP BY id_pengajuan_peminjaman) latest ON pp.id_pengajuan_peminjaman = latest.id_pengajuan_peminjaman AND pp.created_at = latest.max_created) as pengembalian_pinjaman'),
                'pengajuan_peminjaman.id_pengajuan_peminjaman',
                '=',
                'pengembalian_pinjaman.id_pengajuan_peminjaman'
            )
            ->whereIn('pengajuan_peminjaman.status', ['Aktif', 'Dana Sudah Dicairkan', 'Lunas', 'Tertunda', 'Ditolak']);

        // Authorization filter
        if (!$isAdmin) {
            $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();
            if ($debitur) {
                $query->where('pengajuan_peminjaman.id_debitur', $debitur->id_debitur);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Search filter
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('master_debitur_dan_investor.nama', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('bukti_peminjaman.nama_client', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('bukti_peminjaman.no_invoice', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('pengajuan_peminjaman.nomor_peminjaman', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('pengajuan_peminjaman.status', 'LIKE', '%' . $this->searchTerm . '%');
            });
        }

        return $query->orderBy('pengajuan_peminjaman.harapan_tanggal_pencairan', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Debitur',
            'Objek Jaminan',
            'No Invoice',
            'No Kontrak',
            'Nomor Peminjaman',
            'Tanggal Pengajuan',
            'Nilai Yang Diajukan',
            'Nilai Yang Dicairkan',
            'Tanggal Pencairan',
            'Masa Penggunaan (bulan)',
            'Bagi Hasil Oleh Debitur',
            '% Bagi Hasil',
            'Bagi Hasil/Bulan',
            'Nilai Yang Harus Dibayar',
            'Status',
            'Tanggal Bayar Terakhir',
            'Nilai Bayar Total',
            'Kurang Bayar Bagi Hasil',
            'Nilai Pokok Belum Bayar',
            'Total Sisa (Pokok + Bagi Hasil)',
        ];
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        $nilaiDicairkan = $row->nilai_dicairkan ?? 0;
        $bagiHasil = $row->total_bunga ?? 0;
        $masaPenggunaan = $row->masa_penggunaan ?? 1;
        $bagiHasilPerBulan = $masaPenggunaan > 0 ? $bagiHasil / $masaPenggunaan : 0;
        $nilaiHarusDibayar = $nilaiDicairkan + $bagiHasil;
        $sisaPokok = $row->sisa_pokok ?? 0;
        $kurangBayarBagiHasil = $row->kurang_bayar_bunga ?? 0;
        $totalSisa = $sisaPokok + $kurangBayarBagiHasil;

        return [
            $index,
            $row->nama_debitur ?? '-',
            $row->objek_jaminan ?? '-',
            $row->no_invoice ?? '-',
            $row->no_kontrak ?? '-',
            $row->nomor_peminjaman ?? '-',
            $row->harapan_tanggal_pencairan ? date('d/m/Y', strtotime($row->harapan_tanggal_pencairan)) : '-',
            $row->total_pinjaman ?? 0,
            $nilaiDicairkan,
            $row->tanggal_pencairan ? date('d/m/Y', strtotime($row->tanggal_pencairan)) : '-',
            $row->masa_penggunaan ?? 0,
            $bagiHasil,
            $row->persentase_bunga ?? 0,
            $bagiHasilPerBulan,
            $nilaiHarusDibayar,
            $row->status ?? '-',
            $row->tanggal_bayar_terakhir ? date('d/m/Y', strtotime($row->tanggal_bayar_terakhir)) : '-',
            $row->nilai_bayar_total ?? 0,
            $kurangBayarBagiHasil,
            $sisaPokok,
            $totalSisa,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 25,  // Nama Debitur
            'C' => 25,  // Objek Jaminan
            'D' => 15,  // No Invoice
            'E' => 15,  // No Kontrak
            'F' => 20,  // Nomor Peminjaman
            'G' => 15,  // Tanggal Pengajuan
            'H' => 20,  // Nilai Diajukan
            'I' => 20,  // Nilai Dicairkan
            'J' => 15,  // Tanggal Pencairan
            'K' => 20,  // Masa Penggunaan
            'L' => 20,  // Bagi Hasil
            'M' => 12,  // % Bagi Hasil
            'N' => 20,  // Bagi Hasil/Bulan
            'O' => 25,  // Nilai Harus Dibayar
            'P' => 12,  // Status
            'Q' => 20,  // Tanggal Bayar
            'R' => 20,  // Nilai Bayar
            'S' => 20,  // Kurang Bayar
            'T' => 20,  // Pokok Belum Bayar
            'U' => 25,  // Total Sisa
        ];
    }
}
