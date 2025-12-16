<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Http\Requests\SFinlog\PengembalianInvestasiFinlogRequest;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\PengembalianInvestasiFinlog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengembalianInvestasiController extends Controller
{
    public function index()
    {
        return view('livewire.sfinlog.pengembalian-investasi-sfinlog.index');
    }

    /**
     * Store pengembalian investasi untuk SFinlog
     */
    public function store(PengembalianInvestasiFinlogRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            // Catatan: untuk SFinlog kita mapping langsung ke pengajuan_investasi_finlog
            $pengajuan = PengajuanInvestasiFinlog::findOrFail($validated['id_pengajuan_investasi_finlog']);

            $file = null;
            if ($request->bukti_transfer) {
                $file = Storage::disk('public')->put('bukti_pengembalian_investasi_finlog', $request->bukti_transfer);
            }

            $pengembalian = PengembalianInvestasiFinlog::create([
                'id_pengajuan_investasi_finlog' => $pengajuan->id_pengajuan_investasi_finlog,
                'dana_pokok_dibayar' => $validated['dana_pokok_dibayar'] ?? 0,
                'bagi_hasil_dibayar' => $validated['bagi_hasil_dibayar'] ?? 0,
                'bukti_transfer' => $file,
                'tanggal_pengembalian' => $validated['tanggal_pengembalian'],
                'created_by' => auth()->id(),
            ]);

            // Untuk sementara: belum ada kolom sisa_pokok / sisa_bagi_hasil di tabel finlog,
            // jadi di sini belum ada update ke pengajuan_investasi_finlog.

            DB::commit();

            return Response::success($pengembalian, 'Data pengembalian investasi SFinlog berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }
}