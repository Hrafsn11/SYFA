<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasi;

class KertasKerjaInvestorSFinanceController extends Controller
{
    /**
     * Display kertas kerja investor SFinance report
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $page = $request->get('page', 1);

        $data = $this->getKertasKerjaData($year, $search);

        // Manual pagination
        $total = $data->count();
        $paginatedData = $data->slice(($page - 1) * $perPage, $perPage)->values();

        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
            'from' => (($page - 1) * $perPage) + 1,
            'to' => min($page * $perPage, $total),
        ];

        return view('livewire.kertas-kerja-investor-sfinance.index', compact('paginatedData', 'pagination', 'year', 'search', 'perPage'));
    }

    /**
     * Get kertas kerja data with optimized queries (Separate Queries Strategy)
     */
    private function getKertasKerjaData($year, $search = '')
    {
        $query = PengajuanInvestasi::query()
            ->select([
                'id_pengajuan_investasi',
                'tanggal_investasi',
                'deposito',
                'nama_investor',
                'jumlah_investasi',
                'lama_investasi',
                'bagi_hasil_pertahun',
                'nominal_bagi_hasil_yang_didapatkan',
                'sisa_pokok',
                'sisa_bagi_hasil',
                'status',
                'nomor_kontrak'
            ])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '');

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kontrak', 'LIKE', '%' . $search . '%')
                    ->orWhere('nama_investor', 'LIKE', '%' . $search . '%')
                    ->orWhere('deposito', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%');
            });
        }

        $investasi = $query->orderBy('tanggal_investasi', 'desc')->get();

        $totalPengembalian = DB::table('pengembalian_investasi')
            ->select([
                'id_pengajuan_investasi',
                DB::raw('SUM(dana_pokok_dibayar) as total_pokok_all'),
                DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil_all')
            ])
            ->groupBy('id_pengajuan_investasi')
            ->get()
            ->keyBy('id_pengajuan_investasi');

        $tanggalPengembalianTerakhir = DB::table('pengembalian_investasi')
            ->select([
                'id_pengajuan_investasi',
                DB::raw('MAX(tanggal_pengembalian) as tanggal_terakhir')
            ])
            ->groupBy('id_pengajuan_investasi')
            ->get()
            ->keyBy('id_pengajuan_investasi');

        $result = $investasi->map(function ($inv) use ($totalPengembalian, $tanggalPengembalianTerakhir, $year) {
            $id = $inv->id_pengajuan_investasi;
            $total = $totalPengembalian->get($id);
            $tglTerakhir = $tanggalPengembalianTerakhir->get($id);

            $bagiHasilPerBulan = $inv->bagi_hasil_pertahun / 12;
            $cofBulan = ($inv->jumlah_investasi * $bagiHasilPerBulan) / 100;

            $tanggalMulai = \Carbon\Carbon::parse($inv->tanggal_investasi);
            $tanggalAkhirPeriode = \Carbon\Carbon::create($year, 12, 31);

            // Hitung CoF akhir periode
            if ($tanggalMulai->year > $year) {
                $cofAkhirPeriode = 0;
            } else {
                $tanggalSekarang = \Carbon\Carbon::now();
                $tanggalBatas = $tanggalAkhirPeriode->lt($tanggalSekarang) ? $tanggalAkhirPeriode : $tanggalSekarang;

                $bulanBerjalan = max(0, $tanggalMulai->diffInMonths($tanggalBatas) + 1);

                $totalSeharusnya = $cofBulan * $bulanBerjalan;

                $totalDibayar = DB::table('pengembalian_investasi')
                    ->where('id_pengajuan_investasi', $id)
                    ->where('tanggal_pengembalian', '<=', $tanggalBatas)
                    ->sum('bagi_hasil_dibayar');

                $cofAkhirPeriode = max(0, $totalSeharusnya - $totalDibayar);
            }

            // Hitung CoF per bulan (Jan-Des) berdasarkan periode investasi aktif
            $tanggalJatuhTempo = \Carbon\Carbon::parse($inv->tanggal_investasi)->addMonths($inv->lama_investasi);
            $cofPerBulan = [];

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                // Buat tanggal untuk bulan tersebut di tahun yang dipilih
                $tanggalBulanIni = \Carbon\Carbon::create($year, $bulan, 1);
                $tanggalAkhirBulanIni = $tanggalBulanIni->copy()->endOfMonth();

                // Cek apakah bulan ini dalam periode investasi aktif
                $isAktif = $tanggalMulai->lte($tanggalAkhirBulanIni) && $tanggalJatuhTempo->gte($tanggalBulanIni);

                // Jika aktif, isi dengan CoF per bulan, jika tidak isi dengan 0
                $cofPerBulan[$bulan] = $isAktif ? $cofBulan : 0;
            }

            return [
                'id' => $id,
                'nomor_kontrak' => $inv->nomor_kontrak,
                'tanggal_uang_masuk' => $inv->tanggal_investasi,
                'deposito' => $inv->deposito,
                'deposan' => $inv->nama_investor,
                'nominal_deposito' => $inv->jumlah_investasi,
                'lama_deposito' => $inv->lama_investasi,
                'bagi_hasil_pa' => $inv->bagi_hasil_pertahun,
                'bagi_hasil_nominal' => $inv->nominal_bagi_hasil_yang_didapatkan,
                'bagi_hasil_per_bulan' => $bagiHasilPerBulan,
                'cof_bulan' => $cofBulan,
                'cof_akhir_periode' => $cofAkhirPeriode,
                'status' => $inv->status,
                'tgl_pengembalian' => $tglTerakhir->tanggal_terakhir ?? null,

                // CoF per bulan berdasarkan periode investasi aktif
                'jan' => $cofPerBulan[1],
                'feb' => $cofPerBulan[2],
                'mar' => $cofPerBulan[3],
                'apr' => $cofPerBulan[4],
                'mei' => $cofPerBulan[5],
                'jun' => $cofPerBulan[6],
                'jul' => $cofPerBulan[7],
                'agu' => $cofPerBulan[8],
                'sep' => $cofPerBulan[9],
                'okt' => $cofPerBulan[10],
                'nov' => $cofPerBulan[11],
                'des' => $cofPerBulan[12],

                'pengembalian_pokok' => $total->total_pokok_all ?? 0,
                'pengembalian_bagi_hasil' => $total->total_bagi_hasil_all ?? 0,
                'sisa_pokok' => $inv->sisa_pokok,
                'sisa_bagi_hasil' => $inv->sisa_bagi_hasil,
                'total_belum_dikembalikan' => $inv->sisa_pokok + $inv->sisa_bagi_hasil,
            ];
        });

        return $result;
    }
}
