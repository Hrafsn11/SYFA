<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use App\Models\BuktiPeminjaman;
use App\Models\HistoryStatusPengajuanPinjaman;
use Illuminate\Http\Request;
use App\Models\PeminjamanInvoiceFinancing;
use App\Models\PeminjamanInstallmentFinancing;
use App\Models\PeminjamanFactoring;
use App\Models\MasterDebiturDanInvestor;
use App\Models\MasterSumberPendanaanEksternal;
use App\Models\Peminjaman;
use App\Models\PengajuanPeminjaman;
use App\Services\PeminjamanNumberService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        // Use unified PengajuanPeminjaman model
        $header = PengajuanPeminjaman::with(['debitur.kol', 'instansi', 'buktiPeminjaman'])->find($id);
        if (!$header) abort(404);
        
        $headerType = strtolower(str_replace(' ', '_', $header->jenis_pembiayaan ?? 'invoice_financing'));

        $persentase = $header->instansi?->persentase_bagi_hasil ?? null;

        // Unified peminjaman data structure
        $peminjaman = [
            'id' => $header->id_pengajuan_peminjaman,
            'nomor_peminjaman' => $header->nomor_peminjaman,
            'nama_perusahaan' => $header->debitur->nama ?? '',
            'nama_ceo' => $header->debitur->nama_ceo ?? '',
            'alamat' => $header->debitur->alamat ?? '',
            'instansi' => $header->instansi?->nama_instansi ?? null,
            'tanda_tangan' => $header->debitur->tanda_tangan ?? null,
            'nama_bank' => $header->nama_bank,
            'no_rekening' => $header->no_rekening,
            'nama_rekening' => $header->nama_rekening,
            'lampiran_sid' => $header->lampiran_sid,
            'nilai_kol' => $header->nilai_kol,
            'nominal_pinjaman' => $header->total_pinjaman,
            'harapan_tanggal_pencairan' => $header->harapan_tanggal_pencairan,
            'rencana_tgl_pembayaran' => $header->rencana_tgl_pembayaran,
            'total_bagi_hasil' => $header->total_bagi_hasil,
            'pembayaran_total' => $header->pembayaran_total ?? null,
            'persentase_bagi_hasil' => $persentase,
            'jenis_pembiayaan' => $header->jenis_pembiayaan,
            'sumber_pembiayaan' => $header->sumber_pembiayaan,
            'tujuan_pembiayaan' => $header->tujuan_pembiayaan,
            'catatan_lainnya' => $header->catatan_lainnya,
            'status' => $header->status,
            // Installment specific fields
            'tenor_pembayaran' => $header->tenor_pembayaran,
            'pps' => $header->pps,
            'sfinance' => $header->sfinance,
            'yang_harus_dibayarkan' => $header->yang_harus_dibayarkan,
            // Factoring specific fields
            'total_nominal_yang_dialihkan' => $header->total_nominal_yang_dialihkan,
            // Upload fields
            'upload_bukti_transfer' => $header->upload_bukti_transfer,
        ];

        // Get current step from latest history record
        $latestHistory = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $header->id_pengajuan_peminjaman)
            ->orderBy('created_at', 'desc')
            ->first();

        // Get all history records for activity timeline
        $allHistory = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $header->id_pengajuan_peminjaman)
            ->orderBy('created_at', 'desc')
            ->with(['approvedBy', 'rejectedBy', 'submittedBy'])
            ->get();
        
        $currentStep = 1; // Default to step 1
        if ($latestHistory) {
            // Use current_step from history if available, otherwise map from status
            if ($latestHistory->current_step) {
                $currentStep = $latestHistory->current_step;
            } else {
                // Fallback mapping for older records without current_step
                $statusToStep = [
                    'Submit Dokumen' => 2,
                    'Dokumen Tervalidasi' => 3,
                    'Debitur Setuju' => 4,
                    'Disetujui oleh CEO SKI' => 5,
                    'Disetujui oleh Direktur SKI' => 6,
                    'Generate Kontrak' => 7,
                    'Dana Sudah Dicairkan' => 8,
                ];
                
                $currentStep = $statusToStep[$latestHistory->status] ?? 1;
            }
        }
        
        $peminjaman['current_step'] = $currentStep;

        // Add latest history data (nominal disetujui and tanggal pencairan from latest update)
        if ($latestHistory) {
            $peminjaman['nominal_yang_disetujui'] = $latestHistory->nominal_yang_disetujui;
            $peminjaman['tanggal_pencairan'] = $latestHistory->tanggal_pencairan;
            
            // Debug: log the actual values
            // dd($peminjaman['nominal_yang_disetujui']);
        } else {
            $peminjaman['nominal_yang_disetujui'] = null;
            $peminjaman['tanggal_pencairan'] = null;
        }

        // Get all bukti peminjaman (details) for this pengajuan
        $details_data = $header->buktiPeminjaman->map(function($bukti) use ($header) {
            $baseData = [
                'nama_client' => $bukti->nama_client,
                'nilai_invoice' => $bukti->nilai_invoice,
                'nilai_pinjaman' => $bukti->nilai_pinjaman,
                'nilai_bagi_hasil' => $bukti->nilai_bagi_hasil,
                'invoice_date' => $bukti->invoice_date,
                'due_date' => $bukti->due_date,
                'dokumen_invoice' => $bukti->dokumen_invoice,
                'dokumen_kontrak' => $bukti->dokumen_kontrak,
                'dokumen_so' => $bukti->dokumen_so,
                'dokumen_bast' => $bukti->dokumen_bast,
                'dokumen_lainnya' => $bukti->dokumen_lainnya,
            ];

            // Add conditional attributes based on jenis_pembiayaan
            if ($header->jenis_pembiayaan === 'Invoice Financing' || $header->jenis_pembiayaan === 'Installment') {
                $baseData['no_invoice'] = $bukti->no_invoice;
            } else {
                $baseData['no_kontrak'] = $bukti->no_kontrak;
            }

            if ($header->jenis_pembiayaan === 'PO Financing') {
                $baseData['kontrak_date'] = $bukti->kontrak_date;
                $baseData['nama_barang'] = $bukti->nama_barang;
            } elseif ($header->jenis_pembiayaan === 'Installment') {
                $baseData['nama_barang'] = $bukti->nama_barang;
            }

            return $baseData;
        })->toArray();

        // Separate data arrays for backward compatibility with views
        $invoice_financing_data = ($header->jenis_pembiayaan === 'Invoice Financing') ? $details_data : [];
        $po_financing_data = ($header->jenis_pembiayaan === 'PO Financing') ? $details_data : [];
        $installment_data = ($header->jenis_pembiayaan === 'Installment') ? $details_data : [];
        $factoring_data = ($header->jenis_pembiayaan === 'Factoring') ? $details_data : [];

        // Try to read enum values for `nama_bank` from DB so we don't keep duplicate hardcoded lists.
        // Fallback to the previous hardcoded array if query fails or column isn't an enum.
        try {
            $banks = [];
            $column = DB::selectOne("SHOW COLUMNS FROM pengajuan_peminjaman LIKE 'nama_bank'");
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
            $sumber_eksternal = MasterSumberPendanaanEksternal::orderBy('nama_instansi')->get()
                ->map(fn($r) => [
                    'id' => $r->id_instansi,
                    'nama' => $r->nama_instansi,
                    'persentase_bagi_hasil' => $r->persentase_bagi_hasil ?? 0
                ])->toArray();
        } catch (\Throwable $e) {
            $sumber_eksternal = [];
        }

        return view('livewire.peminjaman.detail', compact(
            'peminjaman', 'sumber_eksternal', 'banks', 'tenor_pembayaran',
            'invoice_financing_data', 'po_financing_data', 'installment_data', 'factoring_data',
            'latestHistory', 'allHistory'
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
        // Get pengajuan data from database with debitur relationship
        $pengajuan = PengajuanPeminjaman::with('debitur')
            ->where('id_pengajuan_peminjaman', $id)
            ->first();
        
        if (!$pengajuan) {
            abort(404, 'Pengajuan peminjaman tidak ditemukan');
        }
        


        // Get latest approved nominal from history
        $latestHistory = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $id)
            ->whereNotNull('nominal_yang_disetujui')
            ->orderBy('created_at', 'desc')
            ->first();

        // Generate contract number
        $no_kontrak = 'SKI/FIN/' . date('Y') . '/' . str_pad($pengajuan->id_pengajuan_peminjaman, 3, '0', STR_PAD_LEFT);
        
        // Prepare kontrak data from database
        $kontrak = [
            'id_peminjaman' => $id,
            'no_kontrak' => $no_kontrak,
            'tanggal_kontrak' => now()->format('d F Y'),
            'nama_perusahaan' => 'SYNNOVAC CAPITAL',
            'nama_debitur' => $pengajuan->debitur->nama ?? 'N/A',
            'nama_pimpinan' => $pengajuan->debitur->nama_ceo ?? 'N/A',
            'alamat' => $pengajuan->debitur->alamat ?? 'N/A',
            'tujuan_pembiayaan' => $pengajuan->tujuan_pembiayaan ?? 'N/A',
            'jenis_pembiayaan' => $pengajuan->jenis_pembiayaan ?? 'Invoice & Project Financing',
            'nilai_pembiayaan' => 'Rp. ' . number_format($latestHistory->nominal_yang_disetujui ?? $pengajuan->total_pinjaman ?? 0, 0, ',', '.'),
            'hutang_pokok' => 'Rp. ' . number_format($latestHistory->nominal_yang_disetujui ?? $pengajuan->total_pinjaman ?? 0, 0, ',', '.'),
            'tenor' => ($pengajuan->tenor_pembayaran ?? 1) . ' Bulan',
            'biaya_admin' => 'Rp. 0',
            'nisbah' => ($pengajuan->persentase_bagi_hasil ?? 2) . '% flat / bulan',
            'denda_keterlambatan' => '2% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut',
            'jaminan' => $pengajuan->jenis_pembiayaan ?? 'Invoice & Project Financing',
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
                'nomor_peminjaman' => $r->nomor_peminjaman ?? null,
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
                'nomor_peminjaman' => $r->nomor_peminjaman ?? null,
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
                'nomor_peminjaman' => $r->nomor_peminjaman ?? null,
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
                'nomor_peminjaman' => $r->nomor_peminjaman ?? null,
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
     * Display the create/edit peminjaman page (form).
     */
    public function create()
    {
        return $this->createOrEdit(null);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Get pengajuan peminjaman data
        $pengajuan = PengajuanPeminjaman::with(['debitur', 'instansi', 'buktiPeminjaman'])->findOrFail($id);
        
        // Check if status allows editing
        if (!in_array($pengajuan->status, ['Draft', 'Validasi Ditolak'])) {
            return redirect()->route('peminjaman')->with('error', 'Pengajuan dengan status ' . $pengajuan->status . ' tidak dapat diedit.');
        }

        return $this->createOrEdit($pengajuan);
    }

    /**
     * Unified method for create and edit form
     */
    private function createOrEdit($pengajuan = null)
    {
        $isEdit = !is_null($pengajuan);

        // Get sumber eksternal data
        try {
            $sumber_eksternal = MasterSumberPendanaanEksternal::orderBy('nama_instansi')->get()
                ->map(function($row) {
                    return [
                        'id' => $row->id_instansi,
                        'nama' => $row->nama_instansi,
                        'persentase_bagi_hasil' => $row->persentase_bagi_hasil ?? 0
                    ];
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

        // Get banks list
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

        // Initialize data arrays
        $invoice_financing_data = [];
        $po_financing_data = [];
        $installment_data = [];
        $factoring_data = [];

        // Get master debitur
        if ($isEdit) {
            // For edit mode, get from pengajuan
            $master = $pengajuan->debitur;

            // Prepare existing data based on jenis_pembiayaan
            if ($pengajuan->jenis_pembiayaan === 'Invoice Financing') {
                $invoice_financing_data = $pengajuan->buktiPeminjaman->map(function($bukti) {
                    return [
                        'no_invoice' => $bukti->no_invoice,
                        'nama_client' => $bukti->nama_client,
                        'nilai_invoice' => (int) $bukti->nilai_invoice,
                        'nilai_pinjaman' => (int) $bukti->nilai_pinjaman,
                        'nilai_bagi_hasil' => (int) $bukti->nilai_bagi_hasil,
                        'invoice_date' => $bukti->invoice_date,
                        'due_date' => $bukti->due_date,
                        'dokumen_invoice' => $bukti->dokumen_invoice,
                        'dokumen_kontrak' => $bukti->dokumen_kontrak,
                        'dokumen_so' => $bukti->dokumen_so,
                        'dokumen_bast' => $bukti->dokumen_bast,
                    ];
                })->toArray();
            } elseif ($pengajuan->jenis_pembiayaan === 'PO Financing') {
                $po_financing_data = $pengajuan->buktiPeminjaman->map(function($bukti) {
                    return [
                        'no_kontrak' => $bukti->no_kontrak,
                        'nama_client' => $bukti->nama_client,
                        'nilai_invoice' => (int) $bukti->nilai_invoice,
                        'nilai_pinjaman' => (int) $bukti->nilai_pinjaman,
                        'nilai_bagi_hasil' => (int) $bukti->nilai_bagi_hasil,
                        'kontrak_date' => $bukti->kontrak_date,
                        'due_date' => $bukti->due_date,
                        'dokumen_kontrak' => $bukti->dokumen_kontrak,
                        'dokumen_bast' => $bukti->dokumen_bast,
                        'dokumen_lainnya' => $bukti->dokumen_lainnya,
                    ];
                })->toArray();
            } elseif ($pengajuan->jenis_pembiayaan === 'Installment') {
                $installment_data = $pengajuan->buktiPeminjaman->map(function($bukti) {
                    return [
                        'no_invoice' => $bukti->no_invoice,
                        'nama_barang' => $bukti->nama_barang,
                        'nilai_invoice' => (int) $bukti->nilai_invoice,
                        'invoice_date' => $bukti->invoice_date,
                        'dokumen_invoice' => $bukti->dokumen_invoice,
                    ];
                })->toArray();
            } elseif ($pengajuan->jenis_pembiayaan === 'Factoring') {
                $factoring_data = $pengajuan->buktiPeminjaman->map(function($bukti) {
                    return [
                        'no_invoice' => $bukti->no_invoice,
                        'nama_client' => $bukti->nama_client,
                        'nilai_invoice' => (int) $bukti->nilai_invoice,
                        'nilai_pinjaman' => (int) $bukti->nilai_pinjaman,
                        'nilai_bagi_hasil' => (int) $bukti->nilai_bagi_hasil,
                        'invoice_date' => $bukti->invoice_date,
                        'due_date' => $bukti->due_date,
                        'dokumen_invoice' => $bukti->dokumen_invoice,
                        'dokumen_kontrak' => $bukti->dokumen_kontrak,
                        'dokumen_so' => $bukti->dokumen_so,
                        'dokumen_bast' => $bukti->dokumen_bast,
                    ];
                })->toArray();
            }
        } else {
            // For create mode, get from logged in user
            $master = null;
            try {
                if (auth()->check()) {
                    $userEmail = auth()->user()->email;
                    $master = \App\Models\MasterDebiturDanInvestor::where('email', $userEmail)
                        ->where('flagging', 'tidak')
                        ->where('status', 'active')
                        ->with('kol')
                        ->first();
                }
            } catch (\Throwable $e) {
                $master = null;
            }
        }

        return view('livewire.peminjaman.create', compact(
            'pengajuan',
            'sumber_eksternal',
            'tenor_pembayaran',
            'kebutuhan_pinjaman',
            'invoice_financing_data',
            'po_financing_data',
            'installment_data',
            'factoring_data',
            'banks',
            'master',
            'isEdit'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Get pengajuan peminjaman
        $pengajuan = PengajuanPeminjaman::findOrFail($id);
        
        // Check if status allows editing
        if (!in_array($pengajuan->status, ['Draft', 'Validasi Ditolak'])) {
            return redirect()->route('peminjaman')->with('error', 'Pengajuan dengan status ' . $pengajuan->status . ' tidak dapat diedit.');
        }

        // Get jenis_pembiayaan first for conditional validation
        $jenisPembiayaan = $request->input('jenis_pembiayaan');
        
        // Build validation rules (same as store method)
        $rules = [
            'id_debitur' => 'required|integer',
            'nama_bank' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_rekening' => 'nullable|string',
            'jenis_pembiayaan' => 'required|string',
            'catatan_lainnya' => 'nullable|string',
        ];

        // Add conditional validation based on jenis_pembiayaan
        if ($jenisPembiayaan === 'Invoice Financing') {
            $rules['invoices'] = 'required|string';
            $rules['lampiran_sid'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['nilai_kol'] = 'nullable|string';
            if($request->sumber_pembiayaan === 'eksternal'){
                $rules['id_instansi'] = 'required|integer';
            }else{
                $rules['id_instansi'] = 'nullable';
            }
            $rules['sumber_pembiayaan'] = 'required|in:eksternal,internal';
            $rules['tujuan_pembiayaan'] = 'nullable|string';
            $rules['total_pinjaman'] = 'nullable';
            $rules['harapan_tanggal_pencairan'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['total_bagi_hasil'] = 'nullable';
            $rules['rencana_tgl_pembayaran'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['pembayaran_total'] = 'nullable';

        } elseif ($jenisPembiayaan === 'Installment') {
            $rules['details'] = 'required|array|min:1';
            $rules['total_pinjaman'] = 'nullable';
            $rules['tenor_pembayaran'] = 'nullable|in:3,6,9,12';
            $rules['persentase_bagi_hasil'] = 'nullable|numeric';
            $rules['pps'] = 'nullable|numeric';
            $rules['sfinance'] = 'nullable|numeric';
            $rules['total_pembayaran'] = 'nullable|numeric';
            $rules['yang_harus_dibayarkan'] = 'nullable|numeric';
            
        } elseif ($jenisPembiayaan === 'PO Financing') {
            $rules['details'] = 'required|array|min:1';
            if($request->sumber_pembiayaan === 'eksternal'){
                $rules['id_instansi'] = 'required|integer';
            }else{
                $rules['id_instansi'] = 'nullable';
            }
            $rules['no_kontrak'] = 'nullable|string';
            $rules['lampiran_sid'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['nilai_kol'] = 'nullable|string';
            $rules['sumber_pembiayaan'] = 'required|in:eksternal,internal';
            $rules['tujuan_pembiayaan'] = 'nullable|string';
            $rules['total_pinjaman'] = 'nullable';
            $rules['harapan_tanggal_pencairan'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['rencana_tgl_pembayaran'] = 'nullable|date_format:Y-m-d|sometimes';

        } elseif ($jenisPembiayaan === 'Factoring') {
            $rules['details'] = 'required|array|min:1';
            $rules['lampiran_sid'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['nilai_kol'] = 'nullable|string';
            if($request->sumber_pembiayaan === 'eksternal'){
                $rules['id_instansi'] = 'required|integer';
            }else{
                $rules['id_instansi'] = 'nullable';
            }
            $rules['sumber_pembiayaan'] = 'required|in:eksternal,internal';
            $rules['tujuan_pembiayaan'] = 'nullable|string';
            $rules['total_pinjaman'] = 'nullable';
            $rules['harapan_tanggal_pencairan'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['total_bagi_hasil'] = 'nullable';
            $rules['rencana_tgl_pembayaran'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['pembayaran_total'] = 'nullable';
            $rules['total_nominal_yang_dialihkan'] = 'nullable';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Handle file upload for lampiran_sid
            $lampiran_sid_path = $pengajuan->lampiran_sid;
            if ($request->hasFile('lampiran_sid')) {
                // Delete old file if exists
                if ($lampiran_sid_path && \Storage::disk('public')->exists($lampiran_sid_path)) {
                    \Storage::disk('public')->delete($lampiran_sid_path);
                }
                $lampiran_sid_path = $request->file('lampiran_sid')->store('lampiran_sid', 'public');
            }

            // Update pengajuan peminjaman header
            $pengajuan->update([
                'id_debitur' => $validated['id_debitur'],
                'nama_bank' => $validated['nama_bank'] ?? null,
                'no_rekening' => $validated['no_rekening'] ?? null,
                'nama_rekening' => $validated['nama_rekening'] ?? null,
                'jenis_pembiayaan' => $validated['jenis_pembiayaan'],
                'sumber_pembiayaan' => $validated['sumber_pembiayaan'] ?? null,
                'id_instansi' => $validated['id_instansi'] ?? null,
                'lampiran_sid' => $lampiran_sid_path,
                'nilai_kol' => $validated['nilai_kol'] ?? null,
                'tujuan_pembiayaan' => $validated['tujuan_pembiayaan'] ?? null,
                'total_pinjaman' => isset($validated['total_pinjaman']) ? str_replace(['Rp', 'Rp.', ',', '.', ' '], '', $validated['total_pinjaman']) : null,
                'harapan_tanggal_pencairan' => $validated['harapan_tanggal_pencairan'] ?? null,
                'total_bagi_hasil' => isset($validated['total_bagi_hasil']) ? str_replace(['Rp', 'Rp.', ',', '.', ' '], '', $validated['total_bagi_hasil']) : null,
                'rencana_tgl_pembayaran' => $validated['rencana_tgl_pembayaran'] ?? null,
                'pembayaran_total' => isset($validated['pembayaran_total']) ? str_replace(['Rp', 'Rp.', ',', '.', ' '], '', $validated['pembayaran_total']) : null,
                'catatan_lainnya' => $validated['catatan_lainnya'] ?? null,
                'tenor_pembayaran' => $validated['tenor_pembayaran'] ?? null,
                'persentase_bagi_hasil' => $validated['persentase_bagi_hasil'] ?? null,
                'pps' => isset($validated['pps']) ? str_replace(['Rp', 'Rp.', ',', '.', ' '], '', $validated['pps']) : null,
                's_finance' => isset($validated['sfinance']) ? str_replace(['Rp', 'Rp.', ',', '.', ' '], '', $validated['sfinance']) : null,
                'yang_harus_dibayarkan' => isset($validated['yang_harus_dibayarkan']) ? str_replace(['Rp', 'Rp.', ',', '.', ' '], '', $validated['yang_harus_dibayarkan']) : null,
                'total_nominal_yang_dialihkan' => isset($validated['total_nominal_yang_dialihkan']) ? str_replace(['Rp', 'Rp.', ',', '.', ' '], '', $validated['total_nominal_yang_dialihkan']) : null,
                'updated_by' => auth()->id(),
            ]);

            // Get existing bukti peminjaman to preserve file paths if no new files uploaded
            $existingBukti = BuktiPeminjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                ->get()
                ->keyBy(function($item) use ($jenisPembiayaan) {
                    // Key by invoice/kontrak number for matching
                    if ($jenisPembiayaan === 'Invoice Financing' || $jenisPembiayaan === 'Installment') {
                        return $item->no_invoice;
                    } else {
                        return $item->no_kontrak;
                    }
                })
                ->toArray();

            // Delete existing bukti peminjaman
            BuktiPeminjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)->delete();

            // Insert new bukti peminjaman based on jenis_pembiayaan
            if ($jenisPembiayaan === 'Invoice Financing') {
                $invoices = json_decode($validated['invoices'], true);
                foreach ($invoices as $i => $inv) {
                    // Clean numeric values - remove all non-numeric characters
                    $nilaiInvoice = isset($inv['nilai_invoice']) ? preg_replace('/[^0-9]/', '', $inv['nilai_invoice']) : null;
                    $nilaiPinjaman = isset($inv['nilai_pinjaman']) ? preg_replace('/[^0-9]/', '', $inv['nilai_pinjaman']) : null;
                    $nilaiBagiHasil = isset($inv['nilai_bagi_hasil']) ? preg_replace('/[^0-9]/', '', $inv['nilai_bagi_hasil']) : null;
                    
                    // Additional validation - ensure values are within reasonable range (max 10 digits = 9,999,999,999)
                    if ($nilaiInvoice && strlen($nilaiInvoice) > 10) {
                        throw new \Exception("Nilai invoice terlalu besar: {$inv['nilai_invoice']} (cleaned: {$nilaiInvoice})");
                    }
                    if ($nilaiPinjaman && strlen($nilaiPinjaman) > 10) {
                        throw new \Exception("Nilai pinjaman terlalu besar: {$inv['nilai_pinjaman']} (cleaned: {$nilaiPinjaman})");
                    }
                    
                    // Get existing file paths for this invoice (if any)
                    $noInvoice = $inv['no_invoice'] ?? null;
                    $existingFiles = $existingBukti[$noInvoice] ?? null;
                    
                    $dok_invoice_path = null;
                    $dok_kontrak_path = null;
                    $dok_so_path = null;
                    $dok_bast_path = null;
                    $dok_lainnya_path = null;
                    
                    if ($request->hasFile("files.{$i}.dokumen_invoice") || $request->hasFile("details.{$i}.dokumen_invoice")) {
                        $file = $request->hasFile("files.{$i}.dokumen_invoice") 
                            ? $request->file("files.{$i}.dokumen_invoice") 
                            : $request->file("details.{$i}.dokumen_invoice");
                        $dok_invoice_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_invoice'])) {
                        $dok_invoice_path = $existingFiles['dokumen_invoice'];
                    }
                    
                    if ($request->hasFile("files.{$i}.dokumen_kontrak") || $request->hasFile("details.{$i}.dokumen_kontrak")) {
                        $file = $request->hasFile("files.{$i}.dokumen_kontrak") 
                            ? $request->file("files.{$i}.dokumen_kontrak") 
                            : $request->file("details.{$i}.dokumen_kontrak");
                        $dok_kontrak_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_kontrak'])) {
                        $dok_kontrak_path = $existingFiles['dokumen_kontrak'];
                    }
                    
                    if ($request->hasFile("files.{$i}.dokumen_so") || $request->hasFile("details.{$i}.dokumen_so")) {
                        $file = $request->hasFile("files.{$i}.dokumen_so") 
                            ? $request->file("files.{$i}.dokumen_so") 
                            : $request->file("details.{$i}.dokumen_so");
                        $dok_so_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_so'])) {
                        $dok_so_path = $existingFiles['dokumen_so'];
                    }
                    
                    if ($request->hasFile("files.{$i}.dokumen_bast") || $request->hasFile("details.{$i}.dokumen_bast")) {
                        $file = $request->hasFile("files.{$i}.dokumen_bast") 
                            ? $request->file("files.{$i}.dokumen_bast") 
                            : $request->file("details.{$i}.dokumen_bast");
                        $dok_bast_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_bast'])) {
                        $dok_bast_path = $existingFiles['dokumen_bast'];
                    }
                    
                    if ($request->hasFile("files.{$i}.dokumen_lainnya") || $request->hasFile("details.{$i}.dokumen_lainnya")) {
                        $file = $request->hasFile("files.{$i}.dokumen_lainnya") 
                            ? $request->file("files.{$i}.dokumen_lainnya") 
                            : $request->file("details.{$i}.dokumen_lainnya");
                        $dok_lainnya_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_lainnya'])) {
                        $dok_lainnya_path = $existingFiles['dokumen_lainnya'];
                    }
                    
                    BuktiPeminjaman::create([
                        'id_pengajuan_peminjaman' => $pengajuan->id_pengajuan_peminjaman,
                        'no_invoice' => $inv['no_invoice'] ?? null,
                        'nama_client' => $inv['nama_client'] ?? null,
                        'nilai_invoice' => $nilaiInvoice,
                        'nilai_pinjaman' => $nilaiPinjaman,
                        'nilai_bagi_hasil' => $nilaiBagiHasil,
                        'invoice_date' => $inv['invoice_date'] ?? null,
                        'due_date' => $inv['due_date'] ?? null,
                        'dokumen_invoice' => $dok_invoice_path,
                        'dokumen_kontrak' => $dok_kontrak_path,
                        'dokumen_so' => $dok_so_path,
                        'dokumen_bast' => $dok_bast_path,
                        'dokumen_lainnya' => $dok_lainnya_path,
                    ]);
                }
            } elseif ($jenisPembiayaan === 'PO Financing') {
                $details = $validated['details'];
                foreach ($details as $i => $detail) {
                    // Get existing file paths for this kontrak (if any)
                    $noKontrak = $detail['no_kontrak'] ?? null;
                    $existingFiles = $existingBukti[$noKontrak] ?? null;
                    
                    // Handle file uploads for this detail - use new file if uploaded, otherwise keep old
                    // Check both files[i] and details[i] keys for compatibility
                    $dok_kontrak_path = null;
                    $dok_so_path = null;
                    $dok_bast_path = null;
                    $dok_lainnya_path = null;
                    
                    if ($request->hasFile("files.{$i}.dokumen_kontrak") || $request->hasFile("details.{$i}.dokumen_kontrak")) {
                        $file = $request->hasFile("files.{$i}.dokumen_kontrak") 
                            ? $request->file("files.{$i}.dokumen_kontrak") 
                            : $request->file("details.{$i}.dokumen_kontrak");
                        $dok_kontrak_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_kontrak'])) {
                        $dok_kontrak_path = $existingFiles['dokumen_kontrak'];
                    }
                    
                    if ($request->hasFile("files.{$i}.dokumen_so") || $request->hasFile("details.{$i}.dokumen_so")) {
                        $file = $request->hasFile("files.{$i}.dokumen_so") 
                            ? $request->file("files.{$i}.dokumen_so") 
                            : $request->file("details.{$i}.dokumen_so");
                        $dok_so_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_so'])) {
                        $dok_so_path = $existingFiles['dokumen_so'];
                    }
                    
                    if ($request->hasFile("files.{$i}.dokumen_bast") || $request->hasFile("details.{$i}.dokumen_bast")) {
                        $file = $request->hasFile("files.{$i}.dokumen_bast") 
                            ? $request->file("files.{$i}.dokumen_bast") 
                            : $request->file("details.{$i}.dokumen_bast");
                        $dok_bast_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_bast'])) {
                        $dok_bast_path = $existingFiles['dokumen_bast'];
                    }
                    
                    if ($request->hasFile("files.{$i}.dokumen_lainnya") || $request->hasFile("details.{$i}.dokumen_lainnya")) {
                        $file = $request->hasFile("files.{$i}.dokumen_lainnya") 
                            ? $request->file("files.{$i}.dokumen_lainnya") 
                            : $request->file("details.{$i}.dokumen_lainnya");
                        $dok_lainnya_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_lainnya'])) {
                        $dok_lainnya_path = $existingFiles['dokumen_lainnya'];
                    }
                    
                    BuktiPeminjaman::create([
                        'id_pengajuan_peminjaman' => $pengajuan->id_pengajuan_peminjaman,
                        'no_kontrak' => $detail['no_kontrak'] ?? null,
                        'nama_client' => $detail['nama_client'] ?? null,
                        'nilai_invoice' => isset($detail['nilai_invoice']) ? preg_replace('/[^0-9]/', '', $detail['nilai_invoice']) : null,
                        'nilai_pinjaman' => isset($detail['nilai_pinjaman']) ? preg_replace('/[^0-9]/', '', $detail['nilai_pinjaman']) : null,
                        'nilai_bagi_hasil' => isset($detail['nilai_bagi_hasil']) ? preg_replace('/[^0-9]/', '', $detail['nilai_bagi_hasil']) : null,
                        'kontrak_date' => $detail['kontrak_date'] ?? null,
                        'due_date' => $detail['due_date'] ?? null,
                        'dokumen_kontrak' => $dok_kontrak_path,
                        'dokumen_so' => $dok_so_path,
                        'dokumen_bast' => $dok_bast_path,
                        'dokumen_lainnya' => $dok_lainnya_path,
                    ]);
                }
            } elseif ($jenisPembiayaan === 'Installment') {
                $details = $validated['details'];
                foreach ($details as $i => $detail) {
                    // Get existing file paths for this invoice (if any)
                    $noInvoice = $detail['no_invoice'] ?? null;
                    $existingFiles = $existingBukti[$noInvoice] ?? null;
                    
                    // Handle file uploads for this detail - use new file if uploaded, otherwise keep old
                    $dok_invoice_path = null;
                    
                    if ($request->hasFile("files.{$i}.dokumen_invoice")) {
                        $dok_invoice_path = $request->file("files.{$i}.dokumen_invoice")->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_invoice'])) {
                        $dok_invoice_path = $existingFiles['dokumen_invoice'];
                    }
                    
                    BuktiPeminjaman::create([
                        'id_pengajuan_peminjaman' => $pengajuan->id_pengajuan_peminjaman,
                        'no_invoice' => $detail['no_invoice'] ?? null,
                        'nama_barang' => $detail['nama_barang'] ?? null,
                        'nilai_invoice' => isset($detail['nilai_invoice']) ? preg_replace('/[^0-9]/', '', $detail['nilai_invoice']) : null,
                        'invoice_date' => $detail['invoice_date'] ?? null,
                        'dokumen_invoice' => $dok_invoice_path,
                    ]);
                }
            } elseif ($jenisPembiayaan === 'Factoring') {
                $details = $validated['details'];
                foreach ($details as $i => $detail) {
                    // Get existing file paths for this invoice (if any)
                    $noInvoice = $detail['no_invoice'] ?? null;
                    $existingFiles = $existingBukti[$noInvoice] ?? null;
                    
                    // Handle file uploads for this detail - check both files[i] and details[i] keys
                    $dok_invoice_path = null;
                    
                    if ($request->hasFile("files.{$i}.dokumen_invoice") || $request->hasFile("details.{$i}.dokumen_invoice")) {
                        $file = $request->hasFile("files.{$i}.dokumen_invoice") 
                            ? $request->file("files.{$i}.dokumen_invoice") 
                            : $request->file("details.{$i}.dokumen_invoice");
                        $dok_invoice_path = $file->store('peminjaman/invoices', 'public');
                    } elseif ($existingFiles && isset($existingFiles['dokumen_invoice'])) {
                        $dok_invoice_path = $existingFiles['dokumen_invoice'];
                    }
                    
                    BuktiPeminjaman::create([
                        'id_pengajuan_peminjaman' => $pengajuan->id_pengajuan_peminjaman,
                        'no_invoice' => $detail['no_invoice'] ?? null,
                        'nama_client' => $detail['nama_client'] ?? null,
                        'nilai_invoice' => isset($detail['nilai_invoice']) ? preg_replace('/[^0-9]/', '', $detail['nilai_invoice']) : null,
                        'nilai_pinjaman' => isset($detail['nilai_pinjaman']) ? preg_replace('/[^0-9]/', '', $detail['nilai_pinjaman']) : null,
                        'nilai_bagi_hasil' => isset($detail['nilai_bagi_hasil']) ? preg_replace('/[^0-9]/', '', $detail['nilai_bagi_hasil']) : null,
                        'invoice_date' => $detail['invoice_date'] ?? null,
                        'due_date' => $detail['due_date'] ?? null,
                        'dokumen_invoice' => $dok_invoice_path,
                    ]);
                }
            }

            DB::commit();
            
            // Return JSON for AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan pinjaman berhasil diupdate!',
                    'data' => $pengajuan
                ]);
            }
            
            return redirect()->route('peminjaman')->with('success', 'Pengajuan pinjaman berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Return JSON error for AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate pengajuan pinjaman: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withInput()->with('error', 'Gagal mengupdate pengajuan pinjaman: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {

        // Get jenis_pembiayaan first for conditional validation
        $jenisPembiayaan = $request->input('jenis_pembiayaan');
        
        // Build validation rules based on jenis_pembiayaan
        $rules = [
            'id_debitur' => 'required|integer',
            'nama_bank' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_rekening' => 'nullable|string',
            'jenis_pembiayaan' => 'required|string',
            'catatan_lainnya' => 'nullable|string',
        ];

        // Add conditional validation based on jenis_pembiayaan
        if ($jenisPembiayaan === 'Invoice Financing') {
            $rules['invoices'] = 'required|string';
            $rules['lampiran_sid'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['nilai_kol'] = 'nullable|string';
            if($request->sumber_pembiayaan === 'eksternal'){
                $rules['id_instansi'] = 'required|integer';
            }else{
                $rules['id_instansi'] = 'nullable';
            }
            $rules['sumber_pembiayaan'] = 'required|in:eksternal,internal';
            $rules['tujuan_pembiayaan'] = 'nullable|string';
            $rules['total_pinjaman'] = 'nullable';
            $rules['harapan_tanggal_pencairan'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['total_bagi_hasil'] = 'nullable';
            $rules['rencana_tgl_pembayaran'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['pembayaran_total'] = 'nullable';


        } elseif ($jenisPembiayaan === 'Installment') {
            $rules['details'] = 'required|array|min:1';
            $rules['total_pinjaman'] = 'nullable';
            $rules['tenor_pembayaran'] = 'nullable|in:3,6,9,12';
            $rules['persentase_bagi_hasil'] = 'nullable|numeric';
            $rules['pps'] = 'nullable|numeric';
            $rules['sfinance'] = 'nullable|numeric';
            $rules['total_pembayaran'] = 'nullable|numeric';
            $rules['yang_harus_dibayarkan'] = 'nullable|numeric';
            
        } elseif ($jenisPembiayaan === 'PO Financing') {
            $rules['details'] = 'required|array|min:1';
            if($request->sumber_pembiayaan === 'eksternal'){
                $rules['id_instansi'] = 'required|integer';
            }else{
                $rules['id_instansi'] = 'nullable';
            }
            $rules['no_kontrak'] = 'nullable|string';
            $rules['lampiran_sid'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['nilai_kol'] = 'nullable|string';
            $rules['sumber_pembiayaan'] = 'required|in:eksternal,internal';
            $rules['tujuan_pembiayaan'] = 'nullable|string';
            $rules['total_pinjaman'] = 'nullable';
            $rules['harapan_tanggal_pencairan'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['rencana_tgl_pembayaran'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['pembayaran_total'] = 'nullable';
        } elseif ($jenisPembiayaan === 'Factoring') {
            $rules['details'] = 'required|array|min:1';
            $rules['total_nominal_yang_dialihkan'] = 'nullable|numeric';
            $rules['harapan_tanggal_pencairan'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['total_bagi_hasil'] = 'nullable';
            $rules['rencana_tgl_pembayaran'] = 'nullable|date_format:Y-m-d|sometimes';
            $rules['pembayaran_total'] = 'nullable';

        } else {
            // Default case for other financing types
            $rules['invoices'] = 'nullable|string';
            $rules['details'] = 'required|array|min:1';
        }

        $validated = $request->validate($rules);

        if($validated['jenis_pembiayaan'] === 'Factoring' || $validated['jenis_pembiayaan'] === 'Installment'){
            $validated['id_instansi'] = null;
            $validated['sumber_pembiayaan'] = null;
        }

        // Normalize date inputs that may come as d/m/Y
        $normalizeDate = function($value) {
            if (empty($value)) return null;
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                [$d, $m, $y] = explode('/', $value);
                return "$y-$m-$d";
            }
            return $value;
        };

        $harapan = $normalizeDate($request->input('harapan_tanggal_pencairan', $validated['harapan_tanggal_pencairan'] ?? null));
        $rencana = $normalizeDate($request->input('rencana_tgl_pembayaran', $validated['rencana_tgl_pembayaran'] ?? null));

        // Handle file upload
        if ($request->hasFile('lampiran_sid')) {
            $path = $request->file('lampiran_sid')->store('peminjaman/lampiran', 'public');
            $validated['lampiran_sid'] = $path;
        } elseif (isset($validated['lampiran_sid'])) {
            // remove file key if not an upload
            unset($validated['lampiran_sid']);
        }

        // Merge normalized dates and current user
        if ($harapan) $validated['harapan_tanggal_pencairan'] = $harapan;
        if ($rencana) $validated['rencana_tgl_pembayaran'] = $rencana;
        $validated['total_pinjaman'] = 0; // will be computed later
        $validated['total_bagi_hasil'] = 0; // will be computed later
        $validated['created_by'] = auth()->id() ?? null;
        $validated['updated_by'] = auth()->id() ?? null;

        // Use different data keys based on jenis_pembiayaan
        if ($validated['jenis_pembiayaan'] === 'Invoice Financing') {
            $invoices = json_decode($request->input('invoices', '[]'), true);
            if (!is_array($invoices) || count($invoices) === 0) {
                return response()->json(['success'=>false,'message'=>'No invoices provided'], 422);
            }
        } else {
            $invoices = $details = $request->input('details', []);
        }

        if (empty($validated['status'])) $validated['status'] = 'Draft';

        DB::beginTransaction();
        try {
            $peminjaman = PengajuanPeminjaman::create($validated);

            // Generate nomor_peminjaman using the service with different codes based on jenis_pembiayaan
            $prefix = 'INV'; // default prefix
            switch ($validated['jenis_pembiayaan']) {
                case 'Invoice Financing':
                    $prefix = 'INV';
                    break;
                case 'PO Financing':
                    $prefix = 'PO';
                    break;
                case 'Installment':
                    $prefix = 'INS';
                    break;
                case 'Factoring':
                    $prefix = 'FAC';
                    break;
                default:
                    $prefix = 'INV';
                    break;
            }

            $peminjaman->nomor_peminjaman = (new PeminjamanNumberService())->generateNumber(
                $prefix,
                $peminjaman->created_at?->format('Ym')
            );
            $peminjaman->save();
            
            $sumPinjaman = 0;
            $sumBagi = 0;

            foreach ($invoices as $i => $inv) {
                // server-side validation per invoice/detail based on financing type
                $no = null;
                if ($validated['jenis_pembiayaan'] === 'Invoice Financing') {
                    $no = $inv['no_invoice'] ?? null;
                } elseif ($validated['jenis_pembiayaan'] === 'PO Financing' || $validated['jenis_pembiayaan'] === 'Factoring') {
                    $no = $inv['no_kontrak'] ?? null;
                } elseif ($validated['jenis_pembiayaan'] === 'Installment') {
                    $no = $inv['no_invoice'] ?? null; 
                }
                
                $nilai_invoice = floatval($inv['nilai_invoice'] ?? 0);
                $nilai_pinjaman = floatval($inv['nilai_pinjaman'] ?? 0);

                
                if($validated['jenis_pembiayaan'] === 'Invoice Financing'){
                    if (!$no || $nilai_pinjaman <= 0) {
                        $errorMsg = $validated['jenis_pembiayaan'] === 'Invoice Financing' ? 
                            "Invalid invoice at index {$i}" :
                            "Invalid detail at index {$i}";
                        throw new \Exception($errorMsg);
                    }
    
                    if ($nilai_pinjaman > $nilai_invoice) {
                        $fieldName = $validated['jenis_pembiayaan'] === 'Invoice Financing' ? 'invoice' : 'detail';
                        throw new \Exception("Nilai pinjaman cannot exceed nilai invoice for {$fieldName} {$no}");
                    }
                }

                $nilai_bagi = round($nilai_pinjaman * 0.02, 2);

                $dok_invoice_path = null;
                $dok_kontrak_path = null;
                $dok_so_path = null;
                $dok_bast_path = null;
                $dok_lainnya_path = null;

                if ($request->hasFile("files.{$i}.dokumen_invoice") || $request->hasFile("details.{$i}.dokumen_invoice")) {
                    $file = $request->hasFile("files.{$i}.dokumen_invoice") 
                        ? $request->file("files.{$i}.dokumen_invoice") 
                        : $request->file("details.{$i}.dokumen_invoice");
                    $dok_invoice_path = $file->store('peminjaman/invoices', 'public');
                }
                if ($request->hasFile("files.{$i}.dokumen_kontrak") || $request->hasFile("details.{$i}.dokumen_kontrak")) {
                    $file = $request->hasFile("files.{$i}.dokumen_kontrak") 
                        ? $request->file("files.{$i}.dokumen_kontrak") 
                        : $request->file("details.{$i}.dokumen_kontrak");
                    $dok_kontrak_path = $file->store('peminjaman/invoices', 'public');
                }
                if ($request->hasFile("files.{$i}.dokumen_so") || $request->hasFile("details.{$i}.dokumen_so")) {
                    $file = $request->hasFile("files.{$i}.dokumen_so") 
                        ? $request->file("files.{$i}.dokumen_so") 
                        : $request->file("details.{$i}.dokumen_so");
                    $dok_so_path = $file->store('peminjaman/invoices', 'public');
                }
                if ($request->hasFile("files.{$i}.dokumen_bast") || $request->hasFile("details.{$i}.dokumen_bast")) {
                    $file = $request->hasFile("files.{$i}.dokumen_bast") 
                        ? $request->file("files.{$i}.dokumen_bast") 
                        : $request->file("details.{$i}.dokumen_bast");
                    $dok_bast_path = $file->store('peminjaman/invoices', 'public');
                }
                if ($request->hasFile("files.{$i}.dokumen_lainnya") || $request->hasFile("details.{$i}.dokumen_lainnya")) {
                    $file = $request->hasFile("files.{$i}.dokumen_lainnya") 
                        ? $request->file("files.{$i}.dokumen_lainnya") 
                        : $request->file("details.{$i}.dokumen_lainnya");
                    $dok_lainnya_path = $file->store('peminjaman/invoices', 'public');
                }

                // Prepare base data for BuktiPeminjaman
                $buktiData = [
                    'id_pengajuan_peminjaman' => $peminjaman->id_pengajuan_peminjaman,
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
                    'dokumen_lainnya' => $dok_lainnya_path,
                    'created_by' => auth()->id() ?? null,
                ];

                // Set no_invoice or no_kontrak based on financing type
                if ($validated['jenis_pembiayaan'] === 'Invoice Financing' || $validated['jenis_pembiayaan'] === 'Installment') {
                    $buktiData['no_invoice'] = $no;
                } else {
                    $buktiData['no_kontrak'] = $no;
                }

                // Add conditional attributes based on jenis_pembayaran
                if ($validated['jenis_pembiayaan'] === 'PO Financing') {
                    $buktiData['no_kontrak'] = $inv['no_kontrak'] ?? null;
                    $buktiData['kontrak_date'] = $inv['kontrak_date'] ?? null;
                    $buktiData['nama_barang'] = $inv['nama_barang'] ?? null;
                } elseif ($validated['jenis_pembiayaan'] === 'Factoring') {
                    $buktiData['no_kontrak'] = $inv['no_kontrak'] ?? null;
                } elseif ($validated['jenis_pembiayaan'] === 'Installment') {
                    $buktiData['nama_barang'] = $inv['nama_barang'] ?? null;
                }

                BuktiPeminjaman::create($buktiData);

                $sumPinjaman += $nilai_pinjaman;
                $sumBagi += $nilai_bagi;
            }

            // update header totals based on jenis_pembiayaan
            $manualTotal = $request->input('total_pinjaman');
            
            if ($validated['jenis_pembiayaan'] === 'Installment') {
                // Special calculation for Installment
                $totalPinjaman = $manualTotal ? floatval(preg_replace('/[^0-9\.]/', '', $manualTotal)) : $sumPinjaman;
                $tenor = (int) ($request->input('tenor_pembayaran') ?? 3);
                
                // Business rules: bagi hasil = 10% of total pinjaman
                $persentaseBagi = 10.0000; // stored as percent value (10.0000 => 10%)
                $totalBagiHasil = round($totalPinjaman * ($persentaseBagi / 100), 2);
                
                // PPS = 40% of bagi hasil, S Finance = 60% of bagi hasil
                $ppsAmount = round($totalBagiHasil * 0.40, 2);
                $sfinanceAmount = round($totalBagiHasil * 0.60, 2);
                
                $totalPembayaran = round($totalPinjaman + $totalBagiHasil, 2);
                $monthlyPay = $tenor > 0 ? round($totalPembayaran / $tenor, 2) : $totalPembayaran;
                
                $peminjaman->total_pinjaman = $totalPinjaman;
                $peminjaman->total_bagi_hasil = $totalBagiHasil;
                $peminjaman->pembayaran_total = $totalPembayaran;
                
                // Store additional installment-specific fields
                $peminjaman->tenor_pembayaran = $tenor;
                $peminjaman->persentase_bagi_hasil = $persentaseBagi;
                $peminjaman->pps = $ppsAmount;
                $peminjaman->s_finance = $sfinanceAmount;
                $peminjaman->yang_harus_dibayarkan = $monthlyPay;
            } else {
                // Standard calculation for other financing types
                if ($manualTotal) {
                    // try to normalize numeric from formatted input (remove non-digits except dot)
                    $manualClean = preg_replace('/[^0-9\.]/', '', $manualTotal);
                    $manualValue = floatval($manualClean);
                    $peminjaman->total_pinjaman = $manualValue;
                    // compute bagi hasil using same rate as invoice rows (2%)
                    $peminjaman->total_bagi_hasil = round($manualValue * 0.02, 2);
                    $peminjaman->pembayaran_total = $peminjaman->total_pinjaman + $peminjaman->total_bagi_hasil;
                } else {
                    $peminjaman->total_pinjaman = $sumPinjaman;
                    $peminjaman->total_bagi_hasil = $sumBagi;
                    $peminjaman->pembayaran_total = $sumPinjaman + $sumBagi;
                }
            }
            $peminjaman->save();

            DB::commit();
            return response()->json(['success' => true, 'id' => $peminjaman->id_pengajuan_peminjaman], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    function approval(Request $request, $id)
    {
        $peminjaman = PengajuanPeminjaman::find($id);
        if (!$peminjaman) {
            return response()->json(['success' => false, 'message' => 'Peminjaman not found'], 404);
        }

        // Get the status from request
        $status = $request->input('status');
        $action = $request->input('action', '');
        
        // Validate status
        $validStatuses = [
            'Submit Dokumen', 
            'Dokumen Tervalidasi', 
            'Validasi Ditolak', 
            'Dana Dicairkan',
            'Dana Sudah Dicairkan',
            'Debitur Setuju',
            'Pengajuan Ditolak Debitur',
            'Disetujui oleh CEO SKI',
            'Ditolak oleh CEO SKI',
            'Disetujui oleh Direktur SKI',
            'Ditolak oleh Direktur SKI',
            'Generate Kontrak'
        ];
        if (!in_array($status, $validStatuses)) {
            return response()->json(['success' => false, 'message' => 'Status tidak valid'], 400);
        }

        DB::beginTransaction();
        try {
            // Update peminjaman status
            $peminjaman->status = $status;
            $peminjaman->save();

            // Create history record
            $historyData = [
                'id_pengajuan_peminjaman' => $peminjaman->id_pengajuan_peminjaman,
                'id_config_matrix_peminjaman' => $request->input('id_config_matrix_peminjaman'),
                'date' => $request->input('date') ?: now()->format('Y-m-d'),
                'status' => $status,
            ];

            if ($status === 'Submit Dokumen') {   
                $historyData['submit_step1_by'] = auth()->id();
                $historyData['current_step'] = 2;
            }

            // Handle approval-specific data

            if ($status === 'Dokumen Tervalidasi') {
                $historyData['validasi_dokumen'] = 'disetujui';
                $historyData['approve_by'] = auth()->id();
                $historyData['deviasi'] = $request->input('deviasi');

                $nominalDisetujui = $request->input('nominal_yang_disetujui');
                // Remove Rp, spaces, and dots (thousands separator), keep only numbers
                $nominalDisetujui = preg_replace('/[Rp\s\.]/', '', $nominalDisetujui);
                // Remove any remaining non-numeric characters except commas (decimal separator)
                $nominalDisetujui = preg_replace('/[^0-9,]/', '', $nominalDisetujui);
                // Replace comma with dot for proper decimal parsing if exists
                $nominalDisetujui = str_replace(',', '.', $nominalDisetujui);
                $historyData['nominal_yang_disetujui'] = floatval($nominalDisetujui);
                
                $tanggalPencairan = $request->input('tanggal_pencairan');
                if ($tanggalPencairan) {
                    try {
                        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggalPencairan)) {
                            $historyData['tanggal_pencairan'] = Carbon::createFromFormat('d/m/Y', $tanggalPencairan)->format('Y-m-d');
                        } else {
                            $historyData['tanggal_pencairan'] = Carbon::parse($tanggalPencairan)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $historyData['tanggal_pencairan'] = null;
                    }
                } else {
                    $historyData['tanggal_pencairan'] = null;
                }
                
                $historyData['catatan_validasi_dokumen_disetujui'] = $request->input('catatan_validasi_dokumen_disetujui');
                $historyData['current_step'] = 3;
            } elseif ($status === 'Validasi Ditolak') {
                $historyData['validasi_dokumen'] = 'ditolak';
                $historyData['reject_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_ditolak'] = $request->input('catatan_validasi_dokumen_ditolak');
                $historyData['current_step'] = 1; // Reset to step 1 when validation is rejected
            } elseif ($status === 'Debitur Setuju') {

                $historyData['approve_by'] = auth()->id();
                $nominalDisetujui = $request->input('nominal_yang_disetujui');
                // Remove Rp, spaces, and dots (thousands separator), keep only numbers
                $nominalDisetujui = preg_replace('/[Rp\s\.]/', '', $nominalDisetujui);
                // Remove any remaining non-numeric characters except commas (decimal separator)
                $nominalDisetujui = preg_replace('/[^0-9,]/', '', $nominalDisetujui);
                // Replace comma with dot for proper decimal parsing if exists
                $nominalDisetujui = str_replace(',', '.', $nominalDisetujui);
                $historyData['nominal_yang_disetujui'] = floatval($nominalDisetujui);

                $tanggalPencairan = $request->input('tanggal_pencairan');
                if ($tanggalPencairan) {
                    try {
                        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggalPencairan)) {
                            $historyData['tanggal_pencairan'] = Carbon::createFromFormat('d/m/Y', $tanggalPencairan)->format('Y-m-d');
                        } else {
                            $historyData['tanggal_pencairan'] = Carbon::parse($tanggalPencairan)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $historyData['tanggal_pencairan'] = null;
                    }
                } else {
                    $historyData['tanggal_pencairan'] = null;
                }


                $historyData['catatan_persetujuan_debitur'] = $request->input('catatan_persetujuan_debitur');
                $historyData['current_step'] = 4;
            } elseif ($status === 'Pengajuan Ditolak Debitur') {
                $historyData['reject_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_ditolak'] = $request->input('catatan_persetujuan_debitur');
                $historyData['current_step'] = 8;
            } elseif ($status === 'Disetujui oleh CEO SKI') {
                $historyData['approve_by'] = auth()->id();
                
                $nominalDisetujui = $request->input('nominal_yang_disetujui');
                // Remove Rp, spaces, and dots (thousands separator), keep only numbers
                $nominalDisetujui = preg_replace('/[Rp\s\.]/', '', $nominalDisetujui);
                // Remove any remaining non-numeric characters except commas (decimal separator)
                $nominalDisetujui = preg_replace('/[^0-9,]/', '', $nominalDisetujui);
                // Replace comma with dot for proper decimal parsing if exists
                $nominalDisetujui = str_replace(',', '.', $nominalDisetujui);
                $historyData['nominal_yang_disetujui'] = floatval($nominalDisetujui);
                
                $tanggalPencairan = $request->input('tanggal_pencairan');
                if ($tanggalPencairan) {
                    try {
                        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggalPencairan)) {
                            $historyData['tanggal_pencairan'] = Carbon::createFromFormat('d/m/Y', $tanggalPencairan)->format('Y-m-d');
                        } else {
                            $historyData['tanggal_pencairan'] = Carbon::parse($tanggalPencairan)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $historyData['tanggal_pencairan'] = null;
                    }
                } else {
                    $historyData['tanggal_pencairan'] = null;
                }
                
                $historyData['catatan_validasi_dokumen_disetujui'] = $request->input('catatan_persetujuan_ceo');
                
                // Check nominal untuk menentukan current_step
                if ($historyData['nominal_yang_disetujui'] < 300000000) {
                    $historyData['current_step'] = 6; // Langsung ke step 6 jika < 300 juta
                } else {
                    $historyData['current_step'] = 5; // Ke step 5 (Direktur Approval) jika >= 300 juta
                }
            } elseif ($status === 'Ditolak oleh CEO SKI') {
                $historyData['reject_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_ditolak'] = $request->input('catatan_persetujuan_ceo');
                $historyData['current_step'] = 1;
            } elseif ($status === 'Disetujui oleh Direktur SKI') {
                $historyData['approve_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_disetujui'] = $request->input('catatan_persetujuan_direktur');
                $historyData['current_step'] = 6;
            } elseif ($status === 'Ditolak oleh Direktur SKI') {
                $historyData['reject_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_ditolak'] = $request->input('catatan_persetujuan_direktur');
                $historyData['current_step'] = 8;
            } elseif ($status === 'Generate Kontrak') {
                $historyData['approve_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_disetujui'] = $request->input('catatan') ?? 'Kontrak berhasil digenerate';
                $historyData['current_step'] = 7;
            } elseif ($status === 'Dana Sudah Dicairkan') {
                // Handle file upload for dokumen transfer
                if ($request->hasFile('dokumen_transfer')) {
                    $path = $request->file('dokumen_transfer')->store('peminjaman/bukti_transfer', 'public');
                    $peminjaman->upload_bukti_transfer = $path;
                    $peminjaman->save();
                }

                $historyData['approve_by'] = auth()->id();
                $historyData['current_step'] = 8;
            }

            HistoryStatusPengajuanPinjaman::create($historyData);

            DB::commit();

            // Return success response with appropriate message
            $message = $this->getStatusMessage($status, $action);
            return response()->json([
                'success' => true, 
                'message' => $message,
                'status' => $peminjaman->status,
                'current_step' => $historyData['current_step'] ?? null,
                'data' => [
                    'id' => $peminjaman->id_pengajuan_peminjaman,
                    'status' => $peminjaman->status,
                    'current_step' => $historyData['current_step'] ?? null,
                    'approved_by' => $peminjaman->approved_by ?? null,
                    'rejected_by' => $peminjaman->rejected_by ?? null,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approval Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan saat memproses approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appropriate message based on status and action
     */
    private function getStatusMessage($status, $action)
    {
        $messages = [
            'Submit Dokumen' => 'Dokumen berhasil disubmit untuk review!',
            'Dokumen Tervalidasi' => 'Dokumen berhasil divalidasi',
            'Validasi Ditolak' => 'Dokumen ditolak. Silakan perbaiki dan submit ulang.',
            'Pengajuan Ditolak Debitur' => 'Pengajuan Anda ditolak oleh debitur.',
            'Debitur Setuju' => 'Debitur telah menyetujui pengajuan Anda.',
            'Disetujui oleh CEO SKI' => 'Pengajuan telah disetujui oleh CEO SKI.',
            'Ditolak oleh CEO SKI' => 'Pengajuan Anda ditolak oleh CEO SKI.',
            'Disetujui oleh Direktur SKI' => 'Pengajuan telah disetujui oleh Direktur SKI.',
            'Ditolak oleh Direktur SKI' => 'Pengajuan Anda ditolak oleh Direktur SKI.',
            'Generate Kontrak' => 'Kontrak berhasil digenerate.',
            'Dana Sudah Dicairkan' => 'Dokumen transfer berhasil diupload.',
        ];

        return $messages[$status] ?? 'Status berhasil diupdate!';
    }

    /**
     * Get history detail by ID
     *
     * @param  int  $historyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoryDetail($historyId)
    {
        try {
            $history = HistoryStatusPengajuanPinjaman::with(['approvedBy', 'rejectedBy', 'submittedBy'])
                ->find($historyId);

            $historyNominal = HistoryStatusPengajuanPinjaman::where('id_history_status_pengajuan_pinjaman', $historyId)->where('status', 'Dokumen Tervalidasi')->latest()->first();

            if (!$history) {
                return response()->json([
                    'success' => false,
                    'message' => 'History tidak ditemukan'
                ], 404);
            }

            // Format the history data for response
            $historyData = [
                'id_history_status_pengajuan_pinjaman' => $history->id_history_status_pengajuan_pinjaman,
                'id_pengajuan_peminjaman' => $history->id_pengajuan_peminjaman,
                'status' => $history->status,
                'nominal_yang_disetujui' => $historyNominal ? $historyNominal->nominal_yang_disetujui : null,
                'tanggal_pencairan' => $historyNominal ? $historyNominal->tanggal_pencairan : null,
                'catatan_validasi_dokumen_disetujui' => $history->catatan_validasi_dokumen_disetujui,
                'catatan_validasi_dokumen_ditolak' => $history->catatan_validasi_dokumen_ditolak,
                'devisasi' => $history->devisasi,
                'date' => $history->date,
                'created_at' => $history->created_at,
                'updated_at' => $history->updated_at,
                // User information
                'approved_by' => $history->approvedBy ? [
                    'id' => $history->approvedBy->id,
                    'name' => $history->approvedBy->name,
                    'email' => $history->approvedBy->email,
                ] : null,
                'rejected_by' => $history->rejectedBy ? [
                    'id' => $history->rejectedBy->id,
                    'name' => $history->rejectedBy->name,
                    'email' => $history->rejectedBy->email,
                ] : null,
                'submitted_by' => $history->submittedBy ? [
                    'id' => $history->submittedBy->id,
                    'name' => $history->submittedBy->name,
                    'email' => $history->submittedBy->email,
                ] : null,
            ];

            return response()->json([
                'success' => true,
                'message' => 'History detail berhasil diambil',
                'history' => $historyData
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Get History Detail Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status of pengajuan peminjaman
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive($id)
    {
        try {
            $pengajuan = PengajuanPeminjaman::findOrFail($id);
            
            // Toggle status
            $newStatus = $pengajuan->is_active === 'active' ? 'non active' : 'active';
            $pengajuan->is_active = $newStatus;
            $pengajuan->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah menjadi ' . $newStatus,
                'is_active' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}
