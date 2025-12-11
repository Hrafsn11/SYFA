<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPeminjaman;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of peminjaman for SFinlog module
     */
    public function index()
    {
        // TODO: Implementasi logika query khusus SFinlog
        // Saat ini menggunakan query dasar, bisa ditambahkan filter khusus SFinlog nanti
        $peminjamanRecords = PengajuanPeminjaman::with(['debitur.kol'])->get();

        $peminjaman_data = $peminjamanRecords->map(function($r) {
            return [
                'id' => $r->id_pengajuan_peminjaman,
                'type' => $r->jenis_pembiayaan ?? 'peminjaman',
                'nomor_peminjaman' => $r->nomor_peminjaman ?? null,
                'nama_perusahaan' => $r->debitur?->nama_debitur ?? '',
                'lampiran_sid' => $r->lampiran_sid,
                'nilai_kol' => $r->debitur?->kol->kol ?? '',
                'status' => $r->status ?? 'draft',
            ];
        })->toArray();

        return view('livewire.sfinlog.peminjaman.index', compact('peminjaman_data'));
    }

    /**
     * Show the form for creating a new peminjaman for SFinlog
     */
    public function create()
    {
        // TODO: Implementasi logika form create khusus SFinlog
        return view('livewire.sfinlog.peminjaman.create');
    }

    /**
     * Display the specified peminjaman detail for SFinlog
     */
    public function show(Request $request, $id)
    {
        // TODO: Implementasi logika detail khusus SFinlog
        $header = PengajuanPeminjaman::with(['debitur.kol', 'instansi', 'buktiPeminjaman'])->find($id);
        
        if (!$header) abort(404);
        
        // TODO: Implementasi struktur data khusus SFinlog
        $peminjaman = [
            'id' => $header->id_pengajuan_peminjaman,
            'nomor_peminjaman' => $header->nomor_peminjaman,
            // ... data lainnya akan ditambahkan sesuai kebutuhan SFinlog
        ];

        return view('livewire.sfinlog.peminjaman.detail', compact('peminjaman'));
    }

    /**
     * Show the form for editing the specified peminjaman for SFinlog
     */
    public function edit($id)
    {
        // TODO: Implementasi logika form edit khusus SFinlog
        return view('livewire.sfinlog.peminjaman.edit', compact('id'));
    }

    /**
     * Store a newly created peminjaman for SFinlog
     */
    public function store(Request $request)
    {
        // TODO: Implementasi logika store khusus SFinlog
        return redirect()->route('sfinlog.peminjaman.index');
    }

    /**
     * Update the specified peminjaman for SFinlog
     */
    public function update(Request $request, $id)
    {
        // TODO: Implementasi logika update khusus SFinlog
        return redirect()->route('sfinlog.peminjaman.show', $id);
    }

    /**
     * Preview kontrak for SFinlog
     */
    public function previewKontrak(Request $request, $id)
    {
        // TODO: Implementasi logika preview kontrak khusus SFinlog
        return view('livewire.sfinlog.peminjaman.preview-kontrak', compact('id'));
    }

    /**
     * Download kontrak for SFinlog
     */
    public function downloadKontrak(Request $request, $id)
    {
        // TODO: Implementasi logika download kontrak khusus SFinlog
        return response()->json(['message' => 'Download kontrak untuk SFinlog']);
    }

    /**
     * Approval peminjaman for SFinlog
     */
    public function approval(Request $request, $id)
    {
        // TODO: Implementasi logika approval khusus SFinlog
        return redirect()->back();
    }

    /**
     * Get history detail for SFinlog
     */
    public function getHistoryDetail($historyId)
    {
        // TODO: Implementasi logika history detail khusus SFinlog
        return response()->json(['data' => []]);
    }

    /**
     * Toggle active status for SFinlog
     */
    public function toggleActive($id)
    {
        // TODO: Implementasi logika toggle active khusus SFinlog
        return redirect()->back();
    }
}

