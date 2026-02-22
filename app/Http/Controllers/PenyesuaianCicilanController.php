<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\PengajuanCicilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyesuaianCicilanController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:penyesuaian_cicilan.add')->only(['create', 'store']);
    }

    /**
     * Display the form for creating a new program restrukturisasi
     */
    public function create()
    {
        $approvedRestrukturisasi = PengajuanCicilan::with('debitur')
            ->where('status', 'Selesai')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('penyesuaian-cicilan.create', compact('approvedRestrukturisasi'));
    }

    /**
     * Get approved restrukturisasi data via AJAX
     */
    public function getApprovedCicilan()
    {
        try {
            $data = PengajuanCicilan::with('debitur')
                ->where('status', 'Selesai')
                ->select([
                    'id_pengajuan_cicilan',
                    'id_debitur',
                    'nomor_kontrak_pembiayaan as nomor_kontrak',
                    'sisa_pokok_belum_dibayar as sisa_pokok',
                    'nama_perusahaan as nama_debitur'
                ])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $namaDebitur = $item->debitur ? $item->debitur->nama : $item->nama_debitur;

                    return [
                        'id' => $item->id_pengajuan_cicilan,
                        'nama_debitur' => $namaDebitur,
                        'nomor_kontrak' => $item->nomor_kontrak,
                        'sisa_pokok' => (float) $item->sisa_pokok,
                    ];
                });

            return Response::success($data, 'Data restrukturisasi approved berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data restrukturisasi');
        }
    }

    /**
     * Get detail of specific restrukturisasi
     */
    public function getCicilanDetail($id)
    {
        try {
            $restrukturisasi = PengajuanCicilan::with('debitur')
                ->where('id_pengajuan_cicilan', $id)
                ->where('status', 'Selesai')
                ->firstOrFail();

            $namaDebitur = $restrukturisasi->debitur ? $restrukturisasi->debitur->nama : $restrukturisasi->nama_perusahaan;

            $data = [
                'id' => $restrukturisasi->id_pengajuan_cicilan,
                'nama_debitur' => $namaDebitur,
                'nomor_kontrak' => $restrukturisasi->nomor_kontrak_pembiayaan,
                'sisa_pokok' => (float) $restrukturisasi->sisa_pokok_belum_dibayar,
            ];

            return Response::success($data, 'Data restrukturisasi berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data restrukturisasi');
        }
    }

    /**
     * Store the program restrukturisasi
     */
    public function store(Request $request)
    {
        // Decode jadwal_angsuran from JSON string to array
        $jadwalAngsuran = $request->input('jadwal_angsuran');
        if (is_string($jadwalAngsuran)) {
            $jadwalAngsuran = json_decode($jadwalAngsuran, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return Response::error('Format jadwal angsuran tidak valid', 400);
            }
        }

        // Merge decoded array back to request
        $request->merge(['jadwal_angsuran' => $jadwalAngsuran]);

        $validated = $request->validate([
            'id_pengajuan_cicilan' => 'required|exists:pengajuan_cicilan,id_pengajuan_cicilan',
            'metode_perhitungan' => 'required|in:Flat,Anuitas',
            'plafon_pembiayaan' => 'required|numeric|min:0',
            'suku_bunga_per_tahun' => 'required|numeric|min:0|max:100',
            'jangka_waktu_total' => 'required|integer|min:1',
            'masa_tenggang' => 'required|integer|min:0',
            'tanggal_mulai_cicilan' => 'required|date',
            'jadwal_angsuran' => 'required|array|min:1',
            'jadwal_angsuran.*.no' => 'required|integer',
            'jadwal_angsuran.*.tanggal_jatuh_tempo' => 'required|string',
            'jadwal_angsuran.*.pokok' => 'required|numeric|min:0',
            'jadwal_angsuran.*.margin' => 'required|numeric|min:0',
            'jadwal_angsuran.*.total_cicilan' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            DB::commit();

            return Response::success([
                'data' => $validated,
                'message' => 'Program restrukturisasi berhasil disimpan'
            ], 'Program restrukturisasi berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal menyimpan program restrukturisasi');
        }
    }
}
