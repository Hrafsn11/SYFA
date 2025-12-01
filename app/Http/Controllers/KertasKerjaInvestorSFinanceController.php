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
        
        $data = $this->getKertasKerjaData($year);
        
        return view('livewire.kertas-kerja-investor-sfinance.index', compact('data', 'year'));
    }
    
    /**
     * Get kertas kerja data with optimized queries (Separate Queries Strategy)
     */
    private function getKertasKerjaData($year)
    {
        $investasi = PengajuanInvestasi::query()
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
            ->where('nomor_kontrak', '!=', '')
            ->orderBy('tanggal_investasi', 'desc')
            ->get();
        
        $pengembalianPerBulan = DB::table('pengembalian_investasi')
            ->select([
                'id_pengajuan_investasi',
                DB::raw('MONTH(tanggal_pengembalian) as bulan'),
                DB::raw('SUM(dana_pokok_dibayar) as total_pokok'),
                DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil')
            ])
            ->whereYear('tanggal_pengembalian', $year)
            ->groupBy('id_pengajuan_investasi', DB::raw('MONTH(tanggal_pengembalian)'))
            ->get()
            ->groupBy('id_pengajuan_investasi'); 
        
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
        
        $result = $investasi->map(function($inv) use ($pengembalianPerBulan, $totalPengembalian, $tanggalPengembalianTerakhir, $year) {
            $id = $inv->id_pengajuan_investasi;
            $pembayaranBulan = $pengembalianPerBulan->get($id, collect());
            $total = $totalPengembalian->get($id);
            $tglTerakhir = $tanggalPengembalianTerakhir->get($id);
            
            $bagiHasilPerBulan = $inv->bagi_hasil_pertahun / 12;
            $cofBulan = ($inv->jumlah_investasi * $bagiHasilPerBulan) / 100;
            
            $tanggalMulai = \Carbon\Carbon::parse($inv->tanggal_investasi);
            $tanggalAkhirPeriode = \Carbon\Carbon::create($year, 12, 31);
            
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
                
                'jan' => $pembayaranBulan->where('bulan', 1)->first()->total_bagi_hasil ?? 0,
                'feb' => $pembayaranBulan->where('bulan', 2)->first()->total_bagi_hasil ?? 0,
                'mar' => $pembayaranBulan->where('bulan', 3)->first()->total_bagi_hasil ?? 0,
                'apr' => $pembayaranBulan->where('bulan', 4)->first()->total_bagi_hasil ?? 0,
                'mei' => $pembayaranBulan->where('bulan', 5)->first()->total_bagi_hasil ?? 0,
                'jun' => $pembayaranBulan->where('bulan', 6)->first()->total_bagi_hasil ?? 0,
                'jul' => $pembayaranBulan->where('bulan', 7)->first()->total_bagi_hasil ?? 0,
                'agu' => $pembayaranBulan->where('bulan', 8)->first()->total_bagi_hasil ?? 0,
                'sep' => $pembayaranBulan->where('bulan', 9)->first()->total_bagi_hasil ?? 0,
                'okt' => $pembayaranBulan->where('bulan', 10)->first()->total_bagi_hasil ?? 0,
                'nov' => $pembayaranBulan->where('bulan', 11)->first()->total_bagi_hasil ?? 0,
                'des' => $pembayaranBulan->where('bulan', 12)->first()->total_bagi_hasil ?? 0,
                
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
