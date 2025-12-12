<?php

namespace App\Http\Controllers\Peminjaman;

use App\Services\ArPerbulanService;
use Carbon\Carbon;
use App\Helpers\Response;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use App\Models\BuktiPeminjaman;
use App\Enums\JenisPembiayaanEnum;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanPeminjaman;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\MasterDebiturDanInvestor;
use App\Services\PeminjamanNumberService;
use App\Enums\PengajuanPeminjamanStatusEnum;
use App\Models\HistoryStatusPengajuanPinjaman;
use App\Models\MasterSumberPendanaanEksternal;
use App\Http\Requests\PengajuanPinjamanRequest;
use Illuminate\Http\UploadedFile;

class PeminjamanController extends Controller
{
    public function __construct()
    {
        $this->persentase_bagi_hasil = 2/100;
    }

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

        $persentase = $header->persentase_bagi_hasil ?? ($header->instansi?->persentase_bagi_hasil ?? null);

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
            's_finance' => $header->s_finance,
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

            if ($header->jenis_pembiayaan === 'PO Financing' || $header->jenis_pembiayaan === 'Factoring') {
                $baseData['kontrak_date'] = $bukti->kontrak_date;
            }
            
            if ($header->jenis_pembiayaan === 'PO Financing') {
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
    public function previewKontrak(Request $request, $id)
    {
        // Get pengajuan data from database with debitur relationship

        
        $pengajuan = PengajuanPeminjaman::with('debitur')
            ->where('id_pengajuan_peminjaman', $id)
            ->first();

        $no_kontrak_2 = $request->input('no_kontrak', null);
        
        if($no_kontrak_2 === null){
            $no_kontrak_2 = $pengajuan->no_kontrak ?? null;
        }
        
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
            'no_kontrak2' => $no_kontrak_2,
            'tanda_tangan' => $pengajuan->debitur->tanda_tangan ?? null,
        ];

        return view('livewire.peminjaman.preview-kontrak', compact('kontrak'));
    }

    /**
     * Display the peminjaman index page (list).
     */
    public function index()
    {
        // Get all pengajuan peminjaman with debitur and kol relationships
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
                    $master = MasterDebiturDanInvestor::where('email', $userEmail)
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
            'id_debitur' => 'required|string|size:26', // ULID format
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
                $rules['id_instansi'] = 'required|string|size:26'; // ULID format
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
                $rules['id_instansi'] = 'required|string|size:26'; // ULID format
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
                $rules['id_instansi'] = 'required|string|size:26'; // ULID format
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

        if($jenisPembiayaan === 'Installment'){
            $validated['id_instansi'] = null;
            $validated['sumber_pembiayaan'] = 'internal';
            $validated['persentase_bagi_hasil'] = 10;
        }
        
        if($jenisPembiayaan === 'Factoring'){
            $validated['id_instansi'] = null;
            $validated['sumber_pembiayaan'] = 'internal';
            $validated['persentase_bagi_hasil'] = 2;
        }

        DB::beginTransaction();
        try {
            // Handle file upload for lampiran_sid
            $lampiran_sid_path = $pengajuan->lampiran_sid;
            if ($request->hasFile('lampiran_sid')) {
                // Delete old file if exists
                if ($lampiran_sid_path && Storage::disk('public')->exists($lampiran_sid_path)) {
                    Storage::disk('public')->delete($lampiran_sid_path);
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
                'status' => 'Draft',
            ]);

            $historyPengajuan = HistoryStatusPengajuanPinjaman::create([
                'id_pengajuan_peminjaman' => $pengajuan->id_pengajuan_peminjaman,
                'status' => 'Draft',
                'current_step' => 1,
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

    public function store(PengajuanPinjamanRequest $request)
    {
        DB::beginTransaction();
        try {
            $allData = $request->validated();
            // Handle both 'form_data_invoice' (Invoice/PO) and 'details' (Factoring/Installment) keys
            $dataInvoice = collect($allData['form_data_invoice'] ?? $allData['details'] ?? []);
            unset($allData['form_data_invoice'], $allData['details']);
            $dataPengajuanPeminjaman = $allData;

            $dataPengajuanPeminjaman['status'] = PengajuanPeminjamanStatusEnum::DRAFT;

            $dataPengajuanPeminjaman['nomor_peminjaman'] = (new PeminjamanNumberService())->generateNumber(
                JenisPembiayaanEnum::getPrefix($dataPengajuanPeminjaman['jenis_pembiayaan']),
                now()->format('Ym')
            );

            if ($dataPengajuanPeminjaman['sumber_pembiayaan'] === 'Eksternal') {
                $instansi = MasterSumberPendanaanEksternal::find($dataPengajuanPeminjaman['id_instansi']);
                if ($instansi && $instansi->persentase_bagi_hasil) {
                    $this->persentase_bagi_hasil = (double) $instansi->persentase_bagi_hasil / 100;
                }
            }

            if ($dataPengajuanPeminjaman['jenis_pembiayaan'] == 'Installment') {
                $this->persentase_bagi_hasil = (double) 10/100;
            }

            $masterDebiturDanInvestor = MasterDebiturDanInvestor::where('email', auth()->user()->email)
            ->where('flagging', 'tidak')
            ->where('status', 'active')
            ->with('kol')
            ->first();

            $dataPengajuanPeminjaman['nama_bank'] = $masterDebiturDanInvestor->nama_bank;
            $dataPengajuanPeminjaman['no_rekening'] = $masterDebiturDanInvestor->no_rek;
            $dataPengajuanPeminjaman['nilai_kol'] = $masterDebiturDanInvestor->kol->kol;

            // Use values from frontend if provided, otherwise calculate
            if (!isset($dataPengajuanPeminjaman['total_pinjaman']) || empty($dataPengajuanPeminjaman['total_pinjaman'])) {
                // For Installment use nilai_invoice, for others use nilai_pinjaman
                if ($dataPengajuanPeminjaman['jenis_pembiayaan'] == 'Installment') {
                    $dataPengajuanPeminjaman['total_pinjaman'] = (double) $dataInvoice->sum(fn ($item) => (double) ($item['nilai_invoice'] ?? 0));
                } else {
                    $dataPengajuanPeminjaman['total_pinjaman'] = (double) $dataInvoice->sum(fn ($item) => (double) ($item['nilai_pinjaman'] ?? 0));
                }
            }

            // Set persentase_bagi_hasil for calculation (decimal) and storage (percentage)
            $persentaseForCalculation = 0;
            if ($dataPengajuanPeminjaman['jenis_pembiayaan'] == 'Installment') {
                if (!isset($dataPengajuanPeminjaman['persentase_bagi_hasil'])) {
                    $persentaseForCalculation = 0.10;
                    $dataPengajuanPeminjaman['persentase_bagi_hasil'] = 10; // Store as 10%
                } else {
                    $persentaseForCalculation = (double) $dataPengajuanPeminjaman['persentase_bagi_hasil'] / 100;
                    // persentase_bagi_hasil already in percentage form from frontend
                }
            } else {
                $persentaseForCalculation = $this->persentase_bagi_hasil;
                $dataPengajuanPeminjaman['persentase_bagi_hasil'] = $this->persentase_bagi_hasil * 100; // Store as percentage (5%, 2%, etc)
            }

            // Calculate total_bagi_hasil and pembayaran_total if not provided
            if (!isset($dataPengajuanPeminjaman['total_bagi_hasil']) || empty($dataPengajuanPeminjaman['total_bagi_hasil'])) {
                $dataPengajuanPeminjaman['total_bagi_hasil'] = $dataPengajuanPeminjaman['total_pinjaman'] * $persentaseForCalculation;
            }
            
            if (!isset($dataPengajuanPeminjaman['pembayaran_total']) || empty($dataPengajuanPeminjaman['pembayaran_total'])) {
                $dataPengajuanPeminjaman['pembayaran_total'] = (double) $dataPengajuanPeminjaman['total_pinjaman'] + $dataPengajuanPeminjaman['total_bagi_hasil'];
            }

            if ($dataPengajuanPeminjaman['jenis_pembiayaan'] == 'Installment') {
                // PPS = 40% of bagi hasil, S Finance = 60% of bagi hasil
                $dataPengajuanPeminjaman['pps'] = (double) $dataPengajuanPeminjaman['total_bagi_hasil'] * 0.40;
                $dataPengajuanPeminjaman['s_finance'] = (double) $dataPengajuanPeminjaman['total_bagi_hasil'] * 0.60;;
                $dataPengajuanPeminjaman['yang_harus_dibayarkan'] = (double) ((double) $dataPengajuanPeminjaman['pembayaran_total'] / (double) $dataPengajuanPeminjaman['tenor_pembayaran']);
                // Set fields that don't exist for Installment to NULL
                $dataPengajuanPeminjaman['harapan_tanggal_pencairan'] = null;
                $dataPengajuanPeminjaman['rencana_tgl_pembayaran'] = null;
            } else {
                $dataPengajuanPeminjaman['harapan_tanggal_pencairan'] = parseCarbonDate($dataPengajuanPeminjaman['harapan_tanggal_pencairan'])->format('Y-m-d');
                $dataPengajuanPeminjaman['rencana_tgl_pembayaran'] = parseCarbonDate($dataPengajuanPeminjaman['rencana_tgl_pembayaran'])->format('Y-m-d');
                // Set Installment-specific fields to NULL
                $dataPengajuanPeminjaman['tenor_pembayaran'] = null;
                $dataPengajuanPeminjaman['pps'] = null;
                $dataPengajuanPeminjaman['s_finance'] = null;
                $dataPengajuanPeminjaman['yang_harus_dibayarkan'] = null;
            }

            $userEmail = auth()->user()->email;
            $id_debitur = MasterDebiturDanInvestor::select('id_debitur')->where('email', $userEmail)->first()->id_debitur;
            $dataPengajuanPeminjaman['id_debitur'] = $id_debitur;

            if (isset($dataPengajuanPeminjaman['lampiran_sid']) && $dataPengajuanPeminjaman['lampiran_sid'] instanceof UploadedFile) {
                $dataPengajuanPeminjaman['lampiran_sid'] = Storage::disk('public')->put('lampiran_sid', $dataPengajuanPeminjaman['lampiran_sid']);
            }
            $dataPengajuanPeminjaman['created_by'] = auth()->user()->id;
            $dataPengajuanPeminjaman['updated_by'] = auth()->user()->id;

            $peminjaman = PengajuanPeminjaman::create($dataPengajuanPeminjaman);

            foreach ($dataInvoice as $i => $inv) {                
                // For Installment, nilai_pinjaman doesn't exist, skip calculation
                if ($dataPengajuanPeminjaman['jenis_pembiayaan'] !== JenisPembiayaanEnum::INSTALLMENT) {
                    $nilai_pinjaman = (double) ($inv['nilai_pinjaman'] ?? 0);
                    $nilai_bagi = (double) $nilai_pinjaman * (double) $this->persentase_bagi_hasil;
                    $inv['nilai_bagi_hasil'] = (double) $nilai_bagi;
                }

                $inv['id_pengajuan_peminjaman'] = $peminjaman->id_pengajuan_peminjaman;

                if (
                    in_array($dataPengajuanPeminjaman['jenis_pembiayaan'], [
                        JenisPembiayaanEnum::INVOICE_FINANCING,
                        JenisPembiayaanEnum::INSTALLMENT,
                    ])
                ) {
                    $inv['invoice_date'] = parseCarbonDate($inv['invoice_date'])->format('Y-m-d');
                } else {
                    $inv['kontrak_date'] = parseCarbonDate($inv['kontrak_date'])->format('Y-m-d');
                }

                if (isset($inv['due_date'])) {
                    $inv['due_date'] = parseCarbonDate($inv['due_date'])->format('Y-m-d');
                }

                foreach ([
                    'dokumen_invoice', 
                    'dokumen_kontrak', 
                    'dokumen_so', 
                    'dokumen_bast', 
                    'dokumen_lainnya'
                ] as $dokumen) {
                    if (isset($inv[$dokumen]) && $inv[$dokumen] instanceof UploadedFile) {
                        $inv[$dokumen] = Storage::disk('public')->put($dokumen, $inv[$dokumen]);
                    } else {
                        $inv[$dokumen] = null;
                    }
                }

                BuktiPeminjaman::create($inv);
            }

            DB::commit();
            return Response::success(null, 'Pengajuan pinjaman berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
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
            'Dana Sudah Dicairkan',
            'Debitur Setuju',
            'Pengajuan Ditolak Debitur',
            'Disetujui oleh CEO SKI',
            'Ditolak oleh CEO SKI',
            'Disetujui oleh Direktur SKI',
            'Ditolak oleh Direktur SKI',
            'Generate Kontrak',
            'Menunggu Konfirmasi Debitur',
            'Konfirmasi Ditolak Debitur'
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
                $historyData['deviasi'] = $request->input('deviasi');
            } elseif ($status === 'Pengajuan Ditolak Debitur') {
                $historyData['reject_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_ditolak'] = $request->input('catatan_persetujuan_debitur');
                $historyData['current_step'] = 9;
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
                $historyData['deviasi'] = $request->input('deviasi');
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
                $historyData['current_step'] = 2;
            } elseif ($status === 'Disetujui oleh Direktur SKI') {
                $historyData['approve_by'] = auth()->id();
                
                $nominalDisetujui = $request->input('nominal_yang_disetujui');
                // Remove Rp, spaces, and dots (thousands separator), keep only numbers
                $nominalDisetujui = preg_replace('/[Rp\s\.]/', '', $nominalDisetujui);
                // Remove any remaining non-numeric characters except commas (decimal separator)
                $nominalDisetujui = preg_replace('/[^0-9,]/', '', $nominalDisetujui);
                // Replace comma with dot for proper decimal parsing if exists
                $nominalDisetujui = str_replace(',', '.', $nominalDisetujui);
                $historyData['nominal_yang_disetujui'] = floatval($nominalDisetujui);
                $historyData['deviasi'] = $request->input('deviasi');
                
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
                
                $historyData['catatan_validasi_dokumen_disetujui'] = $request->input('catatan_persetujuan_direktur');
                $historyData['current_step'] = 6;
            } elseif ($status === 'Ditolak oleh Direktur SKI') {
                $historyData['reject_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_ditolak'] = $request->input('catatan_persetujuan_direktur');
                $historyData['current_step'] = 9;
            } elseif ($status === 'Generate Kontrak') {
                $historyData['approve_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_disetujui'] = $request->input('catatan') ?? 'Kontrak berhasil digenerate';
                $historyData['current_step'] = 7;

                $peminjaman->no_kontrak = $request->input('no_kontrak');
                $peminjaman->save();
            } elseif ($status === 'Menunggu Konfirmasi Debitur') {
                // Handle file upload for dokumen transfer
                if ($request->hasFile('dokumen_transfer')) {
                    $path = $request->file('dokumen_transfer')->store('peminjaman/bukti_transfer', 'public');
                    $peminjaman->upload_bukti_transfer = $path;
                    $peminjaman->save();
                }

                $historyData['approve_by'] = auth()->id();
                $historyData['current_step'] = 8;
            } elseif ($status === 'Konfirmasi Ditolak Debitur') {
                $historyData['reject_by'] = auth()->id();
                $historyData['catatan_validasi_dokumen_ditolak'] = $request->input('catatan_konfirmasi_debitur_ditolak');
                $historyData['current_step'] = 7;
            } elseif ($status === 'Dana Sudah Dicairkan') {
                $historyData['approve_by'] = auth()->id();
                $historyData['current_step'] = 9;
                
                app(ArPerbulanService::class)->updateAROnPencairan(
                    $peminjaman->id_debitur,
                    now()
                );
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
            Log::error('Approval Error: ' . $e->getMessage());
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
            'Menunggu Konfirmasi Debitur' => 'Upload bukti transfer berhasil. Menunggu konfirmasi dari debitur.',
            'Konfirmasi Ditolak Debitur' => 'Debitur menolak konfirmasi bukti transfer.',
            'Dana Sudah Dicairkan' => 'Debitur telah mengkonfirmasi bukti transfer.',
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

            $historyNominal = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $history->id_pengajuan_peminjaman)->where('status', 'Dokumen Tervalidasi')->latest()->first();

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
                'deviasi' => $historyNominal ? $historyNominal->deviasi : null,
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
            Log::error('Get History Detail Error: ' . $e->getMessage());
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

    /**
     * Download kontrak as PDF
     */
    public function downloadKontrak(Request $request, $id)
    {
        try {
            // Get pengajuan data
            $pengajuan = PengajuanPeminjaman::with('debitur')
                ->where('id_pengajuan_peminjaman', $id)
                ->first();
            
            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan peminjaman tidak ditemukan'
                ], 404);
            }

            // Get latest approved nominal from history
            $latestHistory = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $id)
                ->whereNotNull('nominal_yang_disetujui')
                ->orderBy('created_at', 'desc')
                ->first();

            // Get data from request
            $no_kontrak_input = $request->input('no_kontrak');
            $biaya_admin_input = $request->input('biaya_administrasi', 0);

            // Generate contract number
            $no_kontrak = $no_kontrak_input ?: 'SKI/FIN/' . date('Y') . '/' . str_pad($pengajuan->id_pengajuan_peminjaman, 3, '0', STR_PAD_LEFT);
            
            // Format biaya admin
            $biaya_admin_formatted = 'Rp. ' . number_format($biaya_admin_input, 0, ',', '.');

            // Prepare kontrak data
            $kontrak = [
                'id_peminjaman' => $id,
                'no_kontrak' => $no_kontrak,
                'no_kontrak2' => $no_kontrak,
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
                'biaya_admin' => $biaya_admin_formatted,
                'biaya_admin_raw' => $biaya_admin_input,
                'nisbah' => ($pengajuan->persentase_bagi_hasil ?? 2) . '% flat / bulan',
                'denda_keterlambatan' => '2% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut',
                'jaminan' => $pengajuan->jenis_pembiayaan ?? 'Invoice & Project Financing',
                'tanda_tangan' => $pengajuan->debitur->tanda_tangan ?? null,
            ];

            // Build custom HTML for PDF
            $html = $this->buildKontrakHTML($kontrak);

            // Generate PDF using DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'Kontrak_Peminjaman_' . str_replace('/', '_', $no_kontrak) . '_' . date('Ymd') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error generating PDF kontrak: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build custom HTML for PDF contract
     */
    private function buildKontrakHTML($kontrak)
    {
        $ttdKreditur = public_path('assets/img/ttd2.png');
        $ttdKrediturBase64 = '';
        if (file_exists($ttdKreditur)) {
            $ttdKrediturBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdKreditur));
        }

        $ttdDebitur = '';
        if (!empty($kontrak['tanda_tangan'])) {
            $ttdDebiturPath = storage_path('app/public/' . $kontrak['tanda_tangan']);
            if (file_exists($ttdDebiturPath)) {
                $ttdDebitur = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdDebiturPath));
            }
        }

        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kontrak Peminjaman</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .title {
            text-align: center;
            margin: 30px 0;
        }
        .title h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        .title h3 {
            font-size: 14px;
            color: #0066cc;
            margin: 5px 0;
        }
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        .section {
            margin: 20px 0;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table td {
            padding: 5px 8px;
            vertical-align: top;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-row {
            display: table;
            width: 100%;
        }
        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-box {
            min-height: 100px;
            margin: 20px 0;
        }
        .signature-img {
            max-height: 60px;
            max-width: 150px;
            margin-bottom: 10px;
        }
        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 10px auto;
        }
        .fw-bold {
            font-weight: bold;
        }
        .text-muted {
            color: #666;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        .mb-4 {
            margin-bottom: 20px;
        }
        .mb-5 {
            margin-bottom: 25px;
        }
        .ps-3 {
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>' . $kontrak['nama_perusahaan'] . '</h2>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>FINANCING CONTRACT</h1>
        <h3>No: ' . $kontrak['no_kontrak2'] . '</h3>
    </div>

    <!-- Content -->
    <div class="content">
        <p class="mb-4">Yang bertandatangan dibawah ini:</p>

        <!-- Pihak Pertama -->
        <div class="section">
            <p class="fw-bold mb-3">I. ' . $kontrak['nama_perusahaan'] . '</p>
            <p>
                suatu perusahaan yang mengelola treasury serta memberikan pelayanan private equity, yang
                berkedudukan di Bandung, beralamat di PermataKuningan Building 17th Floor, Kawasan
                Epicentrum, HR Rasuna Said, Jl. Kuningan Mulia, RT.6/RW.1, Menteng Atas, Setiabudi, South
                Jakarta City, Jakarta12920 ("Kreditur") dalam hal ini diwakili oleh S-FINANCE berkedudukan
                di Jakarta sebagai Pengelola Fasilitas yang menyalurkan dan mengelola transaksi-transaksi
                terkait Fasilitas Pembiayaan yang bertindak sebagai kuasa (selanjutnya disebut "Perseroan"), dan
            </p>
        </div>

        <!-- Pihak Kedua -->
        <div class="section mb-5">
            <p class="fw-bold mb-3">II. Debitur, sebagaimana dimaksud dalam Struktur dan Kontrak Pembiayaan ini</p>
            <p>
                Dengan ini sepakat untuk menetapkan hal-hal pokok, yang selanjutnya akan disebut sebagai
                "Struktur dan Kontrak Pembiayaan" sehubungan dengan Perjanjian Pembiayaan Project Dengan
                Cara Pencairan Dengan Pembayaran Secara Angsuran atau Kontan ini (selanjutnya disebut
                sebagai "Perjanjian"), sebagai berikut:
            </p>
        </div>

        <!-- Data Pembiayaan -->
        <div class="section mb-5">
            <table>
                <tr>
                    <td width="5%">1.</td>
                    <td width="35%">Jenis Pembiayaan</td>
                    <td width="60%">: ' . $kontrak['jenis_pembiayaan'] . '</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td class="fw-bold">Debitur</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">a. Nama Perusahaan</td>
                    <td>: ' . $kontrak['nama_debitur'] . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">b. Nama Pimpinan</td>
                    <td>: ' . $kontrak['nama_pimpinan'] . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">c. Alamat Perusahaan</td>
                    <td>: ' . $kontrak['alamat'] . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">d. Tujuan Pembiayaan</td>
                    <td>: ' . $kontrak['tujuan_pembiayaan'] . '</td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td class="fw-bold">Detail Pembiayaan</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">a. Nilai Pembiayaan</td>
                    <td class="fw-bold">: ' . $kontrak['nilai_pembiayaan'] . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">b. Hutang Pokok</td>
                    <td class="fw-bold">: ' . $kontrak['hutang_pokok'] . '</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>Tenor Pembiayaan</td>
                    <td>: ' . $kontrak['tenor'] . '</td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>Biaya Administrasi</td>
                    <td>: ' . $kontrak['biaya_admin'] . '</td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td>Bagi Hasil (Nisbah)</td>
                    <td>: ' . $kontrak['nisbah'] . '</td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td>Denda Keterlambatan</td>
                    <td>: ' . $kontrak['denda_keterlambatan'] . '</td>
                </tr>
                <tr>
                    <td>8.</td>
                    <td>Jaminan</td>
                    <td>: ' . $kontrak['jaminan'] . '</td>
                </tr>
                <tr>
                    <td>9.</td>
                    <td>Metode Pembiayaan</td>
                    <td>: Transfer</td>
                </tr>
            </table>
        </div>

        <!-- Penutup -->
        <div class="section mb-5">
            <p class="fw-bold mb-3">Penutup</p>
            <p>
                "Bahwa dengan menerima pembiayaan tersebut bersamaan dengan tanda tangan kami, maka segala
                tanggung jawab pengembalian pembiayaan akan kami tepati sesuai dengan plan paid yang telah
                kami buat sendiri yang tertera pada tabel diatas. Apabila terdapat keterlambatan pembayaran
                kami bersedia untuk dikenakan denda penalti hingga sanksi tidak dapat mengakses pembiayaan
                apapun yang terafiliasi dengan S Finance sebelum tanggung jawab pelunasan hutang terlebih
                dahulu kami selesaikan"
            </p>
        </div>

        <!-- Tanggal -->
        <div class="section mb-5">
            <p class="text-muted">Jakarta, ' . $kontrak['tanggal_kontrak'] . '</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-row">
                <div class="signature-col">
                    <p class="fw-bold">Kreditur</p>
                    <p>' . $kontrak['nama_perusahaan'] . '</p>
                    <div class="signature-box">';
        
        if ($ttdKrediturBase64) {
            $html .= '<img src="' . $ttdKrediturBase64 . '" class="signature-img" />';
        }
        
        $html .= '
                        <div class="signature-line"></div>
                        <p class="fw-bold">Muhamad Kurniawan</p>
                    </div>
                    <p class="text-muted">Director</p>
                </div>
                <div class="signature-col">
                    <p class="fw-bold">Debitur</p>
                    <p>' . $kontrak['nama_pimpinan'] . '</p>
                    <div class="signature-box">';
        
        if ($ttdDebitur) {
            $html .= '<img src="' . $ttdDebitur . '" class="signature-img" />';
        }
        
        $html .= '
                        <div class="signature-line"></div>
                        <p class="fw-bold">' . $kontrak['nama_pimpinan'] . '</p>
                    </div>
                    <p class="text-muted">Pimpinan</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';

        return $html;
    }
}
