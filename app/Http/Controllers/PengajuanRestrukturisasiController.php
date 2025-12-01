<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\PengajuanRestrukturisasi;
use App\Models\MasterDebiturDanInvestor;
use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use App\Http\Requests\PengajuanRestrukturisasiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PengajuanRestrukturisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get current logged in user's debitur data if exists
        $debitur = MasterDebiturDanInvestor::where('user_id', Auth::id())
                                            ->where('flagging', 'tidak')
                                            ->where('status', 'active')
                                            ->first();
        
        // Get debitur list for dropdown (if admin)
        $debiturList = MasterDebiturDanInvestor::where('flagging', 'tidak')
                                               ->where('status', 'active')
                                               ->select('id_debitur', 'nama')
                                               ->get();
        
        // Load pengajuan peminjaman untuk dropdown Nomor Kontrak
        $peminjamanList = [];
        if ($debitur) {
            $peminjamanList = PengajuanPeminjaman::where('id_debitur', $debitur->id_debitur)
                ->where('status', 'Dana Sudah Dicairkan')
                ->select('id_pengajuan_peminjaman', 'nomor_peminjaman', 'jenis_pembiayaan', 'total_pinjaman')
                ->get();
        }
        
        return view('livewire.pengajuan-restrukturisasi.index', compact('debitur', 'debiturList', 'peminjamanList'));
    }

    /**
     * Get pengajuan peminjaman data for dropdown
     */
    public function getPeminjamanList($idDebitur)
    {
        try {
            $peminjamanList = PengajuanPeminjaman::where('id_debitur', $idDebitur)
                ->where('status', 'Dana Sudah Dicairkan')
                ->select('id_pengajuan_peminjaman', 'nomor_peminjaman', 'jenis_pembiayaan', 'total_pinjaman')
                ->get();
            
            return Response::success($peminjamanList, 'Data peminjaman berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data peminjaman');
        }
    }

    /**
     * Get pengajuan peminjaman detail for auto-fill
     */
    public function getPengajuanDetail($id)
    {
        try {
            $pengajuan = PengajuanPeminjaman::findOrFail($id);
            
            // Ambil data pengembalian terakhir untuk menghitung sisa pokok
            $pengembalianTerakhir = PengembalianPinjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $sisaPokokBelumDibayar = $pengembalianTerakhir 
                ? $pengembalianTerakhir->sisa_bayar_pokok 
                : $pengajuan->total_pinjaman;
            
            return Response::success([
                'jenis_pembiayaan' => $pengajuan->jenis_pembiayaan,
                'jumlah_plafon_awal' => $pengajuan->total_pinjaman,
                'sisa_pokok_belum_dibayar' => $sisaPokokBelumDibayar,
            ], 'Data pengajuan berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data pengajuan');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PengajuanRestrukturisasiRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Handle file uploads
            $documentFields = [
                'dokumen_ktp_pic',
                'dokumen_npwp_perusahaan',
                'dokumen_laporan_keuangan',
                'dokumen_arus_kas',
                'dokumen_kondisi_eksternal',
                'dokumen_kontrak_pembiayaan',
                'dokumen_lainnya',
                'dokumen_tanda_tangan',
            ];

            foreach ($documentFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = time() . '_' . $field . '_' . $file->getClientOriginalName();
                    $validated[$field] = $file->storeAs('restrukturisasi/dokumen', $filename, 'public');
                }
            }

            // Convert jenis_restrukturisasi array to JSON
            if (isset($validated['jenis_restrukturisasi']) && is_array($validated['jenis_restrukturisasi'])) {
                $validated['jenis_restrukturisasi'] = $validated['jenis_restrukturisasi'];
            }

            // Set default status
            $validated['status'] = 'Draft';

            // Create pengajuan restrukturisasi
            $pengajuan = PengajuanRestrukturisasi::create($validated);

            DB::commit();

            return Response::success($pengajuan, 'Pengajuan restrukturisasi berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal membuat pengajuan restrukturisasi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $pengajuan = PengajuanRestrukturisasi::with(['debitur', 'pengajuanPeminjaman'])
                                                 ->findOrFail($id);
            
            return view('livewire.pengajuan-restrukturisasi.detail', compact('pengajuan'));
        } catch (\Exception $e) {
            abort(404, 'Pengajuan restrukturisasi tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $pengajuan = PengajuanRestrukturisasi::findOrFail($id);
            return Response::success($pengajuan, 'Data pengajuan restrukturisasi berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal mengambil data');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PengajuanRestrukturisasiRequest $request, $id)
    {
        try {
            $pengajuan = PengajuanRestrukturisasi::findOrFail($id);
            
            DB::beginTransaction();

            $validated = $request->validated();

            // Handle file uploads
            $documentFields = [
                'dokumen_ktp_pic',
                'dokumen_npwp_perusahaan',
                'dokumen_laporan_keuangan',
                'dokumen_arus_kas',
                'dokumen_kondisi_eksternal',
                'dokumen_kontrak_pembiayaan',
                'dokumen_lainnya',
                'dokumen_tanda_tangan',
            ];

            foreach ($documentFields as $field) {
                if ($request->hasFile($field)) {
                    // Delete old file if exists
                    if ($pengajuan->$field) {
                        Storage::disk('public')->delete($pengajuan->$field);
                    }

                    $file = $request->file($field);
                    $filename = time() . '_' . $field . '_' . $file->getClientOriginalName();
                    $validated[$field] = $file->storeAs('restrukturisasi/dokumen', $filename, 'public');
                }
            }

            // Convert jenis_restrukturisasi array to JSON
            if (isset($validated['jenis_restrukturisasi']) && is_array($validated['jenis_restrukturisasi'])) {
                $validated['jenis_restrukturisasi'] = $validated['jenis_restrukturisasi'];
            }

            $pengajuan->update($validated);

            DB::commit();

            return Response::success($pengajuan, 'Pengajuan restrukturisasi berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal memperbarui pengajuan restrukturisasi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $pengajuan = PengajuanRestrukturisasi::findOrFail($id);

            // Delete all document files if exist
            $documentFields = [
                'dokumen_ktp_pic',
                'dokumen_npwp_perusahaan',
                'dokumen_laporan_keuangan',
                'dokumen_arus_kas',
                'dokumen_kondisi_eksternal',
                'dokumen_kontrak_pembiayaan',
                'dokumen_lainnya',
                'dokumen_tanda_tangan',
            ];

            foreach ($documentFields as $field) {
                if ($pengajuan->$field) {
                    Storage::disk('public')->delete($pengajuan->$field);
                }
            }

            $pengajuan->delete();

            return Response::success(null, 'Pengajuan restrukturisasi berhasil dihapus!');
        } catch (\Exception $e) {
            return Response::errorCatch($e, 'Gagal menghapus pengajuan restrukturisasi');
        }
    }
}
