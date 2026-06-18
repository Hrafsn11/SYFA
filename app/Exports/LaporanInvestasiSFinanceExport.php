<?php

namespace App\Exports;

use App\Models\PengajuanInvestasi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanInvestasiSFinanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        protected string $year = '',
        protected string $globalSearch = '',
        protected string $filterStatus = ''
    ) {}

    public function collection(): Collection
    {
        $query = PengajuanInvestasi::query()
            ->select([
                'id_pengajuan_investasi',
                'tanggal_investasi',
                'jenis_investasi',
                'nama_investor',
                'jumlah_investasi',
                'lama_investasi',
                'bunga_pertahun',
                'nominal_bunga_yang_didapatkan',
                'sisa_pokok',
                'sisa_bunga',
                'status',
                'nomor_kontrak',
            ])
            ->where(function ($q) {
                $q->whereNotNull('nomor_kontrak')
                    ->where('nomor_kontrak', '!=', '')
                    ->orWhereHas('penyaluranDanaInvestasi');
            });

        $user = auth()->user();
        $hasUnrestrictedRole = false;

        if ($user) {
            if ($user->hasRole('super-admin')) {
                $hasUnrestrictedRole = true;
            } else {
                $roles = $user->roles;
                $hasUnrestrictedRole = $roles->contains(function ($role) {
                    return $role->restriction == 1;
                });
            }
        }

        if (!$hasUnrestrictedRole) {
            $debiturInvestor = $user ? $user->debitur : null;
            if ($debiturInvestor) {
                $query->where('pengajuan_investasi.id_debitur_dan_investor', $debiturInvestor->id_debitur);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        if (!empty($this->globalSearch)) {
            $search = $this->globalSearch;
            $query->where(function ($q) use ($search) {
                $q->where('nama_investor', 'like', '%' . $search . '%')
                    ->orWhere('nomor_kontrak', 'like', '%' . $search . '%')
                    ->orWhere('jenis_investasi', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        if (!empty($this->year)) {
            $query->whereYear('tanggal_investasi', (int) $this->year);
        }

        return $query->orderBy('tanggal_investasi', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Uang Masuk',
            'Jenis Investasi',
            'Deposan',
            'Nominal Deposit',
            'Lama Investasi (Bulan)',
            'Bunga (%PA)',
            'Bunga Nominal',
            'Bunga (%Bulan)',
            'Bunga (COF/Bulan)',
            'CoF Per Akhir Des',
            'Status',
            'Tgl Pengembalian Terakhir',
            'CoF Jan',
            'CoF Feb',
            'CoF Mar',
            'CoF Apr',
            'CoF Mei',
            'CoF Jun',
            'CoF Jul',
            'CoF Agu',
            'CoF Sep',
            'CoF Okt',
            'CoF Nov',
            'CoF Des',
            'Pengembalian Pokok',
            'Pengembalian Bunga',
            'Sisa Pokok',
            'Sisa Bunga',
            'Total Belum Dikembalikan',
        ];
    }

    public function map($row): array
    {
        $data = $this->getRowData($row);

        return [
            Carbon::parse($row->tanggal_investasi)->format('d-m-Y'),
            $row->jenis_investasi,
            $row->nama_investor,
            (float) $row->jumlah_investasi,
            (int) $row->lama_investasi,
            (float) $row->bunga_pertahun,
            (float) $row->nominal_bunga_yang_didapatkan,
            round($data['bunga_per_bulan'], 2),
            round($data['cof_bulan'], 2),
            round($data['cof_akhir_periode'], 2),
            $row->status,
            $data['tgl_pengembalian'] ? Carbon::parse($data['tgl_pengembalian'])->format('d-m-Y') : '-',
            round($data['cof_per_bulan'][1], 2),
            round($data['cof_per_bulan'][2], 2),
            round($data['cof_per_bulan'][3], 2),
            round($data['cof_per_bulan'][4], 2),
            round($data['cof_per_bulan'][5], 2),
            round($data['cof_per_bulan'][6], 2),
            round($data['cof_per_bulan'][7], 2),
            round($data['cof_per_bulan'][8], 2),
            round($data['cof_per_bulan'][9], 2),
            round($data['cof_per_bulan'][10], 2),
            round($data['cof_per_bulan'][11], 2),
            round($data['cof_per_bulan'][12], 2),
            round($data['pengembalian_pokok'], 2),
            round($data['pengembalian_bunga'], 2),
            (float) $row->sisa_pokok,
            (float) $row->sisa_bunga,
            (float) $row->sisa_pokok + (float) $row->sisa_bunga,
        ];
    }

    private function displayYear(): int
    {
        return !empty($this->year) ? (int) $this->year : (int) date('Y');
    }

    private function getRowData($row): array
    {
        $year = $this->displayYear();
        $id = $row->id_pengajuan_investasi;

        $bungaPerBulan = ((float) $row->bunga_pertahun) / 12;
        $cofBulan = ((float) $row->jumlah_investasi * $bungaPerBulan) / 100;

        $tanggalMulai = Carbon::parse($row->tanggal_investasi);
        $tanggalAkhirPeriode = Carbon::create($year, 12, 31);

        if ($tanggalMulai->year > $year) {
            $cofAkhirPeriode = 0;
        } else {
            $now = Carbon::now();
            $tanggalBatas = $tanggalAkhirPeriode->lt($now) ? $tanggalAkhirPeriode : $now;
            $bulanBerjalan = max(0, $tanggalMulai->diffInMonths($tanggalBatas) + 1);
            $totalSeharusnya = $cofBulan * $bulanBerjalan;

            $totalDibayar = DB::table('pengembalian_investasi')
                ->where('id_pengajuan_investasi', $id)
                ->where('tanggal_pengembalian', '<=', $tanggalBatas)
                ->sum('bunga_dibayar');

            $cofAkhirPeriode = max(0, $totalSeharusnya - $totalDibayar);
        }

        $tglTerakhir = DB::table('pengembalian_investasi')
            ->where('id_pengajuan_investasi', $id)
            ->max('tanggal_pengembalian');

        $tanggalJatuhTempo = $tanggalMulai->copy()->addMonths((int) $row->lama_investasi);
        $cofPerBulan = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $startOfMonth = Carbon::create($year, $bulan, 1);
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $isAktif = $tanggalMulai->lte($endOfMonth) && $tanggalJatuhTempo->gte($startOfMonth);
            $cofPerBulan[$bulan] = $isAktif ? $cofBulan : 0;
        }

        $total = DB::table('pengembalian_investasi')
            ->selectRaw('SUM(dana_pokok_dibayar) as total_pokok, SUM(bunga_dibayar) as total_bunga')
            ->where('id_pengajuan_investasi', $id)
            ->first();

        return [
            'bunga_per_bulan' => $bungaPerBulan,
            'cof_bulan' => $cofBulan,
            'cof_akhir_periode' => $cofAkhirPeriode,
            'tgl_pengembalian' => $tglTerakhir,
            'cof_per_bulan' => $cofPerBulan,
            'pengembalian_pokok' => $total->total_pokok ?? 0,
            'pengembalian_bunga' => $total->total_bunga ?? 0,
        ];
    }
}
