<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengembalianPinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('livewire.pengembalian-pinjaman.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pengajuanPeminjaman = PengajuanPeminjaman::where('id_debitur', Auth::id())
            ->where('status', 'Dana Sudah Dicairkan')
            ->with(['buktiPeminjaman:id_bukti_peminjaman,id_pengajuan_peminjaman,no_invoice,nilai_invoice,nilai_pinjaman,nilai_bagi_hasil'])
            ->select('id_pengajuan_peminjaman', 'nomor_peminjaman', 'total_pinjaman', 'total_bagi_hasil', 'harapan_tanggal_pencairan')
            ->get()
            ->map(function ($item) {
                $pengembalianTerakhir = PengembalianPinjaman::where('id_pengajuan_peminjaman', $item->id_pengajuan_peminjaman)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($pengembalianTerakhir) {
                    $item->total_pinjaman = $pengembalianTerakhir->sisa_bayar_pokok;
                    $item->total_bagi_hasil = $pengembalianTerakhir->sisa_bagi_hasil;
                }

                $item->invoices_json = $item->buktiPeminjaman->map(function ($b) {
                    return [
                        'id' => $b->id_bukti_peminjaman,
                        'no_invoice' => $b->no_invoice,
                        'nilai_invoice' => (float) $b->nilai_invoice,
                        'nilai_pinjaman' => (float) $b->nilai_pinjaman,
                        'nilai_bagi_hasil' => (float) $b->nilai_bagi_hasil,
                    ];
                })->toArray();

                return $item;
            });

        $namaPerusahaan = Auth::user()->name;

        return view('livewire.pengembalian-pinjaman.create', compact('pengajuanPeminjaman', 'namaPerusahaan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_peminjaman' => 'required|exists:pengajuan_peminjaman,id_pengajuan_peminjaman',
            'nama_perusahaan' => 'required|string|max:255',
            'total_pinjaman' => 'required|numeric|min:0',
            'total_bagi_hasil' => 'required|numeric|min:0',
            'tanggal_pencairan' => 'required|string',
            'lama_pemakaian' => 'required|integer|min:0',
            'nominal_invoice' => 'required|numeric|min:0',
            'invoice_dibayarkan' => 'required|string|max:255',
            'sisa_utang' => 'required|numeric|min:0',
            'sisa_bagi_hasil' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'pengembalian_invoices.*.nominal' => 'required|numeric|min:1',
            'pengembalian_invoices.*.file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ]);

        $pengembalianInvoices = $request->input('pengembalian_invoices', []);

        if (empty($pengembalianInvoices)) {
            return response()->json(['message' => 'Data pengembalian invoice harus diisi minimal 1 item'], 422);
        }

        \DB::beginTransaction();
        try {
            $pengajuan = PengajuanPeminjaman::findOrFail($validated['kode_peminjaman']);

            $status = 'Belum Lunas';
            if ($validated['sisa_utang'] == 0 && $validated['sisa_bagi_hasil'] == 0) {
                $status = 'Lunas';
            }

            $tanggalPencairan = $validated['tanggal_pencairan'];
            try {
                $tanggalPencairan = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['tanggal_pencairan'])->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    $tanggalPencairan = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['tanggal_pencairan'])->format('Y-m-d');
                } catch (\Exception $e2) {
                    $tanggalPencairan = \Carbon\Carbon::parse($validated['tanggal_pencairan'])->format('Y-m-d');
                }
            }

            $pengembalian = PengembalianPinjaman::create([
                'id_pengajuan_peminjaman' => $validated['kode_peminjaman'],
                'nama_perusahaan' => $validated['nama_perusahaan'],
                'nomor_peminjaman' => $pengajuan->nomor_peminjaman,
                'total_pinjaman' => $validated['total_pinjaman'],
                'total_bagi_hasil' => $validated['total_bagi_hasil'],
                'tanggal_pencairan' => $tanggalPencairan,
                'lama_pemakaian' => $validated['lama_pemakaian'],
                'nominal_invoice' => $validated['nominal_invoice'],
                'invoice_dibayarkan' => $validated['invoice_dibayarkan'],
                'sisa_bayar_pokok' => $validated['sisa_utang'],
                'sisa_bagi_hasil' => $validated['sisa_bagi_hasil'],
                'catatan' => $validated['catatan'],
                'status' => $status,
            ]);

            foreach ($pengembalianInvoices as $index => $item) {
                $filePath = null;

                if ($request->hasFile("pengembalian_invoices.{$index}.file")) {
                    $file = $request->file("pengembalian_invoices.{$index}.file");
                    $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                    $filePath = $file->storeAs('bukti_pembayaran', $fileName, 'public');
                }

                \App\Models\PengembalianInvoice::create([
                    'id_pengembalian' => $pengembalian->id,
                    'nominal_yg_dibayarkan' => $item['nominal'],
                    'bukti_pembayaran' => $filePath,
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pengembalian berhasil disimpan',
                'redirect' => route('pengembalian.index'),
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PengembalianPinjaman $pengembalianPinjaman)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PengembalianPinjaman $pengembalianPinjaman)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PengembalianPinjaman $pengembalianPinjaman)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PengembalianPinjaman $pengembalianPinjaman)
    {
        //
    }
}
