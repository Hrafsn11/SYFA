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
            
            // Handle file uploads
            // Files from Livewire are already stored as path strings in setterFormData()
            // Files from direct form upload will be handled here
            $fileFields = ['dokumen_mitra', 'form_new_customer', 'dokumen_kerja_sama', 'dokumen_npa', 
                          'akta_perusahaan', 'ktp_owner', 'ktp_pic', 'surat_izin_usaha'];
            
            foreach ($fileFields as $field) {
                // Check if it's a file object (from direct form upload)
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = time() . '_' . $field . '_' . $file->getClientOriginalName();
                    $data[$field] = $file->storeAs('peminjaman_finlog', $fileName, 'public');
                }
                // If it's already a string path from Livewire, keep it as is
                // The validation already passed, so it's valid
            }
            
            // Generate nomor peminjaman
            $lastPeminjaman = PeminjamanFinlog::whereYear('created_at', date('Y'))
                                             ->whereMonth('created_at', date('m'))
                                             ->count();
            $data['nomor_peminjaman'] = 'PFL/' . date('Ym') . '/' . str_pad($lastPeminjaman + 1, 4, '0', STR_PAD_LEFT);
            
            $peminjaman = PeminjamanFinlog::create($data);
            
            DB::commit();
            
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
            
            // Handle file uploads
            $fileFields = ['dokumen_mitra', 'form_new_customer', 'dokumen_kerja_sama', 'dokumen_npa', 
                          'akta_perusahaan', 'ktp_owner', 'ktp_pic', 'surat_izin_usaha'];
            
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    // Delete old file if exists
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

            // Delete all uploaded files
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
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $peminjaman = PeminjamanFinlog::with(['debitur', 'cellsProject'])->select('peminjaman_finlog.*');
            
            return DataTables::of($peminjaman)
                ->addIndexColumn()
                ->editColumn('nama_project', function($row) {
                    return $row->nama_project ?? '-';
                })
                ->editColumn('harapan_tanggal_pencairan', function($row) {
                    return $row->harapan_tanggal_pencairan ? $row->harapan_tanggal_pencairan->format('d/m/Y') : '-';
                })
                ->editColumn('durasi_project', function($row) {
                    return $row->durasi_project . ' bulan';
                })
                ->editColumn('nilai_pinjaman', function($row) {
                    return 'Rp ' . number_format($row->nilai_pinjaman, 0, ',', '.');
                })
                ->editColumn('presentase_bagi_hasil', function($row) {
                    return $row->presentase_bagi_hasil . '%';
                })
                ->editColumn('nilai_bagi_hasil', function($row) {
                    return 'Rp ' . number_format($row->nilai_bagi_hasil, 0, ',', '.');
                })
                ->editColumn('status', function($row) {
                    $badges = [
                        'Draft' => 'secondary',
                        'Menunggu Persetujuan' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        'Dicairkan' => 'info',
                        'Selesai' => 'primary'
                    ];
                    $badge = $badges[$row->status] ?? 'secondary';
                    return '<span class="badge bg-'.$badge.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<button type="button" class="btn btn-sm btn-info" onclick="viewDetail(\''.$row->id_peminjaman_finlog.'\')"><i class="ti ti-eye"></i></button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-warning" onclick="editPeminjaman(\''.$row->id_peminjaman_finlog.'\')"><i class="ti ti-edit"></i></button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger" onclick="deletePeminjaman(\''.$row->id_peminjaman_finlog.'\')"><i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }
}

