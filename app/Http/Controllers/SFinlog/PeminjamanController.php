<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Models\PeminjamanFinlog;
use App\Http\Requests\SFinlog\PeminjamanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PeminjamanController extends Controller
{
    /**
     * Store a newly created peminjaman for SFinlog
     */
    public function store(PeminjamanRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();

            $fileFields = ['dokumen_mitra', 'form_new_customer', 'dokumen_kerja_sama', 'dokumen_npa', 
                          'akta_perusahaan', 'ktp_owner', 'ktp_pic', 'surat_izin_usaha'];
            
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = time() . '_' . $field . '_' . $file->getClientOriginalName();
                    $data[$field] = $file->storeAs('peminjaman_finlog', $fileName, 'public');
                }
            }
            
            // Generate nomor peminjaman dengan format PMJ-TAHUNBULAN-SLOG-RUNNINGTEXT
            $yearMonth = date('Ym'); // Format: 202512
            $lastPeminjaman = PeminjamanFinlog::where('nomor_peminjaman', 'LIKE', "PMJ-{$yearMonth}-SLOG-%")
                                             ->orderBy('nomor_peminjaman', 'desc')
                                             ->first();
            
            if ($lastPeminjaman) {
                // Ambil running text terakhir dan tambah 1
                $lastNumber = (int) substr($lastPeminjaman->nomor_peminjaman, -2);
                $runningText = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
            } else {
                // Jika belum ada peminjaman di bulan ini, mulai dari 01
                $runningText = '01';
            }
            
            $data['nomor_peminjaman'] = "PMJ-{$yearMonth}-SLOG-{$runningText}";
            
            $peminjaman = PeminjamanFinlog::create($data);
            
            DB::commit();
            
            // Auto-update AR Perbulan
            $this->autoUpdateARPerbulan($peminjaman->id_debitur, $peminjaman->created_at);
            
            return Response::success($peminjaman, 'Pengajuan peminjaman berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::error(null, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified peminjaman for SFinlog
     */
    public function update(PeminjamanRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $peminjaman = PeminjamanFinlog::findOrFail($id);
            
            if ($peminjaman->status !== 'Draft') {
                return Response::error('Peminjaman tidak dapat diubah setelah disubmit');
            }
            
            $data = $request->validated();
            
            $fileFields = ['dokumen_mitra', 'form_new_customer', 'dokumen_kerja_sama', 'dokumen_npa', 
                          'akta_perusahaan', 'ktp_owner', 'ktp_pic', 'surat_izin_usaha'];
            
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    if ($peminjaman->{$field} && \Storage::disk('public')->exists($peminjaman->{$field})) {
                        \Storage::disk('public')->delete($peminjaman->{$field});
                    }
                    
                    $file = $request->file($field);
                    $fileName = time() . '_' . $field . '_' . $file->getClientOriginalName();
                    $data[$field] = $file->storeAs('peminjaman_finlog', $fileName, 'public');
                }
            }
            
            $peminjaman->update($data);
            
            DB::commit();
            
            // Auto-update AR Perbulan
            $this->autoUpdateARPerbulan($peminjaman->id_debitur, $peminjaman->created_at);
            
            return Response::success($peminjaman, 'Peminjaman berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::error(null, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $peminjaman = PeminjamanFinlog::findOrFail($id);
            
            if ($peminjaman->status !== 'Draft') {
                return Response::error('Peminjaman hanya dapat dihapus jika masih berstatus Draft');
            }

            $fileFields = ['dokumen_mitra', 'form_new_customer', 'dokumen_kerja_sama', 'dokumen_npa', 
                          'akta_perusahaan', 'ktp_owner', 'ktp_pic', 'surat_izin_usaha'];
            
            foreach ($fileFields as $field) {
                if ($peminjaman->{$field}) {
                    \Storage::disk('public')->delete($peminjaman->{$field});
                }
            }

            $peminjaman->delete();

            DB::commit();

            return Response::success(null, 'Peminjaman berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::error(null, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update NPA status for current user's debitur
     */
    public function updateNpaStatus(Request $request)
    {
        try {
            $debitur = auth()->user()->debitur;
            
            if (!$debitur) {
                return Response::error('Data debitur tidak ditemukan');
            }
            
            $debitur->update(['npa' => true]);
            
            return Response::success($debitur, 'Status NPA berhasil diperbarui');
        } catch (\Exception $e) {
            return Response::error(null, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show kontrak peminjaman
     */
    public function showKontrak($id)
    {
        $peminjaman = PeminjamanFinlog::with([
            'debitur',
            'cellsProject'
        ])->findOrFail($id);

        // Prepare data untuk kontrak
        $data = [
            'nomor_kontrak' => $peminjaman->nomor_kontrak,
            'tanggal_kontrak' => now()->toDateString(),
            
            // Data Principal (Cells Project)
            'nama_principal' => $peminjaman->cellsProject->nama_cells_bisnis ?? '-',
            'nama_pic' => $peminjaman->cellsProject->nama_pic ?? '-',
            'alamat_principal' => $peminjaman->cellsProject->alamat ?? '-',
            'deskripsi_bidang' => $peminjaman->cellsProject->deskripsi_bidang ?? '-',
            
            // Data Debitur (Perusahaan)
            'nama_perusahaan' => $peminjaman->debitur->nama ?? '-',
            'nama_ceo' => $peminjaman->debitur->nama_ceo ?? '-',
            'alamat_perusahaan' => $peminjaman->debitur->alamat ?? '-',
            
            // Detail Pembiayaan
            'tujuan_pembiayaan' => $peminjaman->nama_project ?? '-',
            'nilai_pembiayaan' => $peminjaman->nilai_pinjaman ?? 0,
            'tenor_pembiayaan' => $peminjaman->durasi_project ?? 0,
            'biaya_administrasi' => $peminjaman->biaya_administrasi ?? 0,
            'bagi_hasil' => $peminjaman->nilai_bagi_hasil ?? 0,
            'persentase_bagi_hasil' => $peminjaman->presentase_bagi_hasil ?? 0,
            'jaminan' => $peminjaman->jaminan ?? '-',
            
            // Tanggal
            'tanggal_pencairan' => $peminjaman->harapan_tanggal_pencairan,
            'tanggal_pengembalian' => $peminjaman->rencana_tgl_pengembalian,
        ];

        return view('livewire.sfinlog.peminjaman.partials.show-kontrak', compact('peminjaman', 'data'));
    }

    /**
     * Auto-update AR Perbulan saat ada perubahan peminjaman
     */
    private function autoUpdateARPerbulan(string $id_debitur, $date): void
    {
        try {
            $bulan = \Carbon\Carbon::parse($date)->format('Y-m');
            
            // Call ArPerbulanController untuk update AR
            $arController = new \App\Http\Controllers\SFinlog\ArPerbulanController();
            $request = new \Illuminate\Http\Request([
                'id_debitur' => $id_debitur,
                'bulan' => $bulan,
            ]);
            
            $arController->updateAR($request);
            
            \Log::info('AR Perbulan auto-updated from PeminjamanController', [
                'id_debitur' => $id_debitur,
                'bulan' => $bulan,
            ]);
        } catch (\Exception $e) {
            // Log error tapi tidak throw exception agar tidak mengganggu flow utama
            \Log::error('Failed to auto-update AR Perbulan', [
                'id_debitur' => $id_debitur,
                'error' => $e->getMessage(),
            ]);
        }
    }
}


