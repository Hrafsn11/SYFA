<?php

namespace App\Http\Controllers\SFinlog;

use App\Helpers\Response;
use App\Helpers\ListNotifSFinlog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengembalianPinjamanFinlog;
use App\Models\PeminjamanFinlog;
use App\Http\Requests\SFinlog\PengembalianPinjamanFinlogRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PengembalianPinjamanController extends Controller
{
    /**
     * Constructor - Middleware untuk permission
     */
    public function __construct()
    {
        // Store hanya bisa dilakukan oleh Debitur
        $this->middleware('permission:pengembalian_pinjaman_finlog.add')->only(['store']);
    }

    public function store(PengembalianPinjamanFinlogRequest $request)
    {
        try {
            DB::beginTransaction();

            $id_peminjaman_finlog = $request->input('id_pinjaman_finlog');

            $listPengembalian = $request->input('pengembalian_list', []);


            foreach ($listPengembalian as $index => $item) {

                $path = null;
                if (isset($item['bukti_file']) && $item['bukti_file'] instanceof \Illuminate\Http\UploadedFile) {
                    $filename = 'pengembalian_' . time() . '_' . uniqid() . '.' . $item['bukti_file']->extension();
                    $path = $item['bukti_file']->storeAs('pengembalian_finlog', $filename, 'public');
                }

                $nominal = (float) ($item['nominal'] ?? 0);

                PengembalianPinjamanFinlog::create([
                    'id_pinjaman_finlog' => $id_peminjaman_finlog,
                    'id_cells_project' => $item['id_cells_project'] ?? null,
                    'id_project' => $item['id_project'] ?? null,
                    'jumlah_pengembalian' => $nominal,
                    'sisa_pinjaman' => $item['sisa_pinjaman'] ?? 0,
                    'sisa_bagi_hasil' => $item['sisa_bagi_hasil'] ?? 0,
                    'total_sisa_pinjaman' => $item['total_sisa_pinjaman'] ?? 0,
                    'tanggal_pengembalian' => now(),
                    'bukti_pembayaran' => $path,
                    'jatuh_tempo' => $item['jatuh_tempo'] ?? null,
                    'catatan' => $item['catatan'] ?? null,
                    'status' => $item['status'] ?? 'Belum Lunas',
                ]);
            }

            DB::commit();

            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $peminjaman = PeminjamanFinlog::with('debitur')->findOrFail($id_peminjaman_finlog);

            // Kirim notifikasi pengembalian dana
            ListNotifSFinlog::pengembalianDana($peminjaman);

            app(\App\Services\ArPerbulanFinlogService::class)->updateAROnPengembalian(
                $id_peminjaman_finlog,
                now()
            );

            return Response::success([
                'redirect' => route('sfinlog.pengembalian-pinjaman.index')
            ], 'Semua data pengembalian berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }
}
