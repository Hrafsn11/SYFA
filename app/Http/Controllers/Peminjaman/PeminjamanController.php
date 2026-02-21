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
use Illuminate\Http\UploadedFile;

/**
 * PeminjamanController
 *
 * Hanya berisi endpoint yang masih dipanggil secara langsung:
 *  - store        → dipanggil oleh Livewire Create melalui UniversalFormAction
 *  - update       → dipanggil oleh Livewire Create melalui UniversalFormAction
 *  - previewKontrak → dipanggil oleh Livewire Detail (KontrakPdfHandler redirect)
 *  - toggleActive → dipanggil oleh JS fetch di halaman index
 *
 * Semua fitur lain (index, show, create, edit, approval, download kontrak, generate kontrak)
 * sudah ditangani sepenuhnya oleh Livewire components di App\Livewire\PengajuanPinjaman\.
 */
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

        $no_kontrak = 'SKI/FIN/' . date('Y') . '/' . str_pad($pengajuan->id_pengajuan_peminjaman, 3, '0', STR_PAD_LEFT);

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
            'denda_keterlambatan'  => '2% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut',
            'jaminan'              => $pengajuan->jenis_pembiayaan ?? 'Invoice Financing',
            'tanda_tangan'         => $pengajuan->debitur->tanda_tangan ?? null,
        ];

        return view('livewire.pengajuan-pinjaman.preview-kontrak', compact('kontrak'));
    }

    /**
     * Update pengajuan peminjaman (dipanggil oleh Livewire Create via UniversalFormAction).
     */
    public function update(Request $request, $id)
    {
        $pengajuan = PengajuanPeminjaman::findOrFail($id);

        if (!in_array($pengajuan->status, ['Draft', 'Validasi Ditolak'])) {
            return redirect()->route('peminjaman')->with('error', 'Pengajuan dengan status ' . $pengajuan->status . ' tidak dapat diedit.');
        }

        $jenisPembiayaan = $request->input('jenis_pembiayaan');

        $rules = [
            'id_debitur'       => 'required|string|size:26',
            'nama_bank'        => 'nullable|string',
            'no_rekening'      => 'nullable|string',
            'nama_rekening'    => 'nullable|string',
            'jenis_pembiayaan' => 'required|string',
            'catatan_lainnya'  => 'nullable|string',
        ];

        if ($jenisPembiayaan === 'Invoice Financing') {
            $rules['details']                    = 'required|array|min:1';
            $rules['lampiran_sid']               = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['nilai_kol']                  = 'nullable|string';
            $rules['id_instansi']                = 'nullable';
            $rules['sumber_pembiayaan']          = 'nullable';
            $rules['tujuan_pembiayaan']          = 'nullable|string';
            $rules['total_pinjaman']             = 'nullable';
            $rules['harapan_tanggal_pencairan']  = 'required|date_format:Y-m-d';
            $rules['total_bunga']                = 'nullable';
            $rules['rencana_tgl_pembayaran']     = 'required|date_format:Y-m-d';
            $rules['pembayaran_total']           = 'nullable';
        } elseif ($jenisPembiayaan === 'Installment') {
            $rules['details']              = 'required|array|min:1';
            $rules['total_pinjaman']       = 'nullable';
            $rules['tenor_pembayaran']     = 'nullable|in:3,6,9,12';
            $rules['persentase_bunga']     = 'nullable|numeric';
            $rules['pps']                  = 'nullable|numeric';
            $rules['sfinance']             = 'nullable|numeric';
            $rules['total_pembayaran']     = 'nullable|numeric';
            $rules['yang_harus_dibayarkan'] = 'nullable|numeric';
        } elseif ($jenisPembiayaan === 'PO Financing') {
            $rules['details']                   = 'required|array|min:1';
            $rules['id_instansi']               = 'nullable';
            $rules['no_kontrak']                = 'nullable|string';
            $rules['lampiran_sid']              = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['nilai_kol']                 = 'nullable|string';
            $rules['sumber_pembiayaan']         = 'nullable';
            $rules['tujuan_pembiayaan']         = 'nullable|string';
            $rules['total_pinjaman']            = 'nullable';
            $rules['harapan_tanggal_pencairan'] = 'required|date_format:Y-m-d';
            $rules['total_bunga']               = 'nullable';
            $rules['rencana_tgl_pembayaran']    = 'required|date_format:Y-m-d';
            $rules['pembayaran_total']          = 'nullable';
        } elseif ($jenisPembiayaan === 'Factoring') {
            $rules['details']                   = 'required|array|min:1';
            $rules['lampiran_sid']              = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['nilai_kol']                 = 'nullable|string';
            $rules['id_instansi']               = 'nullable';
            $rules['sumber_pembiayaan']         = 'nullable';
            $rules['tujuan_pembiayaan']         = 'nullable|string';
            $rules['total_pinjaman']            = 'nullable';
            $rules['harapan_tanggal_pencairan'] = 'required|date_format:Y-m-d';
            $rules['total_bunga']               = 'nullable';
            $rules['rencana_tgl_pembayaran']    = 'required|date_format:Y-m-d';
            $rules['pembayaran_total']          = 'nullable';
            $rules['total_nominal_yang_dialihkan'] = 'nullable';
        }

        $formDataInvoice = $request->input('form_data_invoice', $request->input('details', []));
        $invoiceKey = $request->has('form_data_invoice') ? 'form_data_invoice' : 'details';
        if ($jenisPembiayaan && !empty($formDataInvoice)) {
            $invoiceRequest = new \App\Http\Requests\InvoicePengajuanPinjamanRequest();
            $invoiceRules = $invoiceRequest->getRules($jenisPembiayaan, $formDataInvoice);
            foreach ($invoiceRules as $key => $rule) {
                if ($key === 'no_invoice' || $key === 'no_kontrak') {
                    $rule = array_merge((array) $rule, ['distinct']);
                }
                $rules["{$invoiceKey}.*.{$key}"] = $rule;
            }
        }

        $validated = $request->validate($rules);

        if ($jenisPembiayaan === 'Installment') {
            $validated['id_instansi']        = null;
            $validated['sumber_pembiayaan']  = 'Internal';
            $validated['persentase_bunga']   = 10;
        } elseif (in_array($jenisPembiayaan, ['Invoice Financing', 'PO Financing', 'Factoring'])) {
            $validated['id_instansi']        = null;
            $validated['sumber_pembiayaan']  = 'Internal';
            $validated['persentase_bunga']   = 2;
        }

        DB::beginTransaction();
        try {
            $lampiran_sid_path = $pengajuan->lampiran_sid;
            if ($request->hasFile('lampiran_sid')) {
                if ($lampiran_sid_path && Storage::disk('public')->exists($lampiran_sid_path)) {
                    Storage::disk('public')->delete($lampiran_sid_path);
                }
                $lampiran_sid_path = $request->file('lampiran_sid')->store('lampiran_sid', 'public');
            }

            $updateData = [
                'id_debitur'                => $validated['id_debitur'],
                'nama_bank'                 => $validated['nama_bank'] ?? null,
                'no_rekening'               => $validated['no_rekening'] ?? null,
                'nama_rekening'             => $validated['nama_rekening'] ?? null,
                'jenis_pembiayaan'          => $validated['jenis_pembiayaan'],
                'sumber_pembiayaan'         => $validated['sumber_pembiayaan'] ?? null,
                'id_instansi'               => $validated['id_instansi'] ?? null,
                'lampiran_sid'              => $lampiran_sid_path,
                'nilai_kol'                 => $validated['nilai_kol'] ?? null,
                'tujuan_pembiayaan'         => $validated['tujuan_pembiayaan'] ?? null,
                'harapan_tanggal_pencairan' => $validated['harapan_tanggal_pencairan'] ?? null,
                'rencana_tgl_pembayaran'    => $validated['rencana_tgl_pembayaran'] ?? null,
                'catatan_lainnya'           => $validated['catatan_lainnya'] ?? null,
                'tenor_pembayaran'          => $validated['tenor_pembayaran'] ?? null,
                'persentase_bunga'          => $validated['persentase_bunga'] ?? null,
                'updated_by'               => auth()->id(),
                'status'                   => 'Draft',
            ];

            foreach (['total_pinjaman', 'total_bunga', 'pembayaran_total', 'pps', 'yang_harus_dibayarkan', 'total_nominal_yang_dialihkan'] as $field) {
                if ($request->has($field)) {
                    $updateData[$field === 'pps' ? 'pps' : ($field === 'sfinance' ? 's_finance' : $field)]
                        = preg_replace('/[^0-9.]/', '', $request->input($field));
                }
            }
            if ($request->has('sfinance')) {
                $updateData['s_finance'] = preg_replace('/[^0-9.]/', '', $request->input('sfinance'));
            }

            $pengajuan->update($updateData);

            HistoryStatusPengajuanPinjaman::create([
                'id_pengajuan_peminjaman' => $pengajuan->id_pengajuan_peminjaman,
                'status'       => 'Draft',
                'current_step' => 1,
            ]);

            $existingBukti = BuktiPeminjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                ->get()
                ->keyBy(function ($item) use ($jenisPembiayaan) {
                    return in_array($jenisPembiayaan, ['Invoice Financing', 'Installment'])
                        ? ($item->no_invoice ?? 'tmp_' . $item->id_bukti_peminjaman)
                        : ($item->no_kontrak   ?? 'tmp_' . $item->id_bukti_peminjaman);
                })
                ->toArray();

            BuktiPeminjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)->delete();

            $details = $validated['details'];
            foreach ($details as $i => $det) {
                $this->storeBuktiPeminjaman($request, $pengajuan->id_pengajuan_peminjaman, $jenisPembiayaan, $i, $det, $existingBukti);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Pengajuan pinjaman berhasil diupdate!', 'data' => $pengajuan]);
            }

            return redirect()->route('peminjaman')->with('success', 'Pengajuan pinjaman berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengupdate: ' . $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Gagal mengupdate pengajuan pinjaman: ' . $e->getMessage());
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
                ->with('kol')
                ->first();

            $dataPengajuanPeminjaman['nama_bank']  = $masterDebitur->nama_bank;
            $dataPengajuanPeminjaman['no_rekening'] = $masterDebitur->no_rek;
            $dataPengajuanPeminjaman['nilai_kol']   = $masterDebitur->kol->kol;

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
                $dataPengajuanPeminjaman['pps']                   = (float) $dataPengajuanPeminjaman['total_bunga'] * 0.40;
                $dataPengajuanPeminjaman['s_finance']             = (float) $dataPengajuanPeminjaman['total_bunga'] * 0.60;
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

    /**
     * Toggle active/non-active status (dipanggil oleh JS fetch di halaman index).
     */
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
            $existingKey = in_array($jenis, ['Invoice Financing', 'Installment'])
                ? ($det['no_invoice'] ?? null)
                : ($det['no_kontrak']   ?? null);
            return $existing[$existingKey][$field] ?? null;
        };

        $clean = fn($v) => $v !== null ? preg_replace('/[^0-9]/', '', $v) : null;

        $base = ['id_pengajuan_peminjaman' => $idPengajuan];

        if ($jenis === 'Invoice Financing') {
            BuktiPeminjaman::create($base + [
                'no_invoice'      => $det['no_invoice']   ?? null,
                'nama_client'     => $det['nama_client']  ?? null,
                'nilai_invoice'   => $clean($det['nilai_invoice']  ?? null),
                'nilai_pinjaman'  => $clean($det['nilai_pinjaman'] ?? null),
                'nilai_bunga'     => $clean($det['nilai_bunga']    ?? null),
                'invoice_date'    => $det['invoice_date'] ?? null,
                'due_date'        => $det['due_date']     ?? null,
                'dokumen_invoice' => $getFile('dokumen_invoice'),
                'dokumen_kontrak' => $getFile('dokumen_kontrak'),
                'dokumen_so'      => $getFile('dokumen_so'),
                'dokumen_bast'    => $getFile('dokumen_bast'),
                'dokumen_lainnya' => $getFile('dokumen_lainnya'),
            ]);
        } elseif ($jenis === 'PO Financing') {
            BuktiPeminjaman::create($base + [
                'no_kontrak'      => $det['no_kontrak']  ?? null,
                'nama_client'     => $det['nama_client'] ?? null,
                'nilai_invoice'   => $clean($det['nilai_invoice']  ?? null),
                'nilai_pinjaman'  => $clean($det['nilai_pinjaman'] ?? null),
                'nilai_bunga'     => $clean($det['nilai_bunga']    ?? null),
                'kontrak_date'    => $det['kontrak_date'] ?? null,
                'due_date'        => $det['due_date']     ?? null,
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
                'invoice_date'    => $det['invoice_date'] ?? null,
                'dokumen_invoice' => $getFile('dokumen_invoice'),
                'dokumen_lainnya' => $getFile('dokumen_lainnya'),
            ]);
        } elseif ($jenis === 'Factoring') {
            BuktiPeminjaman::create($base + [
                'no_kontrak'      => $det['no_kontrak']  ?? null,
                'nama_client'     => $det['nama_client'] ?? null,
                'nilai_invoice'   => $clean($det['nilai_invoice']  ?? null),
                'nilai_pinjaman'  => $clean($det['nilai_pinjaman'] ?? null),
                'nilai_bunga'     => $clean($det['nilai_bunga']    ?? null),
                'kontrak_date'    => $det['kontrak_date'] ?? null,
                'due_date'        => $det['due_date']     ?? null,
                'dokumen_invoice' => $getFile('dokumen_invoice'),
                'dokumen_kontrak' => $getFile('dokumen_kontrak'),
                'dokumen_so'      => $getFile('dokumen_so'),
                'dokumen_bast'    => $getFile('dokumen_bast'),
            ]);
        }
    }
}
