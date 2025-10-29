<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use App\Models\BuktiPeminjaman;
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
            'nama_perusahaan' => $header->debitur->nama_debitur ?? '',
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
        ];

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
     * Display the create peminjaman page (form).
     */
    public function create()
    {
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
            $rules['id_instansi'] = 'required|integer';
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
            $rules['id_instansi'] = 'required|integer';
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

        if (empty($validated['status'])) $validated['status'] = 'submitted';

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
                    $buktiData['dokumen_lainnya'] = $inv['dokumen_lainnya'] ?? null;
                } elseif ($validated['jenis_pembiayaan'] === 'Installment') {
                    $buktiData['nama_barang'] = $inv['nama_barang'] ?? null;
                    $buktiData['dokumen_lainnya'] = $inv['dokumen_lainnya'] ?? null;
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
}
