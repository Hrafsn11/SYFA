<?php

namespace App\Http\Controllers\SFinlog;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Models\ArPerbulanFinlog;
use App\Models\MasterDebiturDanInvestor;
use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanTagihanBulananController extends Controller
{
    /**
     * Display AR Perbulan index for SFinlog
     */
    public function index()
    {
        return view('livewire.sfinlog.ar-perbulan.index');
    }

    /**
     * Update atau create AR Perbulan untuk debitur tertentu di bulan tertentu
     */
    public function updateAR(Request $request)
    {
        $request->validate([
            'id_debitur' => 'required|exists:master_debitur_dan_investor,id_debitur',
            'bulan' => 'required|date_format:Y-m',
        ]);

        try {
            DB::beginTransaction();

            $id_debitur = $request->id_debitur;
            $bulan = $request->bulan;

            $debitur = MasterDebiturDanInvestor::find($id_debitur);
            if (!$debitur) {
                return Response::error('Debitur tidak ditemukan');
            }

            $periode = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $year = $periode->year;
            $month = $periode->month;
            $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

            // Hitung total pinjaman yang sudah dicairkan sampai bulan tertentu
            $totalPinjaman = $this->calculateTotalPinjaman($id_debitur, $endOfMonth);

            // Hitung total pengembalian sampai bulan tertentu
            $totalPengembalian = $this->calculateTotalPengembalian($id_debitur, $endOfMonth);

            // Hitung sisa AR
            $sisaArPokok = $totalPinjaman['pokok'] - $totalPengembalian['pokok'];
            $sisaBagiHasil = $totalPinjaman['bagi_hasil'] - $totalPengembalian['bagi_hasil'];
            $sisaArTotal = $sisaArPokok + $sisaBagiHasil;

            // Tentukan status
            $status = $sisaArTotal <= 0 ? 'lunas' : 'active';

            // Hitung jumlah pinjaman yang sudah dicairkan
            $jumlahPinjaman = PeminjamanFinlog::where('id_debitur', $id_debitur)
                ->whereIn('status', ['Approved', 'Disbursed', 'Active'])
                ->whereDate('created_at', '<=', $endOfMonth)
                ->count();

            $arPerbulan = ArPerbulanFinlog::updateOrCreate(
                [
                    'id_debitur' => $id_debitur,
                    'bulan' => $bulan,
                ],
                [
                    'nama_perusahaan' => $debitur->nama,
                    'periode' => $periode->toDateString(),
                    'total_pinjaman_pokok' => $totalPinjaman['pokok'],
                    'total_bagi_hasil' => $totalPinjaman['bagi_hasil'],
                    'total_pengembalian_pokok' => $totalPengembalian['pokok'],
                    'total_pengembalian_bagi_hasil' => $totalPengembalian['bagi_hasil'],
                    'sisa_ar_pokok' => $sisaArPokok,
                    'sisa_bagi_hasil' => $sisaBagiHasil,
                    'sisa_ar_total' => $sisaArTotal,
                    'jumlah_pinjaman' => $jumlahPinjaman,
                    'status' => $status,
                ]
            );

            DB::commit();

            return Response::success($arPerbulan, 'AR Perbulan berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update AR Perbulan Finlog: ' . $e->getMessage());
            return Response::error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hitung total pinjaman yang sudah dicairkan sampai tanggal tertentu
     */
    private function calculateTotalPinjaman(string $id_debitur, Carbon $endDate): array
    {
        $result = PeminjamanFinlog::where('id_debitur', $id_debitur)
            ->whereIn('status', ['Approved', 'Disbursed', 'Active'])
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('
                COALESCE(SUM(nilai_pinjaman), 0) as total_pokok,
                COALESCE(SUM(nilai_bagi_hasil), 0) as total_bagi_hasil
            ')
            ->first();

        return [
            'pokok' => $result->total_pokok ?? 0,
            'bagi_hasil' => $result->total_bagi_hasil ?? 0,
        ];
    }

    /**
     * Hitung total pengembalian yang sudah dibayar sampai tanggal tertentu
     * Rumus: Total Dibayar = Total Pinjaman Awal - Sisa Hutang Terakhir
     */
    private function calculateTotalPengembalian(string $id_debitur, Carbon $endDate): array
    {
        // Ambil semua pengembalian terakhir per pinjaman untuk debitur ini
        $pengembalianTerakhir = PengembalianPinjamanFinlog::whereHas('peminjamanFinlog', function ($query) use ($id_debitur) {
                $query->where('id_debitur', $id_debitur);
            })
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalBayarPokok = 0;
        $totalBayarBagiHasil = 0;

        // Group by id_pinjaman_finlog dan ambil yang terakhir
        foreach ($pengembalianTerakhir->groupBy('id_pinjaman_finlog') as $pinjamanId => $pengembalians) {
            $terakhir = $pengembalians->first();
            $pinjaman = PeminjamanFinlog::find($pinjamanId);
            
            if ($pinjaman) {
                // Hitung yang sudah dibayar
                $totalBayarPokok += ($pinjaman->nilai_pinjaman - $terakhir->sisa_pinjaman);
                $totalBayarBagiHasil += ($pinjaman->nilai_bagi_hasil - $terakhir->sisa_bagi_hasil);
            }
        }

        return [
            'pokok' => $totalBayarPokok,
            'bagi_hasil' => $totalBayarBagiHasil,
        ];
    }
}

