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

            $fileFields = [
                'dokumen_mitra',
                'form_new_customer',
                'dokumen_kerja_sama',
                'dokumen_npa',
                'akta_perusahaan',
                'ktp_owner',
                'ktp_pic',
                'surat_izin_usaha'
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = time() . '_' . $field . '_' . $file->getClientOriginalName();
                    $data[$field] = $file->storeAs('peminjaman_finlog', $fileName, 'public');
                }
            }

            $yearMonth = date('Ym'); 
            $lastPeminjaman = PeminjamanFinlog::where('nomor_peminjaman', 'LIKE', "PMJ-{$yearMonth}-SLOG-%")
                ->orderBy('nomor_peminjaman', 'desc')
                ->first();

            if ($lastPeminjaman) {
                $lastNumber = (int) substr($lastPeminjaman->nomor_peminjaman, -2);
                $runningText = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
            } else {
                $runningText = '01';
            }

            $data['nomor_peminjaman'] = "PMJ-{$yearMonth}-SLOG-{$runningText}";

            $peminjaman = PeminjamanFinlog::create($data);

            DB::commit();

            return Response::success($peminjaman, 'Pengajuan peminjaman berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::error(null, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(PeminjamanRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $peminjaman = PeminjamanFinlog::findOrFail($id);

            if ($peminjaman->status !== 'Draft') {
                return Response::error('Peminjaman tidak dapat diubah setelah disubmit');
            }

            $data = $request->validated();

            $fileFields = [
                'dokumen_mitra',
                'form_new_customer',
                'dokumen_kerja_sama',
                'dokumen_npa',
                'akta_perusahaan',
                'ktp_owner',
                'ktp_pic',
                'surat_izin_usaha'
            ];

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

            return Response::success($peminjaman, 'Peminjaman berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::error(null, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $peminjaman = PeminjamanFinlog::findOrFail($id);

            if ($peminjaman->status !== 'Draft') {
                return Response::error('Peminjaman hanya dapat dihapus jika masih berstatus Draft');
            }

            $fileFields = [
                'dokumen_mitra',
                'form_new_customer',
                'dokumen_kerja_sama',
                'dokumen_npa',
                'akta_perusahaan',
                'ktp_owner',
                'ktp_pic',
                'surat_izin_usaha'
            ];

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

    public function showKontrak($id)
    {
        $peminjaman = PeminjamanFinlog::with(['debitur', 'cellsProject'])->findOrFail($id);
        $data = $this->prepareContractData($peminjaman);

        return view('livewire.sfinlog.peminjaman.partials.show-kontrak', compact('peminjaman', 'data'));
    }

    public function downloadKontrakPdf($id)
    {
        $peminjaman = PeminjamanFinlog::with(['debitur', 'cellsProject'])->findOrFail($id);
        $data = $this->prepareContractData($peminjaman);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.sfinlog.peminjaman.partials.show-kontrak-pdf', compact('peminjaman', 'data'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Kontrak-Pembiayaan-' . $peminjaman->nomor_kontrak . '.pdf');
    }

    private function prepareContractData($peminjaman)
    {
        return [
            'nomor_kontrak' => $peminjaman->nomor_kontrak,
            'tanggal_kontrak' => now()->toDateString(),

            // Principal / Cells
            'nama_principal' => $peminjaman->cellsProject->nama_cells_bisnis ?? '-',
            'nama_pic' => $peminjaman->cellsProject->nama_pic ?? '-',
            'alamat_principal' => $peminjaman->cellsProject->alamat ?? '-',
            'deskripsi_bidang' => $peminjaman->cellsProject->deskripsi_bidang ?? '-',

            // Debitur
            'nama_perusahaan' => $peminjaman->debitur->nama ?? '-',
            'nama_ceo' => $peminjaman->debitur->nama_ceo ?? '-',
            'alamat_perusahaan' => $peminjaman->debitur->alamat ?? '-',

            // Details
            'tujuan_pembiayaan' => $peminjaman->nama_project ?? '-',
            'nilai_pembiayaan' => $peminjaman->nilai_pinjaman ?? 0,
            'tenor_pembiayaan' => $peminjaman->durasi_project ?? 0,
            'biaya_administrasi' => $peminjaman->biaya_administrasi ?? 0,
            'bagi_hasil' => $peminjaman->nilai_bagi_hasil ?? 0,
            'persentase_bagi_hasil' => $peminjaman->presentase_bagi_hasil ?? 0,
            'jaminan' => $peminjaman->jaminan ?? '-',

            // Dates
            'tanggal_pencairan' => $peminjaman->harapan_tanggal_pencairan,
            'tanggal_pengembalian' => $peminjaman->rencana_tgl_pengembalian,
        ];
    }

    /**
     * Download Certificate for Peminjaman Finlog
     * 
     * Method ini digunakan untuk generate dan download sertifikat peminjaman
     * Hanya bisa diakses jika status peminjaman = "Selesai"
     * 
     * @param int $id - ID peminjaman finlog
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function downloadSertifikat($id)
    {
        try {
            // 1. Ambil data peminjaman dengan relasi debitur
            $peminjaman = PeminjamanFinlog::with('debitur')->findOrFail($id);
            
            // 2. Validasi: Hanya status "Selesai" yang bisa cetak sertifikat
            if ($peminjaman->status !== 'Selesai') {
                return redirect()->back()->with('error', 
                    'Sertifikat hanya tersedia untuk peminjaman yang sudah selesai');
            }

            // 3. Generate Nomor Sertifikat
            // Format: DCF (Deposito Certificate Finglog) + TAHUN + NOMOR URUT 4 DIGIT
            // Contoh: DCF20250001, DCF20250002, dst.
            $year = date('Y');
            $countThisYear = PeminjamanFinlog::whereYear('created_at', $year)
                ->where('status', 'Selesai')
                ->where('id', '<=', $peminjaman->id)
                ->count();
            
            $nomorSertifikat = 'DCF' . $year . str_pad($countThisYear, 4, '0', STR_PAD_LEFT);

            // 4. Tentukan deskripsi
            $deskripsi = 'PEMINJAMAN FINLOG';

            // 5. Hitung jangka waktu peminjaman
            // Dari harapan_tanggal_pencairan sampai rencana_tgl_pengembalian
            $tanggalPencairanCarbon = \Carbon\Carbon::parse($peminjaman->harapan_tanggal_pencairan);
            $tanggalPengembalianCarbon = \Carbon\Carbon::parse($peminjaman->rencana_tgl_pengembalian);

            // 6. Format tanggal untuk ditampilkan (bahasa Indonesia)
            $tanggalPencairan = $tanggalPencairanCarbon->translatedFormat('d F Y');
            $tanggalPengembalian = $tanggalPengembalianCarbon->translatedFormat('d F Y');
            $jangkaWaktu = $tanggalPencairan . ' - ' . $tanggalPengembalian;

            // 7. Siapkan data untuk view sertifikat
            $data = [
                // Nama peminjam (dari nama perusahaan debitur)
                'nama_peminjam' => $peminjaman->debitur->nama ?? $peminjaman->nama_perusahaan ?? '-',
                
                // Nomor sertifikat yang sudah digenerate
                'nomor_sertifikat' => $nomorSertifikat,
                
                // Deskripsi jenis peminjaman
                'deskripsi' => $deskripsi,
                
                // Nilai pinjaman (formatted Rupiah tanpa desimal)
                'nilai_pinjaman' => 'Rp ' . number_format($peminjaman->nilai_pinjaman, 0, ',', '.'),
                
                // Kode transaksi (nomor peminjaman)
                'kode_transaksi' => $peminjaman->nomor_peminjaman,
                
                // Jangka waktu (range tanggal)
                'jangka_waktu' => $jangkaWaktu,
                
                // Bagi hasil (persentase per tahun)
                'bagi_hasil' => $peminjaman->presentase_bagi_hasil . ' % P.A NET',
                
                // Nilai pinjaman dalam text (formatted Rupiah dengan 2 desimal)
                // Digunakan di halaman 2 sertifikat
                'nilai_pinjaman_text' => 'Rp. ' . number_format($peminjaman->nilai_pinjaman, 2, ',', '.'),
            ];

            // 8. Return view sertifikat
            return view('livewire.sfinlog.peminjaman.sertifikat', compact('data'));
            
        } catch (\Exception $e) {
            // Handle error dan redirect kembali dengan pesan error
            return redirect()->back()->with('error', 
                'Gagal menggenerate sertifikat: ' . $e->getMessage());
        }
    }

}
