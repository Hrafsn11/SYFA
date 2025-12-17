<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanInvestasiFinlog;

class KertasKerjaInvestorSFinlogController extends Controller
{
    /**
     * Display kertas kerja investor SFinlog report
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        $data = $this->getKertasKerjaData($year);
        
        return view('livewire.sfinlog.kertas-kerja-investor-sfinlog.index', compact('data', 'year'));
    }
    
    /**
     * Get kertas kerja data with optimized queries (Separate Queries Strategy)
     */
    private function getKertasKerjaData($year)
    {
        $investasi = PengajuanInvestasiFinlog::query()
            ->select([
                'id_pengajuan_investasi_finlog',
                'tanggal_investasi',
                'nama_investor',
                'nominal_investasi',
                'lama_investasi',
                'persentase_bagi_hasil',
                'nominal_bagi_hasil_yang_didapat',
                'status',
                'nomor_kontrak'
            ])
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->orderBy('tanggal_investasi', 'desc')
            ->get();
        
        $pengembalianPerBulan = DB::table('pengembalian_investasi_finlog')
            ->select([
                'id_pengajuan_investasi_finlog',
                DB::raw('MONTH(tanggal_pengembalian) as bulan'),
                DB::raw('SUM(dana_pokok_dibayar) as total_pokok'),
                DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil')
            ])
            ->whereYear('tanggal_pengembalian', $year)
            ->groupBy('id_pengajuan_investasi_finlog', DB::raw('MONTH(tanggal_pengembalian)'))
            ->get()
            ->groupBy('id_pengajuan_investasi_finlog'); 
        
        $totalPengembalian = DB::table('pengembalian_investasi_finlog')
            ->select([
                'id_pengajuan_investasi_finlog',
                DB::raw('SUM(dana_pokok_dibayar) as total_pokok_all'),
                DB::raw('SUM(bagi_hasil_dibayar) as total_bagi_hasil_all')
            ])
            ->groupBy('id_pengajuan_investasi_finlog')
            ->get()
            ->keyBy('id_pengajuan_investasi_finlog'); 
        
        $result = $investasi->map(function($inv) use ($pengembalianPerBulan, $totalPengembalian, $year) {
            $id = $inv->id_pengajuan_investasi_finlog;
            $pembayaranBulan = $pengembalianPerBulan->get($id, collect());
            $total = $totalPengembalian->get($id);
            
            // Bagi hasil per bulan = persentase bagi hasil / 12
            $bagiHasilPerBulan = $inv->persentase_bagi_hasil / 12;
            
            // COF per bulan = nominal investasi * bagi hasil per bulan / 100
            $cofBulan = ($inv->nominal_investasi * $bagiHasilPerBulan) / 100;
            
            // COF per akhir periode (31 Desember tahun yang dipilih)
            $tanggalMulai = \Carbon\Carbon::parse($inv->tanggal_investasi);
            $tanggalAkhirPeriode = \Carbon\Carbon::create($year, 12, 31);
            
            if ($tanggalMulai->year > $year) {
                $cofAkhirPeriode = 0;
            } else {
                $tanggalSekarang = \Carbon\Carbon::now();
                $tanggalBatas = $tanggalAkhirPeriode->lt($tanggalSekarang) ? $tanggalAkhirPeriode : $tanggalSekarang;
                
                // Hitung bulan berjalan dari tanggal investasi sampai batas
                $bulanBerjalan = max(0, $tanggalMulai->diffInMonths($tanggalBatas) + 1);
                
                // Total yang seharusnya dibayar
                $totalSeharusnya = $cofBulan * $bulanBerjalan;
                
                // Total yang sudah dibayar sampai batas
                $totalDibayar = DB::table('pengembalian_investasi_finlog')
                    ->where('id_pengajuan_investasi_finlog', $id)
                    ->where('tanggal_pengembalian', '<=', $tanggalBatas)
                    ->sum('bagi_hasil_dibayar');
                
                // COF akhir periode = total seharusnya - total dibayar
                $cofAkhirPeriode = max(0, $totalSeharusnya - $totalDibayar);
            }
            
            // Hitung sisa pokok dan sisa bagi hasil
            $totalPokokDikembalikan = $total->total_pokok_all ?? 0;
            $totalBagiHasilDibayar = $total->total_bagi_hasil_all ?? 0;
            
            // Sisa pokok = nominal investasi - total pokok yang sudah dikembalikan
            $sisaPokok = max(0, $inv->nominal_investasi - $totalPokokDikembalikan);
            
            // Sisa bagi hasil = nominal bagi hasil yang didapat - total bagi hasil yang sudah dibayar
            $sisaBagiHasil = max(0, ($inv->nominal_bagi_hasil_yang_didapat ?? 0) - $totalBagiHasilDibayar);
            
            return [
                'id' => $id,
                'nomor_kontrak' => $inv->nomor_kontrak,
                'tanggal_uang_masuk' => $inv->tanggal_investasi,
                'deposan' => $inv->nama_investor,
                'nominal_deposito' => $inv->nominal_investasi,
                'lama_deposito' => $inv->lama_investasi,
                'bagi_hasil_pa' => $inv->persentase_bagi_hasil,
                'bagi_hasil_nominal' => $inv->nominal_bagi_hasil_yang_didapat ?? 0,
                'bagi_hasil_per_bulan' => $bagiHasilPerBulan,
                'cof_bulan' => $cofBulan,
                'cof_akhir_periode' => $cofAkhirPeriode,
                'status' => $inv->status,
                
                // Data bulanan (Januari - Desember)
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
                
                // Data pengembalian
                'pengembalian_pokok' => $totalPokokDikembalikan,
                'pengembalian_bagi_hasil' => $totalBagiHasilDibayar,
                'sisa_pokok' => $sisaPokok,
                'sisa_bagi_hasil' => $sisaBagiHasil,
                'total_belum_dikembalikan' => $sisaPokok + $sisaBagiHasil,
            ];
        });
        
        return $result;
    }
}

