<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use App\Services\ArPerbulanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PengembalianPinjamanRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class PengembalianPinjamanController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();

        if (!$debitur) {
            return view('livewire.pengembalian-pinjaman.create', [
                'pengajuanPeminjaman' => collect([]),
                'namaPerusahaan' => Auth::user()->name ?? '',
            ]);
        }

        $pengajuanPeminjaman = PengajuanPeminjaman::where('id_debitur', $debitur->id_debitur)
            ->where('status', 'Dana Sudah Dicairkan')
            ->with(['buktiPeminjaman' => function ($query) {
                $query->select('id_bukti_peminjaman', 'id_pengajuan_peminjaman', 'no_invoice', 'no_kontrak', 'nilai_invoice', 'nilai_pinjaman', 'nilai_bagi_hasil', 'due_date')
                    ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('due_date', 'asc');
            }])
            ->select('id_pengajuan_peminjaman', 'nomor_peminjaman', 'jenis_pembiayaan', 'total_pinjaman', 'total_bagi_hasil', 'harapan_tanggal_pencairan', 'tenor_pembayaran', 'yang_harus_dibayarkan')
            ->get()
            ->map(function ($item) {
                $pengembalianTerakhir = PengembalianPinjaman::where('id_pengajuan_peminjaman', $item->id_pengajuan_peminjaman)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($pengembalianTerakhir) {
                    $item->total_pinjaman = $pengembalianTerakhir->sisa_bayar_pokok;
                    $item->total_bagi_hasil = $pengembalianTerakhir->sisa_bagi_hasil;
                }

                $jenisPembiayaan = $item->jenis_pembiayaan;

                // Map invoice/kontrak yang belum lunas untuk dropdown pembayaran
                $item->invoices_json = $item->buktiPeminjaman->map(function ($b) use ($jenisPembiayaan, $item) {
                    $bagiHasil = (float) $b->nilai_bagi_hasil;

                    // Tentukan field yang digunakan berdasarkan jenis pembiayaan
                    if ($jenisPembiayaan === 'Invoice Financing') {
                        $labelField = $b->no_invoice;
                        $nilaiAsli = (float) $b->nilai_pinjaman;
                    } elseif (in_array($jenisPembiayaan, ['PO Financing', 'Factoring'])) {
                        $labelField = $b->no_kontrak;
                        $nilaiAsli = (float) $b->nilai_pinjaman;
                    } else {
                        $labelField = $b->no_invoice ?? $b->no_kontrak;
                        $nilaiAsli = (float) $b->nilai_pinjaman;
                    }

                    // Rumus: Total yang harus dibayar = Pokok + Bagi Hasil
                    $totalHarusDibayar = $nilaiAsli + $bagiHasil;

                    // Hitung total yang sudah dibayar dari detail pembayaran (pengembalian_invoice)
                    $totalDibayar = \App\Models\PengembalianInvoice::whereHas('pengembalian', function ($q) use ($item, $labelField) {
                        $q->where('id_pengajuan_peminjaman', $item->id_pengajuan_peminjaman)
                            ->where('invoice_dibayarkan', $labelField);
                    })->sum('nominal_yg_dibayarkan');

                    $sisaNominal = $totalHarusDibayar - $totalDibayar;

                    // Skip invoice/kontrak yang sudah lunas
                    if ($sisaNominal <= 0) {
                        return null;
                    }

                    return [
                        'id' => $b->id_bukti_peminjaman,
                        'label' => $labelField,
                        'nilai' => $sisaNominal,
                        'nilai_asli' => $totalHarusDibayar,
                        'sudah_dibayar' => $totalDibayar,
                        'due_date' => $b->due_date,
                    ];
                })->filter()->values()->toArray();

                // Tambahan data Installment
                if ($jenisPembiayaan === 'Installment') {
                    $item->tenor_pembayaran_value = $item->tenor_pembayaran;
                    $item->yang_harus_dibayarkan_value = round($item->yang_harus_dibayarkan ?? 0);                    // Ambil tanggal pencairan dari history
                    $historyPencairan = \App\Models\HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $item->id_pengajuan_peminjaman)
                        ->whereNotNull('tanggal_pencairan')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $item->tanggal_pencairan_real = $historyPencairan->tanggal_pencairan ?? $item->harapan_tanggal_pencairan;
                }

                return $item;
            });

        $namaPerusahaan = $debitur->nama ?? Auth::user()->name;

        return view('livewire.pengembalian-pinjaman.create_old', compact('pengajuanPeminjaman', 'namaPerusahaan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PengembalianPinjamanRequest $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validated();
                $pengembalianInvoices = $validated['pengembalian_invoices'] ?? [];

                // 1. Fetch & Prepare Data
                $pengajuan = PengajuanPeminjaman::findOrFail($validated['kode_peminjaman']);
                $tanggalPencairan = $this->parseDate($validated['tanggal_pencairan']);

                // 2. Create Parent Record
                $pengembalian = PengembalianPinjaman::create([
                    'id_pengajuan_peminjaman' => $validated['kode_peminjaman'],
                    'nama_perusahaan'         => $validated['nama_perusahaan'],
                    'nomor_peminjaman'        => $pengajuan->nomor_peminjaman,
                    'total_pinjaman'          => $validated['total_pinjaman'],
                    'total_bagi_hasil'        => $validated['total_bagi_hasil'],
                    'tanggal_pencairan'       => $tanggalPencairan,
                    'lama_pemakaian'          => $validated['lama_pemakaian'],
                    'nominal_invoice'         => $validated['nominal_invoice'],
                    'invoice_dibayarkan'      => $validated['invoice_dibayarkan'],
                    'bulan_pembayaran'        => $validated['bulan_pembayaran'] ?? null,
                    'yang_harus_dibayarkan'   => $validated['yang_harus_dibayarkan'] ?? null,
                    'sisa_bayar_pokok'        => $validated['sisa_utang'],
                    'sisa_bagi_hasil'         => $validated['sisa_bagi_hasil'],
                    'catatan'                 => $validated['catatan'] ?? null,
                    'status'                  => $this->determineStatus($validated['sisa_utang'], $validated['sisa_bagi_hasil']),
                ]);

                // 3. Process Invoices & Reports
                $this->processInvoices($pengembalian, $pengembalianInvoices, $validated, $pengajuan);

                // 4. Update AR
                app(ArPerbulanService::class)->updateAROnPengembalian($validated['kode_peminjaman'], now());

                return Response::success([
                    'redirect' => route('pengembalian.index')
                ], 'Data pengembalian berhasil disimpan');
            } catch (\Exception $e) {
                // DB::transaction will auto-rollback on exception
                throw $e;
            }
        });
    }

    public function exportPdf(Request $request)
    {
        $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();

        if (!$debitur) {
            abort(404);
        }

        // Ambil data sama seperti di DataTable (hanya record terakhir per nomor_peminjaman + invoice_dibayarkan)
        $latestRecords = \DB::table('pengembalian_pinjaman as pp1')
            ->select('pp1.ulid')
            ->joinSub(
                \DB::table('pengembalian_pinjaman')
                    ->select('nomor_peminjaman', 'invoice_dibayarkan', \DB::raw('MAX(created_at) as max_created'))
                    ->whereIn('id_pengajuan_peminjaman', function ($subQuery) use ($debitur) {
                        $subQuery->select('id_pengajuan_peminjaman')
                            ->from('pengajuan_peminjaman')
                            ->where('id_debitur', $debitur->id_debitur);
                    })
                    ->groupBy('nomor_peminjaman', 'invoice_dibayarkan'),
                'latest',
                function ($join) {
                    $join->on('pp1.nomor_peminjaman', '=', 'latest.nomor_peminjaman')
                        ->on('pp1.invoice_dibayarkan', '=', 'latest.invoice_dibayarkan')
                        ->on('pp1.created_at', '=', 'latest.max_created');
                }
            )
            ->pluck('ulid');

        $data = PengembalianPinjaman::with(['pengembalianInvoices'])
            ->whereIn('ulid', $latestRecords)
            ->orderByDesc('created_at')
            ->get();

        $html = view('exports.pengembalian-pinjaman-index-pdf', [
            'items' => $data,
        ])->render();

        $fileName = 'Pengembalian_Pinjaman_' . now()->format('Ymd') . '.pdf';

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }

    private function parseDate($dateString)
    {
        try {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        }
    }

    private function determineStatus($sisaUtang, $sisaBagiHasil)
    {
        return ($sisaUtang <= 0 && $sisaBagiHasil <= 0) ? 'Lunas' : 'Belum Lunas';
    }

    private function processInvoices($pengembalian, $invoices, $validated, $pengajuan)
    {
        $dueDate = $this->getDueDate($pengajuan, $validated['invoice_dibayarkan']);
        $hariKeterlambatan = $this->calculateHariKeterlambatan($dueDate);
        $totalBulanPemakaian = $this->convertHariToBulan($validated['lama_pemakaian']);

        foreach ($invoices as $itemObject) {
            $item = (array) $itemObject;

            \App\Models\PengembalianInvoice::create([
                'id_pengembalian'       => $pengembalian->ulid,
                'nominal_yg_dibayarkan' => $item['nominal'],
                'bukti_pembayaran'      => $item['file'] ?? null
            ]);

            \App\Models\ReportPengembalian::create([
                'id_pengembalian'          => $pengembalian->ulid,
                'nomor_peminjaman'         => $pengembalian->nomor_peminjaman,
                'nomor_invoice'            => $validated['invoice_dibayarkan'],
                'due_date'                 => $dueDate,
                'hari_keterlambatan'       => $hariKeterlambatan,
                'total_bulan_pemakaian'    => $totalBulanPemakaian,
                'nilai_total_pengembalian' => $item['nominal']
            ]);
        }
    }

    private function getDueDate($pengajuan, $invoiceDibayarkan)
    {
        if ($pengajuan->jenis_pembiayaan === 'Installment') {
            return null;
        }

        $query = \App\Models\BuktiPeminjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman);

        if ($pengajuan->jenis_pembiayaan === 'Invoice Financing') {
            $query->where('no_invoice', $invoiceDibayarkan);
        } else {
            $query->where('no_kontrak', $invoiceDibayarkan);
        }

        return $query->value('due_date');
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $pengembalian = PengembalianPinjaman::where('ulid', $id)->firstOrFail();
            return Response::success($pengembalian, 'Data pengembalian berhasil diambil');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PengembalianPinjamanRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $pengembalian = PengembalianPinjaman::where('ulid', $id)->firstOrFail();

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

            $pengembalian->update([
                'nama_perusahaan' => $validated['nama_perusahaan'],
                'total_pinjaman' => $validated['total_pinjaman'],
                'total_bagi_hasil' => $validated['total_bagi_hasil'],
                'tanggal_pencairan' => $tanggalPencairan,
                'lama_pemakaian' => $validated['lama_pemakaian'],
                'nominal_invoice' => $validated['nominal_invoice'],
                'invoice_dibayarkan' => $validated['invoice_dibayarkan'],
                'sisa_bayar_pokok' => $validated['sisa_utang'],
                'sisa_bagi_hasil' => $validated['sisa_bagi_hasil'],
                'catatan' => $validated['catatan'] ?? null,
                'status' => $status,
            ]);

            DB::commit();

            return Response::success(null, 'Data pengembalian berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}

    private function calculateHariKeterlambatan($dueDate)
    {
        if (!$dueDate) {
            return '0 Hari';
        }

        $dueDateCarbon = \Carbon\Carbon::parse($dueDate);
        $now = \Carbon\Carbon::now();

        if ($now->lte($dueDateCarbon)) {
            return '0 Hari';
        }

        $diff = $now->diff($dueDateCarbon);

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = $diff->y . ' Tahun';
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m . ' Bulan';
        }
        if ($diff->d > 0) {
            $parts[] = $diff->d . ' Hari';
        }

        return !empty($parts) ? implode(' ', $parts) : '0 Hari';
    }

    private function convertHariToBulan($hari)
    {
        if ($hari <= 0) {
            return '0 Bulan';
        }

        $tahun = floor($hari / 365);
        $sisaHari = $hari % 365;
        $bulan = floor($sisaHari / 30);
        $hari = $sisaHari % 30;

        $parts = [];
        if ($tahun > 0) {
            $parts[] = $tahun . ' Tahun';
        }
        if ($bulan > 0) {
            $parts[] = $bulan . ' Bulan';
        }
        if ($hari > 0) {
            $parts[] = $hari . ' Hari';
        }

        return !empty($parts) ? implode(' ', $parts) : '0 Hari';
    }
}
