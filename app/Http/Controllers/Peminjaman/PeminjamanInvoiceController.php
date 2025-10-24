<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PeminjamanInvoiceFinancing;
use App\Models\InvoiceFinancing;
use App\Services\PeminjamanNumberService;

class PeminjamanInvoiceController extends Controller
{
    public function store(Request $request)
    {
        // Basic validation for header
        $validator = Validator::make($request->all(), [
            'id_debitur' => 'required|integer|exists:master_debitur_dan_investor,id_debitur',
            'sumber_pembiayaan' => 'required|in:eksternal,internal',
            'invoices' => 'required|string', // JSON
            'sumber_eksternal_id' => 'nullable|integer|exists:master_sumber_pendanaan_eksternal,id_instansi',
            'lampiran_sid' => 'nullable|file|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'errors'=>$validator->errors()], 422);
        }

        $invoices = json_decode($request->input('invoices', '[]'), true);
        if (!is_array($invoices) || count($invoices) === 0) {
            return response()->json(['success'=>false,'message'=>'No invoices provided'], 422);
        }

        DB::beginTransaction();
        try {
            // Normalize header input names (form uses slightly different field names)
            $id_debitur = $request->input('id_debitur') ?? $request->input('master_id') ?? null;
            $id_instansi = $request->input('id_instansi') ?? $request->input('sumber_eksternal_id') ?? null;
            $sumber_pembiayaan = $request->input('sumber_pembiayaan') ?? null;
            $nama_bank = $request->input('nama_bank') ?? null;
            $no_rekening = $request->input('no_rekening') ?? null;
            $nama_rekening = $request->input('nama_rekening') ?? null;
            $tujuan_pembiayaan = $request->input('tujuan_pembiayaan') ?? $request->input('defaultFormControlInput') ?? null;
            $harapan_tanggal_pencairan = $request->input('harapan_tanggal_pencairan') ?? $request->input('tanggal_pencairan') ?? null;
            $rencana_tgl_pembayaran = $request->input('rencana_tgl_pembayaran') ?? $request->input('tanggal_pembayaran') ?? null;

            // Normalize date inputs (allow d/m/Y from form)
            if ($harapan_tanggal_pencairan && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $harapan_tanggal_pencairan)) {
                [$d, $m, $y] = explode('/', $harapan_tanggal_pencairan);
                $harapan_tanggal_pencairan = "$y-$m-$d";
            }
            if ($rencana_tgl_pembayaran && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $rencana_tgl_pembayaran)) {
                [$d2, $m2, $y2] = explode('/', $rencana_tgl_pembayaran);
                $rencana_tgl_pembayaran = "$y2-$m2-$d2";
            }

            // handle lampiran SID upload (if provided)
            $lampiran_sid_path = null;
            if ($request->hasFile('lampiran_sid')) {
                $lampiran_sid_path = $request->file('lampiran_sid')->store('peminjaman/lampiran', 'public');
            }

            // Create header
            $numberService = new PeminjamanNumberService();

            $headerData = [
                'id_debitur' => $id_debitur,
                'id_instansi' => $id_instansi,
                'sumber_pembiayaan' => $sumber_pembiayaan,
                'nama_bank' => $nama_bank,
                'no_rekening' => $no_rekening,
                'nama_rekening' => $nama_rekening,
                'lampiran_sid' => $lampiran_sid_path,
                'tujuan_pembiayaan' => $tujuan_pembiayaan,
                'harapan_tanggal_pencairan' => $harapan_tanggal_pencairan,
                'rencana_tgl_pembayaran' => $rencana_tgl_pembayaran,
                'catatan_lainnya' => $request->catatan_lainnya,
                'status' => 'submitted',
                'created_by' => auth()->id() ?? null,
            ];

            // Create header first, then generate nomor based on header id (fallback when sequences table absent)
            $header = PeminjamanInvoiceFinancing::create($headerData);
            $header->nomor_peminjaman = (new PeminjamanNumberService())->generateFromId($header->id_invoice_financing, 'INV', $header->created_at?->format('Ym'));
            $header->save();

            $sumPinjaman = 0;
            $sumBagi = 0;

            foreach ($invoices as $i => $inv) {
                // server-side validation per invoice
                $no = $inv['no_invoice'] ?? null;
                $nilai_invoice = floatval($inv['nilai_invoice'] ?? 0);
                $nilai_pinjaman = floatval($inv['nilai_pinjaman'] ?? 0);

                if (!$no || $nilai_pinjaman <= 0) {
                    throw new \Exception("Invalid invoice at index {$i}");
                }

                if ($nilai_pinjaman > $nilai_invoice) {
                    throw new \Exception("Nilai pinjaman cannot exceed nilai invoice for invoice {$no}");
                }

                $nilai_bagi = round($nilai_pinjaman * 0.02, 2);

                // handle files mapping: expect files[{$i}][dokumen_invoice] etc.
                $dok_invoice_path = null;
                $dok_kontrak_path = null;
                $dok_so_path = null;
                $dok_bast_path = null;

                if ($request->hasFile("files.{$i}.dokumen_invoice")) {
                    $dok_invoice_path = $request->file("files.{$i}.dokumen_invoice")->store('peminjaman/invoices', 'public');
                }
                if ($request->hasFile("files.{$i}.dokumen_kontrak")) {
                    $dok_kontrak_path = $request->file("files.{$i}.dokumen_kontrak")->store('peminjaman/invoices', 'public');
                }
                if ($request->hasFile("files.{$i}.dokumen_so")) {
                    $dok_so_path = $request->file("files.{$i}.dokumen_so")->store('peminjaman/invoices', 'public');
                }
                if ($request->hasFile("files.{$i}.dokumen_bast")) {
                    $dok_bast_path = $request->file("files.{$i}.dokumen_bast")->store('peminjaman/invoices', 'public');
                }

                InvoiceFinancing::create([
                    'id_invoice_financing' => $header->id_invoice_financing,
                    'no_invoice' => $no,
                    'nama_client' => $inv['nama_client'] ?? null,
                    'nilai_invoice' => $nilai_invoice,
                    'nilai_pinjaman' => $nilai_pinjaman,
                    'nilai_bagi_hasil' => $nilai_bagi,
                    'invoice_date' => $inv['invoice_date'] ?? null,
                    'due_date' => $inv['due_date'] ?? null,
                    'dokumen_invoice' => $dok_invoice_path,
                    'dokumen_kontrak' => $dok_kontrak_path,
                    'dokumen_so' => $dok_so_path,
                    'dokumen_bast' => $dok_bast_path,
                    'created_by' => auth()->id() ?? null,
                ]);

                $sumPinjaman += $nilai_pinjaman;
                $sumBagi += $nilai_bagi;
            }

            // update header totals
            // If client provided a manual total_pinjaman, prefer that value and compute bagi hasil from it;
            // otherwise use sums derived from invoice rows.
            $manualTotal = $request->input('total_pinjaman');
            if ($manualTotal) {
                // try to normalize numeric from formatted input (remove non-digits except dot)
                $manualClean = preg_replace('/[^0-9\.]/', '', $manualTotal);
                $manualValue = floatval($manualClean);
                $header->total_pinjaman = $manualValue;
                // compute bagi hasil using same rate as invoice rows (2%)
                $header->total_bagi_hasil = round($manualValue * 0.02, 2);
                $header->pembayaran_total = $header->total_pinjaman + $header->total_bagi_hasil;
            } else {
                $header->total_pinjaman = $sumPinjaman;
                $header->total_bagi_hasil = $sumBagi;
                $header->pembayaran_total = $sumPinjaman + $sumBagi;
            }
            $header->save();

            DB::commit();

            return response()->json(['success'=>true,'data'=>['id_invoice_financing'=>$header->id_invoice_financing]]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    }
}
