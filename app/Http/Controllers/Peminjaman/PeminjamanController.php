<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeminjamanInvoiceFinancing;
use App\Models\MasterDebiturDanInvestor;

class PeminjamanController extends Controller
{
    /**
     * Display the specified resource detail view.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $header = PeminjamanInvoiceFinancing::with(['debitur.kol', 'invoices'])->find($id);
        if (!$header) {
            abort(404);
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

        $peminjaman = [
            'id' => $header->id_peminjaman,
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

        $po_financing_data = [];
        $installment_data = [];
        $factoring_data = [];

        $banks = ['BCA','Mandiri','BNI','BRI','CIMB Niaga','Danamon','Permata Bank','OCBC NISP','UOB Indonesia','Panin Bank'];
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
     * Display the peminjaman index page (list).
     */
    public function index()
    {
        $records = PeminjamanInvoiceFinancing::with(['debitur.kol'])->orderBy('created_at','desc')->get();
        $peminjaman_data = $records->map(function($r) {
            return [
                'id' => $r->id_peminjaman,
                'nama_perusahaan' => $r->debitur->nama_debitur ?? '',
                'lampiran_sid' => $r->lampiran_sid,
                'nilai_kol' => $r->debitur->kol->kol ?? '',
                'status' => $r->status ?? 'draft',
            ];
        })->toArray();

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

        $banks = ['BCA','Mandiri','BNI','BRI','CIMB Niaga','Danamon','Permata Bank','OCBC NISP','UOB Indonesia','Panin Bank'];

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
