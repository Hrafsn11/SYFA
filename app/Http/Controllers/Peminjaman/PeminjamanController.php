<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeminjamanInvoiceFinancing;
use App\Models\PeminjamanInstallmentFinancing;
use App\Models\PeminjamanFactoring;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Display the specified resource detail view.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $requestedType = $request->query('type');

        if ($requestedType === 'invoice') {
            $headerInvoice = PeminjamanInvoiceFinancing::with(['debitur.kol', 'invoices'])->find($id);
            if (!$headerInvoice) abort(404);
            $headerType = 'invoice';
            $header = $headerInvoice;
        } elseif ($requestedType === 'po') {
            $headerPO = \App\Models\PeminjamanPoFinancing::with(['debitur.kol','details'])->find($id);
            if (!$headerPO) abort(404);
            $headerType = 'po';
            $header = $headerPO;
        } elseif ($requestedType === 'installment') {
            $headerInst = PeminjamanInstallmentFinancing::with(['debitur.kol','details'])->find($id);
            if (!$headerInst) abort(404);
            $headerType = 'installment';
            $header = $headerInst;
        } elseif ($requestedType === 'factoring') {
            $headerFact = PeminjamanFactoring::with(['debitur.kol','details'])->find($id);
            if (!$headerFact) abort(404);
            $headerType = 'factoring';
            $header = $headerFact;
        } else {
            // Backward compatible fallback: try invoice first then PO
            $headerInvoice = PeminjamanInvoiceFinancing::with(['debitur.kol', 'invoices'])->find($id);
            if ($headerInvoice) {
                $headerType = 'invoice';
                $header = $headerInvoice;
            } else {
                $headerPO = \App\Models\PeminjamanPoFinancing::with(['debitur.kol','details'])->find($id);
                if (!$headerPO) abort(404);
                $headerType = 'po';
                $header = $headerPO;
            }
        }

        $persentase = null;
        if (!empty($header->id_instansi)) {
            try {
                $master = \App\Models\MasterSumberPendanaanEksternal::find($header->id_instansi);
                $persentase = $master?->persentase_bagi_hasil ?? null;
            } catch (\Throwable $e) {
                $persentase = null;
            }
        }

        if ($headerType === 'invoice') {
            $peminjaman = [
                'id' => $header->id_invoice_financing,
                'nama_perusahaan' => $header->debitur->nama_debitur ?? '',
                'nama_bank' => $header->nama_bank,
                'no_rekening' => $header->no_rekening,
                'lampiran_sid' => $header->lampiran_sid,
                'nilai_kol' => $header->debitur->kol->kol ?? '',
                'nominal_pinjaman' => $header->total_pinjaman,
                'harapan_tanggal_pencairan' => $header->harapan_tanggal_pencairan,
                'rencana_tgl_pembayaran' => $header->rencana_tgl_pembayaran,
                'total_bagi_hasil' => $header->total_bagi_hasil,
                'pembayaran_total' => $header->pembayaran_total ?? null,
                'persentase_bagi_hasil' => $persentase,
                'jenis_pembiayaan' => $header->sumber_pembiayaan ?? 'Invoice Financing',
            ];
        } else {
            // PO header mapping
            $peminjaman = [
                'id' => $header->id_po_financing,
                'nama_perusahaan' => $header->debitur?->nama_debitur ?? '',
                'nama_bank' => $header->nama_bank,
                'no_rekening' => $header->no_rekening,
                'lampiran_sid' => $header->lampiran_sid,
                'nilai_kol' => $header->debitur?->kol->kol ?? '',
                'nominal_pinjaman' => $header->total_pinjaman,
                'harapan_tanggal_pencairan' => $header->harapan_tanggal_pencairan,
                'rencana_tgl_pembayaran' => $header->rencana_tgl_pembayaran,
                'total_bagi_hasil' => $header->total_bagi_hasil,
                'pembayaran_total' => $header->pembayaran_total ?? null,
                'persentase_bagi_hasil' => $persentase,
                'jenis_pembiayaan' => $header->sumber_pembiayaan ?? 'PO Financing',
            ];
        }

        $invoice_financing_data = [];
        $po_financing_data = [];

        if ($headerType === 'invoice') {
            $invoice_financing_data = $header->invoices->map(function($inv) {
                return [
                    'no_invoice' => $inv->no_invoice,
                    'nama_client' => $inv->nama_client,
                    'nilai_invoice' => $inv->nilai_invoice,
                    'nilai_pinjaman' => $inv->nilai_pinjaman,
                    'nilai_bagi_hasil' => $inv->nilai_bagi_hasil,
                    'invoice_date' => $inv->invoice_date,
                    'due_date' => $inv->due_date,
                    'dokumen_invoice' => $inv->dokumen_invoice,
                    'dokumen_kontrak' => $inv->dokumen_kontrak,
                    'dokumen_so' => $inv->dokumen_so,
                    'dokumen_bast' => $inv->dokumen_bast,
                ];
            })->toArray();
        } elseif ($headerType === 'po') {
            // Map PO details only when header explicitly a PO
            $po_financing_data = $header->details->map(function($d) {
                return [
                    'no_kontrak' => $d->no_kontrak,
                    'nama_client' => $d->nama_client,
                    'nilai_invoice' => $d->nilai_invoice,
                    'nilai_pinjaman' => $d->nilai_pinjaman,
                    'nilai_bagi_hasil' => $d->nilai_bagi_hasil,
                    'kontrak_date' => $d->kontrak_date,
                    'due_date' => $d->due_date,
                    'dokumen_kontrak' => $d->dokumen_kontrak,
                    'dokumen_so' => $d->dokumen_so,
                    'dokumen_bast' => $d->dokumen_bast,
                    'dokumen_lainnya' => $d->dokumen_lainnya,
                ];
            })->toArray();
        }

        $installment_data = [];
        // If header is installment, map header and installment_data accordingly
        if ($headerType === 'installment') {
            $peminjaman = [
                'id' => $header->id_installment,
                'nama_perusahaan' => $header->debitur?->nama_debitur ?? '',
                'nama_bank' => $header->nama_bank,
                'no_rekening' => $header->no_rekening,
                'lampiran_sid' => $header->lampiran_sid ?? null,
                'nilai_kol' => $header->debitur?->kol->kol ?? '',
                'nominal_pinjaman' => $header->total_pinjaman,
                'tenor_pembayaran' => $header->tenor_pembayaran ?? null,
                'total_bagi_hasil' => $header->persentase_bagi_hasil ?? null,
                'pps' => $header->pps ?? null,
                'sfinance' => $header->sfinance ?? null,
                'pembayaran_total' => $header->total_pembayaran ?? null,
                'yang_harus_dibayarkan' => $header->yang_harus_dibayarkan ?? null,
                'persentase_bagi_hasil' => $header->persentase_bagi_hasil ?? null,
                'jenis_pembiayaan' => 'Installment',
            ];

            $installment_data = $header->details->map(function($d) {
                return [
                    'no_invoice' => $d->no_invoice,
                    'nama_client' => $d->nama_client,
                    'nilai_invoice' => $d->nilai_invoice,
                    'invoice_date' => $d->invoice_date,
                    'nama_barang' => $d->nama_barang,
                    'dokumen_invoice' => $d->dokumen_invoice,
                    'dokumen_lainnya' => $d->dokumen_lainnya,
                ];
            })->toArray();
        }
        $factoring_data = [];
        if ($headerType === 'factoring') {
            $peminjaman = [
                'id' => $header->id_factoring,
                'nama_perusahaan' => $header->debitur?->nama_debitur ?? '',
                'nama_bank' => $header->nama_bank,
                'no_rekening' => $header->no_rekening,
                'lampiran_sid' => $header->lampiran_sid ?? null,
                'nilai_kol' => $header->debitur?->kol->kol ?? '',
                'nominal_pinjaman' => $header->total_nominal_yang_dialihkan,
                'harapan_tanggal_pencairan' => $header->harapan_tanggal_pencairan,
                'rencana_tgl_pembayaran' => $header->rencana_tgl_pembayaran,
                'total_bagi_hasil' => $header->total_bagi_hasil,
                'pembayaran_total' => $header->pembayaran_total ?? null,
                'persentase_bagi_hasil' => $persentase,
                'jenis_pembiayaan' => 'Factoring',
            ];

            $factoring_data = $header->details->map(function($d) {
                return [
                    'no_kontrak' => $d->no_kontrak,
                    'nama_client' => $d->nama_client,
                    'nilai_invoice' => $d->nilai_invoice,
                    'nilai_pinjaman' => $d->nilai_pinjaman,
                    'nilai_bagi_hasil' => $d->nilai_bagi_hasil,
                    'kontrak_date' => $d->kontrak_date,
                    'due_date' => $d->due_date,
                    'dokumen_invoice' => $d->dokumen_invoice,
                    'dokumen_kontrak' => $d->dokumen_kontrak,
                    'dokumen_so' => $d->dokumen_so,
                    'dokumen_bast' => $d->dokumen_bast,
                ];
            })->toArray();
        }

        // Try to read enum values for `nama_bank` from DB so we don't keep duplicate hardcoded lists.
        // Fallback to the previous hardcoded array if query fails or column isn't an enum.
        try {
            $banks = [];
            $column = DB::selectOne("SHOW COLUMNS FROM peminjaman_invoice_financing LIKE 'nama_bank'");
            if ($column && preg_match('/^enum\((.*)\)$/', $column->Type, $matches)) {
                $vals = explode(',', $matches[1]);
                foreach ($vals as $v) {
                    // strip surrounding quotes and trim
                    $banks[] = trim($v, "' \t\n\r\0\x0B");
                }
            }
            if (empty($banks)) {
                // fallback
                $banks = ['BCA','BSI','Mandiri','BNI','BRI','CIMB Niaga','Danamon','Permata Bank','OCBC NISP','UOB Indonesia','Panin Bank'];
            }
        } catch (\Throwable $e) {
            $banks = ['BCA','BSI','Mandiri','BNI','BRI','CIMB Niaga','Danamon','Permata Bank','OCBC NISP','UOB Indonesia','Panin Bank'];
        }
        $tenor_pembayaran = [
            ['value' => '3', 'label' => '3 Bulan'],
            ['value' => '6', 'label' => '6 Bulan'],
            ['value' => '9', 'label' => '9 Bulan'],
            ['value' => '12', 'label' => '12 Bulan'],
        ];

        try {
            $sumber_eksternal = \App\Models\MasterSumberPendanaanEksternal::orderBy('nama_instansi')->get()
                ->map(fn($r) => ['id' => $r->id_instansi, 'nama' => $r->nama_instansi])->toArray();
        } catch (\Throwable $e) {
            $sumber_eksternal = [];
        }

        return view('livewire.peminjaman.detail', compact(
            'peminjaman', 'sumber_eksternal', 'banks', 'tenor_pembayaran',
            'invoice_financing_data', 'po_financing_data', 'installment_data', 'factoring_data'
        ));
    }

    /**
     * Display preview kontrak page.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function previewKontrak($id)
    {
        // Sample kontrak data
        $kontrak = [
            'id_invoice_financing' => $id,
            'no_kontrak' => 'SKI/FIN/2025/001',
            'tanggal_kontrak' => '22 September 2025',
            'nama_perusahaan' => 'SYNNOVAC CAPITAL',
            'nama_debitur' => 'Techno Infinity',
            'nama_pimpinan' => 'Cahyo',
            'alamat' => 'Gd. Permata Kuningan Lantai 17 Unit 07 Jl. Kuningan Mulia',
            'tujuan_pembiayaan' => 'Kebutuhan Gaji Operasional/Umum Sept',
            'jenis_pembiayaan' => 'Invoice & Project Financing',
            'nilai_pembiayaan' => 'Rp. 250.000.000',
            'hutang_pokok' => 'Rp. 250.000.000',
            'tenor' => '1 Bulan',
            'biaya_admin' => 'Rp. 0.00',
            'nisbah' => '2% flat / bulan',
            'denda_keterlambatan' => '2% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut',
            'jaminan' => 'Invoice & Project Financing',
        ];

        return view('livewire.peminjaman.preview-kontrak', compact('kontrak'));
    }

    /**
     * Display the peminjaman index page (list).
     */
    public function index()
    {
        $invoiceRecords = PeminjamanInvoiceFinancing::with(['debitur.kol'])->get();
        $poRecords = \App\Models\PeminjamanPoFinancing::with(['debitur.kol'])->get();
        $installmentRecords = PeminjamanInstallmentFinancing::with(['debitur.kol'])->get();

        $invoiceData = $invoiceRecords->map(function($r) {
            return [
                'id' => $r->id_invoice_financing,
                'type' => 'invoice',
                'nama_perusahaan' => $r->debitur->nama_debitur ?? '',
                'lampiran_sid' => $r->lampiran_sid,
                'nilai_kol' => $r->debitur->kol->kol ?? '',
                'status' => $r->status ?? 'draft',
            ];
        })->toArray();

        $poData = $poRecords->map(function($r) {
            return [
                'id' => $r->id_po_financing,
                'type' => 'po',
                'nama_perusahaan' => $r->debitur?->nama_debitur ?? '',
                'lampiran_sid' => $r->lampiran_sid,
                'nilai_kol' => $r->debitur?->kol->kol ?? '',
                'status' => $r->status ?? 'draft',
            ];
        })->toArray();

        $installmentData = $installmentRecords->map(function($r) {
            return [
                'id' => $r->id_installment,
                'type' => 'installment',
                'nama_perusahaan' => $r->debitur?->nama_debitur ?? '',
                'lampiran_sid' => $r->lampiran_sid ?? null,
                'nilai_kol' => $r->debitur?->kol->kol ?? '',
                'status' => $r->status ?? 'draft',
            ];
        })->toArray();

        // Factoring records
        $factoringRecords = \App\Models\PeminjamanFactoring::with(['debitur.kol'])->get();
        $factoringData = $factoringRecords->map(function($r) {
            return [
                'id' => $r->id_factoring,
                'type' => 'factoring',
                'nama_perusahaan' => $r->debitur?->nama_debitur ?? '',
                'lampiran_sid' => $r->lampiran_sid ?? null,
                'nilai_kol' => $r->debitur?->kol->kol ?? '',
                'status' => $r->status ?? 'draft',
            ];
        })->toArray();

    $peminjaman_data = array_merge($invoiceData, $poData, $installmentData, $factoringData);

        return view('livewire.peminjaman.index', compact('peminjaman_data'));
    }

    /**
     * Display the create peminjaman page (form).
     */
    public function create()
    {
        try {
            $sumber_eksternal = \App\Models\MasterSumberPendanaanEksternal::orderBy('nama_instansi')->get()
                ->map(function($row) {
                    return ['id' => $row->id_instansi, 'nama' => $row->nama_instansi];
                })->toArray();
        } catch (\Throwable $e) {
            $sumber_eksternal = [];
        }

        $tenor_pembayaran = [
            ['value' => '3', 'label' => '3 Bulan'],
            ['value' => '6', 'label' => '6 Bulan'],
            ['value' => '9', 'label' => '9 Bulan'],
            ['value' => '12', 'label' => '12 Bulan'],
        ];

        $kebutuhan_pinjaman = [
            ['value' => 'Lainnya', 'label' => 'Lainnya'],
            ['value' => 'Modal Usaha', 'label' => 'Modal Usaha'],
            ['value' => 'Pengembangan Bisnis', 'label' => 'Pengembangan Bisnis'],
            ['value' => 'Operasional', 'label' => 'Operasional'],
            ['value' => 'Investasi', 'label' => 'Investasi'],
        ];

        $invoice_financing_data = [];
        $po_financing_data = [];
        $installment_data = [];
        $factoring_data = [];

        // Try to read enum values for `nama_bank` from DB so we don't keep duplicate hardcoded lists.
        // Fallback to a reasonable default if query fails or column isn't an enum.
        try {
            $banks = [];
            $column = DB::selectOne("SHOW COLUMNS FROM peminjaman_invoice_financing LIKE 'nama_bank'");
            if ($column && preg_match('/^enum\((.*)\)$/', $column->Type, $matches)) {
                $vals = explode(',', $matches[1]);
                foreach ($vals as $v) {
                    $banks[] = trim($v, "' \t\n\r\0\x0B");
                }
            }
            if (empty($banks)) {
                $banks = ['BCA','BSI','Mandiri','BNI','BRI','CIMB Niaga','Danamon','Permata Bank','OCBC NISP','UOB Indonesia','Panin Bank'];
            }
        } catch (\Throwable $e) {
            $banks = ['BCA','BSI','Mandiri','BNI','BRI','CIMB Niaga','Danamon','Permata Bank','OCBC NISP','UOB Indonesia','Panin Bank'];
        }

        $master = null;
        try {
            if (auth()->check()) {
                $userEmail = auth()->user()->email;
                $master = \App\Models\MasterDebiturDanInvestor::where('email', $userEmail)->with('kol')->first();
            }
        } catch (\Throwable $e) {
            // In case auth or model lookup fails in some contexts (e.g. artisan tinker), we silently ignore
            // and continue rendering the form without pre-fill.
            $master = null;
        }

        return view('livewire.peminjaman.create', compact(
            'sumber_eksternal','tenor_pembayaran','kebutuhan_pinjaman','invoice_financing_data','po_financing_data','installment_data','factoring_data','banks','master'
        ));
    }
}
