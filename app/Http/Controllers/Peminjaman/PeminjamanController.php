<?php

namespace App\Http\Controllers\Peminjaman;

use App\Helpers\Response;
use App\Models\BuktiPeminjaman;
use App\Enums\JenisPembiayaanEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanPeminjaman;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\MasterDebiturDanInvestor;
use App\Services\PeminjamanNumberService;
use App\Enums\PengajuanPeminjamanStatusEnum;
use App\Models\HistoryStatusPengajuanPinjaman;
use App\Http\Requests\PengajuanPinjamanRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;

class PeminjamanController extends Controller
{
    public function __construct()
    {
        $this->persentase_bunga = 2 / 100;
        $this->middleware('can:peminjaman_dana.add')->only(['store']);
        $this->middleware('can:peminjaman_dana.edit')->only(['update']);
        $this->middleware('can:peminjaman_dana.active/non_active')->only(['toggleActive']);
    }

    /**
     * Tampilkan halaman preview kontrak.
     * Dipanggil melalui redirect dari KontrakPdfHandler::previewKontrak() di Livewire Detail.
     */
    public function previewKontrak(Request $request, $id)
    {
        $pengajuan = PengajuanPeminjaman::with('debitur')
            ->where('id_pengajuan_peminjaman', $id)
            ->first();

        if (!$pengajuan) {
            abort(404, 'Pengajuan peminjaman tidak ditemukan');
        }

        $no_kontrak_2 = $request->input('no_kontrak', $pengajuan->no_kontrak ?? null);

        $latestHistory = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $id)
            ->whereNotNull('nominal_yang_disetujui')
            ->orderBy('created_at', 'desc')
            ->first();

        $no_kontrak = $pengajuan->no_kontrak ?? ('SKI/FIN/' . date('Y') . '/' . str_pad($pengajuan->id_pengajuan_peminjaman, 3, '0', STR_PAD_LEFT));

        $kontrak = [
            'id_peminjaman'        => $id,
            'no_kontrak'           => $no_kontrak,
            'no_kontrak2'          => $no_kontrak_2,
            'tanggal_kontrak'      => now()->format('d F Y'),
            'nama_perusahaan'      => 'SYNNOVAC CAPITAL',
            'nama_debitur'         => $pengajuan->debitur->nama ?? 'N/A',
            'nama_pimpinan'        => $pengajuan->debitur->nama_ceo ?? 'N/A',
            'alamat'               => $pengajuan->debitur->alamat ?? 'N/A',
            'tujuan_pembiayaan'    => $pengajuan->tujuan_pembiayaan ?? 'N/A',
            'jenis_pembiayaan'     => $pengajuan->jenis_pembiayaan ?? 'Invoice Financing',
            'nilai_pembiayaan'     => 'Rp. ' . number_format($latestHistory->nominal_yang_disetujui ?? $pengajuan->total_pinjaman ?? 0, 0, ',', '.'),
            'hutang_pokok'         => 'Rp. ' . number_format($latestHistory->nominal_yang_disetujui ?? $pengajuan->total_pinjaman ?? 0, 0, ',', '.'),
            'tenor'                => ($pengajuan->tenor_pembayaran ?? 1) . ' Bulan',
            'biaya_admin'          => 'Rp. 0',
            'nisbah'               => ($pengajuan->persentase_bunga ?? 2) . '% flat / bulan',
            'denda_keterlambatan'  => ($pengajuan->persentase_bunga ?? 2) . '% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut',
            'jaminan'              => $pengajuan->jenis_pembiayaan ?? 'Invoice Financing',
            'tanda_tangan'         => $pengajuan->debitur->tanda_tangan ?? null,
        ];

        return view('livewire.pengajuan-pinjaman.preview-kontrak', compact('kontrak'));
    }

    /**
     * Generate dan download kontrak PDF.
     * Dipanggil via AJAX POST dari halaman preview-kontrak.
     */
    public function downloadKontrakPdf(Request $request, $id): \Illuminate\Http\Response
    {
        $this->authorize('peminjaman_dana.generate_kontrak');

        $pengajuan = PengajuanPeminjaman::with('debitur')
            ->where('id_pengajuan_peminjaman', $id)
            ->firstOrFail();

        $latestHistory = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $id)
            ->whereNotNull('nominal_yang_disetujui')
            ->orderBy('created_at', 'desc')
            ->first();

        $nilaiPembiayaan = $latestHistory->nominal_yang_disetujui ?? $pengajuan->total_pinjaman ?? 0;

        // Use the no_kontrak sent from the preview view (finalized contract number)
        $noKontrak = $request->input('no_kontrak', $pengajuan->no_kontrak ?? '');

        $kontrak = [
            'id_peminjaman'        => $id,
            'no_kontrak'           => $noKontrak,
            'no_kontrak2'          => $noKontrak,
            'tanggal_kontrak'      => now()->format('d F Y'),
            'nama_perusahaan'      => 'SYNNOVAC CAPITAL',
            'nama_debitur'         => $pengajuan->debitur->nama ?? 'N/A',
            'nama_pimpinan'        => $pengajuan->debitur->nama_ceo ?? 'N/A',
            'alamat'               => $pengajuan->debitur->alamat ?? 'N/A',
            'tujuan_pembiayaan'    => $pengajuan->tujuan_pembiayaan ?? 'N/A',
            'jenis_pembiayaan'     => $pengajuan->jenis_pembiayaan ?? 'Invoice Financing',
            'nilai_pembiayaan'     => 'Rp. ' . number_format($nilaiPembiayaan, 0, ',', '.'),
            'hutang_pokok'         => 'Rp. ' . number_format($nilaiPembiayaan, 0, ',', '.'),
            'tenor'                => ($pengajuan->tenor_pembayaran ?? 1) . ' Bulan',
            'biaya_admin'          => 'Rp. 0',
            'nisbah'               => ($pengajuan->persentase_bunga ?? 2) . '% flat / bulan',
            'denda_keterlambatan'  => ($pengajuan->persentase_bunga ?? 2) . '% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut',
            'jaminan'              => $pengajuan->jenis_pembiayaan ?? 'Invoice Financing',
            'tanda_tangan'         => $pengajuan->debitur->tanda_tangan ?? null,
        ];

        // Prepare base64-encoded signature images for the blade view
        $ttdKrediturPath = public_path('assets/img/ttd2.png');
        $ttdKrediturBase64 = file_exists($ttdKrediturPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($ttdKrediturPath))
            : '';

        $ttdDebiturBase64 = '';
        if (!empty($kontrak['tanda_tangan'])) {
            $ttdDebiturPath = storage_path('app/public/' . $kontrak['tanda_tangan']);
            if (file_exists($ttdDebiturPath)) {
                $ttdDebiturBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdDebiturPath));
            }
        }

        $pdf = Pdf::loadView('livewire.pengajuan-pinjaman.kontrak-pdf', compact(
            'kontrak',
            'ttdKrediturBase64',
            'ttdDebiturBase64'
        ))->setPaper('a4', 'portrait');

        $filename = 'Kontrak-' . preg_replace('/[\/\\\\]/', '-', $noKontrak ?: $id) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Update pengajuan peminjaman (dipanggil oleh Livewire Create via UniversalFormAction).
     */
    public function update(Request $request, $id)
    {
        $pengajuan = PengajuanPeminjaman::findOrFail($id);

        if (!in_array($pengajuan->status, ['Draft', 'Validasi Ditolak'])) {
            return Response::error(null, 'Pengajuan dengan status ' . $pengajuan->status . ' tidak dapat diedit.', 422);
        }

        $jenisPembiayaan = $request->input('jenis_pembiayaan');

        // ── 1. Pastikan id_debitur tersedia ──────────────────────────────────
        if (empty($request->input('id_debitur'))) {
            $request->merge(['id_debitur' => $pengajuan->id_debitur]);
        }

        // ── 2. Tangani lampiran_sid lama (string path, bukan file baru) ──────
        $existingLampiranSid = null;
        if (!$request->hasFile('lampiran_sid') && is_string($request->input('lampiran_sid'))) {
            $existingLampiranSid = $request->input('lampiran_sid') ?: $pengajuan->lampiran_sid;
            $request->request->remove('lampiran_sid');
        }

        // ── 3. Normalisasi data invoice ──────────────────────────────────────
        // Livewire mengirim key 'form_data_invoice', controller menggunakan 'details'
        $rawInvoice = $request->input('form_data_invoice', $request->input('details', []));

        $normalizedDetails = collect($rawInvoice)->map(function (array $item) {
            // Konversi tanggal Y-m-d → d/m/Y agar lolos validasi date_format:d/m/Y
            foreach (['invoice_date', 'due_date', 'kontrak_date'] as $field) {
                if (!empty($item[$field])) {
                    $parsed = parseCarbonDate($item[$field]);
                    if ($parsed) {
                        $item[$field] = $parsed->format('d/m/Y');
                    }
                }
            }

            // Pindahkan path dokumen lama ke key 'existing_*' agar tidak gagal validasi 'file'
            foreach (['dokumen_invoice', 'dokumen_kontrak', 'dokumen_so', 'dokumen_bast', 'dokumen_lainnya'] as $dok) {
                if (isset($item[$dok]) && is_string($item[$dok]) && $item[$dok] !== '') {
                    $item['existing_' . $dok] = $item[$dok];
                    unset($item[$dok]);
                }
            }

            return $item;
        })->values()->all();

        $request->merge(['details' => $normalizedDetails]);

        // ── 4. Bangun rules validasi ─────────────────────────────────────────
        $rules = [
            'id_debitur'       => 'required|string',
            'nama_bank'        => 'nullable|string',
            'no_rekening'      => 'nullable|string',
            'nama_rekening'    => 'nullable|string',
            'jenis_pembiayaan' => 'required|string',
            'catatan_lainnya'  => 'nullable|string',
        ];

        if ($jenisPembiayaan === 'Invoice Financing') {
            $rules += [
                'details'                   => 'required|array|min:1',
                'lampiran_sid'              => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
                'id_instansi'               => 'nullable',
                'sumber_pembiayaan'         => 'nullable',
                'tujuan_pembiayaan'         => 'nullable|string',
                'total_pinjaman'            => 'nullable',
                'harapan_tanggal_pencairan' => 'required|date_format:d/m/Y',
                'total_bunga'               => 'nullable',
                'rencana_tgl_pembayaran'    => 'required|date_format:d/m/Y',
                'pembayaran_total'          => 'nullable',
            ];
        } elseif ($jenisPembiayaan === 'Installment') {
            $rules += [
                'details'               => 'required|array|min:1',
                'total_pinjaman'        => 'nullable',
                'tenor_pembayaran'      => 'nullable|in:3,6,9,12',
                'persentase_bunga'      => 'nullable|numeric',
                'pps'                   => 'nullable|numeric',
                'sfinance'              => 'nullable|numeric',
                'total_pembayaran'      => 'nullable|numeric',
                'yang_harus_dibayarkan' => 'nullable|numeric',
            ];
        }

        // Tambahkan rules per-item invoice dengan exclude IDs untuk unique check
        if ($jenisPembiayaan && !empty($normalizedDetails)) {
            $existingBuktiIds = BuktiPeminjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                ->pluck('id_bukti_peminjaman')
                ->toArray();

            $invoiceRules = (new \App\Http\Requests\InvoicePengajuanPinjamanRequest())
                ->getRules($jenisPembiayaan, $normalizedDetails, $existingBuktiIds);

            foreach ($invoiceRules as $key => $rule) {
                // Tambahkan distinct untuk no_invoice/no_kontrak
                if (in_array($key, ['no_invoice', 'no_kontrak'])) {
                    $rule = array_merge((array) $rule, ['distinct']);
                }
                $rules["details.*.{$key}"] = $rule;
            }

            // Field tambahan yang tidak ada di invoice rules tapi perlu disimpan
            $rules['details.*.nilai_bunga'] = 'nullable';
            $rules['details.*.nama_barang'] = 'nullable|string';
        }

        // ── 5. Validasi ──────────────────────────────────────────────────────
        $validated = $request->validate($rules);

        // Override nilai yang selalu fixed
        $validated['sumber_pembiayaan'] = 'Internal';
        $validated['id_instansi']       = null;
        $validated['persentase_bunga']  = $jenisPembiayaan === 'Installment' ? 10 : 2;

        // ── 6. Simpan ke database ────────────────────────────────────────────
        DB::beginTransaction();
        try {
            // Handle lampiran SID
            $lampiranSidPath = $existingLampiranSid ?? $pengajuan->lampiran_sid;
            if ($request->hasFile('lampiran_sid')) {
                if ($lampiranSidPath && Storage::disk('public')->exists($lampiranSidPath)) {
                    Storage::disk('public')->delete($lampiranSidPath);
                }
                $lampiranSidPath = $request->file('lampiran_sid')->store('lampiran_sid', 'public');
            }

            $pengajuan->update([
                'id_debitur'                => $validated['id_debitur'],
                'nama_bank'                 => $validated['nama_bank'] ?? null,
                'no_rekening'               => $validated['no_rekening'] ?? null,
                'nama_rekening'             => $validated['nama_rekening'] ?? null,
                'jenis_pembiayaan'          => $validated['jenis_pembiayaan'],
                'sumber_pembiayaan'         => $validated['sumber_pembiayaan'],
                'id_instansi'               => $validated['id_instansi'],
                'lampiran_sid'              => $lampiranSidPath,
                'tujuan_pembiayaan'         => $validated['tujuan_pembiayaan'] ?? null,
                'harapan_tanggal_pencairan' => isset($validated['harapan_tanggal_pencairan'])
                    ? parseCarbonDate($validated['harapan_tanggal_pencairan'])->format('Y-m-d')
                    : null,
                'rencana_tgl_pembayaran'    => isset($validated['rencana_tgl_pembayaran'])
                    ? parseCarbonDate($validated['rencana_tgl_pembayaran'])->format('Y-m-d')
                    : null,
                'catatan_lainnya'           => $validated['catatan_lainnya'] ?? null,
                'tenor_pembayaran'          => $validated['tenor_pembayaran'] ?? null,
                'persentase_bunga'          => $validated['persentase_bunga'],
                'updated_by'                => auth()->id(),
                'status'                    => 'Draft',
            ]);

            HistoryStatusPengajuanPinjaman::create([
                'id_pengajuan_peminjaman' => $pengajuan->id_pengajuan_peminjaman,
                'status'                  => 'Draft',
                'current_step'            => 1,
            ]);

            // Ambil data bukti lama sebelum dihapus (untuk fallback file path)
            $existingBukti = BuktiPeminjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                ->get()
                ->keyBy(fn($item) => in_array($jenisPembiayaan, ['Invoice Financing', 'Installment'])
                    ? ($item->no_invoice ?? 'tmp_' . $item->id_bukti_peminjaman)
                    : ($item->no_kontrak  ?? 'tmp_' . $item->id_bukti_peminjaman)
                )
                ->toArray();

            BuktiPeminjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)->delete();

            foreach ($validated['details'] as $i => $det) {
                $this->storeBuktiPeminjaman($request, $pengajuan->id_pengajuan_peminjaman, $jenisPembiayaan, $i, $det, $existingBukti);
            }

            DB::commit();

            return Response::success(null, 'Pengajuan pinjaman berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e, 'Gagal mengupdate pengajuan pinjaman.');
        }
    }

    /**
     * Simpan pengajuan peminjaman baru (dipanggil oleh Livewire Create via UniversalFormAction).
     */
    public function store(PengajuanPinjamanRequest $request)
    {
        DB::beginTransaction();
        try {
            $allData = $request->validated();
            $dataInvoice = collect($allData['form_data_invoice'] ?? $allData['details'] ?? []);
            unset($allData['form_data_invoice'], $allData['details']);
            $dataPengajuanPeminjaman = $allData;

            $dataPengajuanPeminjaman['status'] = PengajuanPeminjamanStatusEnum::DRAFT;
            $dataPengajuanPeminjaman['nomor_peminjaman'] = (new PeminjamanNumberService())->generateNumber(
                JenisPembiayaanEnum::getPrefix($dataPengajuanPeminjaman['jenis_pembiayaan']),
                now()->format('Ym')
            );

            $this->persentase_bunga = $dataPengajuanPeminjaman['jenis_pembiayaan'] === 'Installment'
                ? (float) 10 / 100
                : (float) 2 / 100;

            $masterDebitur = MasterDebiturDanInvestor::where('email', auth()->user()->email)
                ->where('flagging', 'tidak')
                ->where('status', 'active')
                ->first();

            $dataPengajuanPeminjaman['no_rekening'] = $masterDebitur->no_rek;

            if (empty($dataPengajuanPeminjaman['total_pinjaman'])) {
                $dataPengajuanPeminjaman['total_pinjaman'] = $dataPengajuanPeminjaman['jenis_pembiayaan'] === 'Installment'
                    ? (float) $dataInvoice->sum(fn($item) => (float) ($item['nilai_invoice'] ?? 0))
                    : (float) $dataInvoice->sum(fn($item) => (float) ($item['nilai_pinjaman'] ?? 0));
            }

            if ($dataPengajuanPeminjaman['jenis_pembiayaan'] === 'Installment') {
                $persentaseForCalc = isset($dataPengajuanPeminjaman['persentase_bunga'])
                    ? (float) $dataPengajuanPeminjaman['persentase_bunga'] / 100
                    : 0.10;
                $dataPengajuanPeminjaman['persentase_bunga'] = $persentaseForCalc * 100;
            } else {
                $persentaseForCalc = $this->persentase_bunga;
                $dataPengajuanPeminjaman['persentase_bunga'] = $this->persentase_bunga * 100;
            }

            $dataPengajuanPeminjaman['sumber_pembiayaan'] = 'Internal';
            $dataPengajuanPeminjaman['id_instansi']       = null;

            if (empty($dataPengajuanPeminjaman['total_bunga'])) {
                $dataPengajuanPeminjaman['total_bunga'] = $dataPengajuanPeminjaman['total_pinjaman'] * $persentaseForCalc;
            }
            if (empty($dataPengajuanPeminjaman['pembayaran_total'])) {
                $dataPengajuanPeminjaman['pembayaran_total'] = (float) $dataPengajuanPeminjaman['total_pinjaman'] + $dataPengajuanPeminjaman['total_bunga'];
            }

            if ($dataPengajuanPeminjaman['jenis_pembiayaan'] === 'Installment') {
                $dataPengajuanPeminjaman['pps']                   = (float) $dataPengajuanPeminjaman['total_bunga'] * 0.60;
                $dataPengajuanPeminjaman['s_finance']             = (float) $dataPengajuanPeminjaman['total_bunga'] * 0.40;
                $dataPengajuanPeminjaman['yang_harus_dibayarkan'] = (float) ($dataPengajuanPeminjaman['pembayaran_total'] / $dataPengajuanPeminjaman['tenor_pembayaran']);
                $dataPengajuanPeminjaman['harapan_tanggal_pencairan'] = null;
                $dataPengajuanPeminjaman['rencana_tgl_pembayaran']    = null;
            } else {
                $dataPengajuanPeminjaman['harapan_tanggal_pencairan'] = parseCarbonDate($dataPengajuanPeminjaman['harapan_tanggal_pencairan'])->format('Y-m-d');
                $dataPengajuanPeminjaman['rencana_tgl_pembayaran']    = parseCarbonDate($dataPengajuanPeminjaman['rencana_tgl_pembayaran'])->format('Y-m-d');
                $dataPengajuanPeminjaman['tenor_pembayaran']          = null;
                $dataPengajuanPeminjaman['pps']                       = null;
                $dataPengajuanPeminjaman['s_finance']                 = null;
                $dataPengajuanPeminjaman['yang_harus_dibayarkan']     = null;
            }

            $debitur = MasterDebiturDanInvestor::select('id_debitur', 'kode_perusahaan')
                ->where('email', auth()->user()->email)
                ->first();
            $dataPengajuanPeminjaman['id_debitur'] = $debitur->id_debitur;

            if (isset($dataPengajuanPeminjaman['lampiran_sid']) && $dataPengajuanPeminjaman['lampiran_sid'] instanceof UploadedFile) {
                $dataPengajuanPeminjaman['lampiran_sid'] = Storage::disk('public')->put('lampiran_sid', $dataPengajuanPeminjaman['lampiran_sid']);
            }
            $dataPengajuanPeminjaman['created_by']           = auth()->user()->id;
            $dataPengajuanPeminjaman['updated_by']           = auth()->user()->id;
            $dataPengajuanPeminjaman['nominal_pengajuan_awal'] = $dataPengajuanPeminjaman['total_pinjaman'];

            $peminjaman = PengajuanPeminjaman::create($dataPengajuanPeminjaman);

            foreach ($dataInvoice as $i => $inv) {
                if ($dataPengajuanPeminjaman['jenis_pembiayaan'] !== JenisPembiayaanEnum::INSTALLMENT) {
                    $inv['nilai_bunga'] = (float) ($inv['nilai_pinjaman'] ?? 0) * (float) $this->persentase_bunga;
                }
                $inv['id_pengajuan_peminjaman'] = $peminjaman->id_pengajuan_peminjaman;

                if (in_array($dataPengajuanPeminjaman['jenis_pembiayaan'], [JenisPembiayaanEnum::INVOICE_FINANCING, JenisPembiayaanEnum::INSTALLMENT])) {
                    $inv['invoice_date'] = parseCarbonDate($inv['invoice_date'])->format('Y-m-d');
                } else {
                    $inv['kontrak_date'] = parseCarbonDate($inv['kontrak_date'])->format('Y-m-d');
                }
                if (isset($inv['due_date'])) {
                    $inv['due_date'] = parseCarbonDate($inv['due_date'])->format('Y-m-d');
                }

                foreach (['dokumen_invoice', 'dokumen_kontrak', 'dokumen_so', 'dokumen_bast', 'dokumen_lainnya'] as $dok) {
                    $inv[$dok] = (isset($inv[$dok]) && $inv[$dok] instanceof UploadedFile)
                        ? Storage::disk('public')->put($dok, $inv[$dok])
                        : null;
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

    public function toggleActive($id)
    {
        try {
            $pengajuan = PengajuanPeminjaman::findOrFail($id);
            $newStatus = $pengajuan->is_active === 'active' ? 'non active' : 'active';
            $pengajuan->is_active = $newStatus;
            $pengajuan->save();

            return response()->json([
                'success'   => true,
                'message'   => 'Status berhasil diubah menjadi ' . $newStatus,
                'is_active' => $newStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper: simpan satu baris BuktiPeminjaman saat update.
     */
    private function storeBuktiPeminjaman(Request $request, string $idPengajuan, string $jenis, int $i, array $det, array $existing): void
    {
        $getFile = function (string $field) use ($request, $i, $det, $existing, $jenis): ?string {
            foreach (["files.{$i}.{$field}", "details.{$i}.{$field}"] as $key) {
                if ($request->hasFile($key)) {
                    return $request->file($key)->store('peminjaman/invoices', 'public');
                }
            }
            // Gunakan path lama yang disimpan saat normalisasi (existing_dokumen_*)
            if (!empty($det['existing_' . $field])) {
                return $det['existing_' . $field];
            }
            // Fallback ke $existing yang di-build dari DB sebelum delete
            $existingKey = in_array($jenis, ['Invoice Financing', 'Installment'])
                ? ($det['no_invoice'] ?? null)
                : ($det['no_kontrak']   ?? null);
            return $existing[$existingKey][$field] ?? null;
        };

        $clean = fn($v) => $v !== null ? (string) (int) round((float) preg_replace('/[^0-9.]/', '', $v)) : null;

        $base = ['id_pengajuan_peminjaman' => $idPengajuan];

        if ($jenis === 'Invoice Financing') {
            BuktiPeminjaman::create($base + [
                'no_invoice'      => $det['no_invoice']   ?? null,
                'nama_client'     => $det['nama_client']  ?? null,
                'nilai_invoice'   => $clean($det['nilai_invoice']  ?? null),
                'nilai_pinjaman'  => $clean($det['nilai_pinjaman'] ?? null),
                'nilai_bunga'     => $clean($det['nilai_bunga']    ?? null),
                'invoice_date'    => !empty($det['invoice_date']) ? parseCarbonDate($det['invoice_date'])?->format('Y-m-d') : null,
                'due_date'        => !empty($det['due_date'])     ? parseCarbonDate($det['due_date'])?->format('Y-m-d')     : null,
                'dokumen_invoice' => $getFile('dokumen_invoice'),
                'dokumen_kontrak' => $getFile('dokumen_kontrak'),
                'dokumen_so'      => $getFile('dokumen_so'),
                'dokumen_bast'    => $getFile('dokumen_bast'),
                'dokumen_lainnya' => $getFile('dokumen_lainnya'),
            ]);
        } elseif ($jenis === 'Installment') {
            BuktiPeminjaman::create($base + [
                'no_invoice'      => $det['no_invoice']  ?? null,
                'nama_client'     => $det['nama_client'] ?? null,
                'nama_barang'     => $det['nama_barang'] ?? null,
                'nilai_invoice'   => $clean($det['nilai_invoice'] ?? null),
                'invoice_date'    => !empty($det['invoice_date']) ? parseCarbonDate($det['invoice_date'])?->format('Y-m-d') : null,
                'dokumen_invoice' => $getFile('dokumen_invoice'),
                'dokumen_lainnya' => $getFile('dokumen_lainnya'),
            ]);
        }
    }
}
