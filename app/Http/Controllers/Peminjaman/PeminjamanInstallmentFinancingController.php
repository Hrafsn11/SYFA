<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeminjamanInstallmentFinancing;
use App\Models\InstallmentFinancing;
use Illuminate\Support\Facades\Storage;
use App\Services\PeminjamanNumberService;

class PeminjamanInstallmentFinancingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_debitur' => 'required|integer',
            'total_pinjaman' => 'required|numeric',
            'tenor_pembayaran' => 'required|in:3,6,9,12',
            'persentase_bagi_hasil' => 'nullable|numeric',
            'pps' => 'nullable|numeric',
            'sfinance' => 'nullable|numeric',
            'total_pembayaran' => 'nullable|numeric',
            'yang_harus_dibayarkan' => 'nullable|numeric',
            'details' => 'required|array|min:1',
        ]);

        // enforce numeric total pinjaman and compute derived fields
        $totalPinjaman = (float) $validated['total_pinjaman'];
        $tenor = (int) $validated['tenor_pembayaran'];

        // Business rules: bagi hasil = 10% of total pinjaman
        $persentaseBagi = 10.0000; // stored as percent value (10.0000 => 10%)
        $totalBagiHasil = round($totalPinjaman * ($persentaseBagi / 100), 2);

        // PPS = 40% of bagi hasil, S Finance = 60% of bagi hasil
        $ppsAmount = round($totalBagiHasil * 0.40, 2);
        $sfinanceAmount = round($totalBagiHasil * 0.60, 2);

        $totalPembayaran = round($totalPinjaman + $totalBagiHasil, 2);
        $monthlyPay = $tenor > 0 ? round($totalPembayaran / $tenor, 2) : $totalPembayaran;

        $numberService = new PeminjamanNumberService();

        $header = PeminjamanInstallmentFinancing::create([
            'id_debitur' => $validated['id_debitur'],
            'nama_bank' => $request->input('nama_bank'),
            'no_rekening' => $request->input('no_rekening'),
            'nama_rekening' => $request->input('nama_rekening'),
            'total_pinjaman' => $totalPinjaman,
            'tenor_pembayaran' => $validated['tenor_pembayaran'],
            'persentase_bagi_hasil' => $persentaseBagi,
            'pps' => $ppsAmount,
            'sfinance' => $sfinanceAmount,
            'total_pembayaran' => $totalPembayaran,
            'yang_harus_dibayarkan' => $monthlyPay,
            'catatan_lainnya' => $request->input('catatan_lainnya'),
            'created_by' => auth()->id() ?? null,
        ]);
        // generate nomor based on header id
        $header->nomor_peminjaman = $numberService->generateFromId($header->id_installment, 'INS', $header->created_at?->format('Ym'));
        $header->save();

        $details = $request->input('details', []);
        foreach ($details as $i => $d) {
            $detailData = [
                'id_installment' => $header->id_installment,
                'no_invoice' => $d['no_invoice'] ?? null,
                'nama_client' => $d['nama_client'] ?? null,
                'nilai_invoice' => $d['nilai_invoice'] ?? 0,
                'invoice_date' => $d['invoice_date'] ?? null,
                'nama_barang' => $d['nama_barang'] ?? null,
            ];

            // handle files with keys like details.0.dokumen_invoice
            if ($request->hasFile("details.$i.dokumen_invoice")) {
                $file = $request->file("details.$i.dokumen_invoice");
                $path = $file->store("installments/{$header->id_installment}", 'public');
                $detailData['dokumen_invoice'] = $path;
            }
            if ($request->hasFile("details.$i.dokumen_lainnya")) {
                $file = $request->file("details.$i.dokumen_lainnya");
                $path = $file->store("installments/{$header->id_installment}", 'public');
                $detailData['dokumen_lainnya'] = $path;
            }

            InstallmentFinancing::create($detailData);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'id' => $header->id_installment]);
        }

        return redirect()->route('peminjaman.detail', ['id' => $header->id_installment, 'type' => 'installment'])
            ->with('success', 'Peminjaman installment berhasil dibuat');
    }

    public function show($id)
    {
        $header = PeminjamanInstallmentFinancing::with('details')->findOrFail($id);
        return view('livewire.peminjaman.detail-installment', compact('header'));
    }

    public function update(Request $request, $id)
    {
        $header = PeminjamanInstallmentFinancing::findOrFail($id);

        $validated = $request->validate([
            'total_pinjaman' => 'required|numeric',
            'tenor_pembayaran' => 'required|in:3,6,9,12',
            'details' => 'required|array|min:1',
        ]);

        // Recompute derived fields on update as well
        $totalPinjaman = (float) $validated['total_pinjaman'];
        $tenor = (int) $validated['tenor_pembayaran'];
        $persentaseBagi = 10.0000;
        $totalBagiHasil = round($totalPinjaman * ($persentaseBagi / 100), 2);
        $ppsAmount = round($totalBagiHasil * 0.40, 2);
        $sfinanceAmount = round($totalBagiHasil * 0.60, 2);
        $totalPembayaran = round($totalPinjaman + $totalBagiHasil, 2);
        $monthlyPay = $tenor > 0 ? round($totalPembayaran / $tenor, 2) : $totalPembayaran;

        $header->update([
            'total_pinjaman' => $totalPinjaman,
            'tenor_pembayaran' => $validated['tenor_pembayaran'],
            'persentase_bagi_hasil' => $persentaseBagi,
            'pps' => $ppsAmount,
            'sfinance' => $sfinanceAmount,
            'total_pembayaran' => $totalPembayaran,
            'yang_harus_dibayarkan' => $monthlyPay,
            'catatan_lainnya' => $request->input('catatan_lainnya'),
            'updated_by' => auth()->id() ?? null,
        ]);

        // delete existing details and recreate (simple approach)
        $header->details()->delete();

        $details = $request->input('details', []);
        foreach ($details as $i => $d) {
            $detailData = [
                'id_installment' => $header->id_installment,
                'no_invoice' => $d['no_invoice'] ?? null,
                'nama_client' => $d['nama_client'] ?? null,
                'nilai_invoice' => $d['nilai_invoice'] ?? 0,
                'invoice_date' => $d['invoice_date'] ?? null,
                'nama_barang' => $d['nama_barang'] ?? null,
            ];

            if ($request->hasFile("details.$i.dokumen_invoice")) {
                $file = $request->file("details.$i.dokumen_invoice");
                $path = $file->store("installments/{$header->id_installment}", 'public');
                $detailData['dokumen_invoice'] = $path;
            }
            if ($request->hasFile("details.$i.dokumen_lainnya")) {
                $file = $request->file("details.$i.dokumen_lainnya");
                $path = $file->store("installments/{$header->id_installment}", 'public');
                $detailData['dokumen_lainnya'] = $path;
            }

            InstallmentFinancing::create($detailData);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'id' => $header->id_installment]);
        }

        return redirect()->back()->with('success', 'Peminjaman installment diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $header = PeminjamanInstallmentFinancing::findOrFail($id);
        $header->delete();
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman installment dihapus');
    }
}
