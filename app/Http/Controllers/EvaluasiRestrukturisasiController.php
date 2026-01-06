<?php

namespace App\Http\Controllers;

use App\Models\{
    EvaluasiAnalisaRestrukturisasi,
    EvaluasiKelayakanDebitur,
    EvaluasiKelengkapanDokumen,
    EvaluasiPengajuanRestrukturisasi,
    HistoryStatusPengajuanRestrukturisasi,
    PengajuanRestrukturisasi,
    PersetujuanKomiteRestrukturisasi
};
use App\Helpers\ListNotifSFinance;
use Carbon\Carbon;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Validation\ValidationException;

class EvaluasiRestrukturisasiController extends Controller
{
    private const STATUS_SELESAI = 'Selesai';
    private const STATUS_DALAM_PROSES = 'Dalam Proses';
    private const STATUS_PERBAIKAN_DOKUMEN = 'Perbaikan Dokumen';
    private const STATUS_PERLU_EVALUASI = 'Perlu Evaluasi Ulang';
    private const STATUS_DITOLAK = 'Ditolak';

    private const VALIDASI_DISETUJUI = 'disetujui';
    private const VALIDASI_DITOLAK = 'ditolak';

    private const STEP_FINAL = 5;
    private const STEP_APPROVAL_THRESHOLD = 4;

    public function save(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rekomendasi' => 'required|string',
                'justifikasi_rekomendasi' => 'nullable|string',
            ]);

            $sections = $this->parseSections($request);

            DB::beginTransaction();

            $pengajuan = PengajuanRestrukturisasi::findOrFail($id);
            $evaluasi = $this->createOrUpdateEvaluasi($id, $validated);

            $this->syncSections($evaluasi, $sections, $request);

            // Jika status sebelumnya "Perlu Evaluasi Ulang", ubah menjadi "Dalam Proses"
            // Ini memastikan form menjadi read-only setelah evaluasi ulang disimpan
            if ($pengajuan->status === self::STATUS_PERLU_EVALUASI) {
                $pengajuan->status = self::STATUS_DALAM_PROSES;
                $pengajuan->save();
            }

            DB::commit();

            return $this->successResponse($evaluasi, 'Evaluasi berhasil disimpan');
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error save evaluasi', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->errorResponse('Gagal menyimpan evaluasi: ' . $e->getMessage(), 500);
        }
    }

    public function decision(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'step' => 'required|integer',
                'note' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $pengajuan = PengajuanRestrukturisasi::findOrFail($id);

            $history = $this->buildHistoryData($pengajuan, $validated);

            // Handle submit pengajuan (step 1 -> step 2)
            if ($validated['action'] === 'approve' && $validated['step'] == 1) {
                $pengajuan->status = 'Submit Dokumen';
                $pengajuan->current_step = 2;
                $pengajuan->save();

                $history['status'] = $pengajuan->status;
                $history['current_step'] = $pengajuan->current_step;
                $history['submit_by'] = auth()->id();
            } else if ($validated['action'] === 'approve') {
                $this->handleApproval($pengajuan, $validated['step'], $history);
            } else {
                $this->handleRejection($pengajuan, $validated['step'], $history);
            }

            $historyRecord = HistoryStatusPengajuanRestrukturisasi::create($history);

            // Reload pengajuan dengan relasi
            $pengajuan->load('debitur');

            DB::commit();

            // Kirim notifikasi berdasarkan step dan action
            ListNotifSFinance::menuRestrukturisasi($pengajuan->status, $pengajuan, $validated['step']);

            return $this->successResponse($pengajuan, 'Keputusan berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error save decision', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->errorResponse('Gagal memproses keputusan: ' . $e->getMessage(), 500);
        }
    }

    private function parseSections(Request $request): array
    {
        return [
            'kelengkapan' => json_decode($request->input('kelengkapan'), true) ?: [],
            'kelayakan' => json_decode($request->input('kelayakan'), true) ?: [],
            'analisa' => json_decode($request->input('analisa'), true) ?: [],
        ];
    }

    private function createOrUpdateEvaluasi(string $id, array $data): EvaluasiPengajuanRestrukturisasi
    {
        $evaluasi = EvaluasiPengajuanRestrukturisasi::firstOrCreate(
            ['id_pengajuan_restrukturisasi' => $id]
        );

        $evaluasi->update($data);

        return $evaluasi;
    }

    private function syncSections(EvaluasiPengajuanRestrukturisasi $evaluasi, array $sections, Request $request): void
    {
        $evaluasiId = $evaluasi->id_evaluasi_restrukturisasi;

        $this->syncKelengkapanDokumen($evaluasiId, $sections['kelengkapan']);
        $this->syncKelayakanDebitur($evaluasiId, $sections['kelayakan']);
        $this->syncAnalisaRestrukturisasi($evaluasiId, $sections['analisa']);
        $this->syncPersetujuanKomite($evaluasiId, $request);
    }

    private function syncKelengkapanDokumen($evaluasiId, array $data): void
    {
        if (empty($data)) return;

        EvaluasiKelengkapanDokumen::where('id_evaluasi_restrukturisasi', $evaluasiId)->delete();

        foreach ($data as $row) {
            EvaluasiKelengkapanDokumen::create([
                'id_evaluasi_restrukturisasi' => $evaluasiId,
                'nama_dokumen' => $row['nama_dokumen'] ?? '',
                'status' => $row['status'] ?? 'Tidak',
                'catatan' => $row['catatan'] ?? null,
            ]);
        }
    }

    private function syncKelayakanDebitur($evaluasiId, array $data): void
    {
        if (empty($data)) return;

        EvaluasiKelayakanDebitur::where('id_evaluasi_restrukturisasi', $evaluasiId)->delete();

        foreach ($data as $row) {
            EvaluasiKelayakanDebitur::create([
                'id_evaluasi_restrukturisasi' => $evaluasiId,
                'kriteria' => $row['kriteria'] ?? '',
                'status' => $row['status'] ?? 'Tidak',
                'catatan' => $row['catatan'] ?? null,
            ]);
        }
    }

    private function syncAnalisaRestrukturisasi($evaluasiId, array $data): void
    {
        if (empty($data)) return;

        EvaluasiAnalisaRestrukturisasi::where('id_evaluasi_restrukturisasi', $evaluasiId)->delete();

        foreach ($data as $row) {
            EvaluasiAnalisaRestrukturisasi::create([
                'id_evaluasi_restrukturisasi' => $evaluasiId,
                'aspek' => $row['aspek'] ?? '',
                'evaluasi' => $row['evaluasi'] ?? null,
                'catatan' => $row['catatan'] ?? null,
            ]);
        }
    }

    private function syncPersetujuanKomite($evaluasiId, Request $request): void
    {
        if (!$request->has('persetujuan_komite')) return;

        $komiteData = $request->input('persetujuan_komite');
        if (!is_array($komiteData)) return;

        PersetujuanKomiteRestrukturisasi::where('id_evaluasi_restrukturisasi', $evaluasiId)->delete();

        foreach ($komiteData as $idx => $row) {
            $ttdPath = $this->handleTtdUpload($request, $idx);

            PersetujuanKomiteRestrukturisasi::create([
                'id_evaluasi_restrukturisasi' => $evaluasiId,
                'nama_anggota' => $row['nama_anggota'] ?? null,
                'jabatan' => $row['jabatan'] ?? null,
                'tanggal_persetujuan' => $row['tanggal_persetujuan'] ?? null,
                'ttd_digital' => $ttdPath,
            ]);
        }
    }

    private function handleTtdUpload(Request $request, int $index): ?string
    {
        $fileKey = "persetujuan_komite.{$index}.ttd_digital";

        if (!$request->hasFile($fileKey)) {
            return null;
        }

        $file = $request->file($fileKey);
        $filename = time() . '_persetujuan_' . $index . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName());

        return $file->storeAs('restrukturisasi/ttd', $filename, 'public');
    }

    private function buildHistoryData(PengajuanRestrukturisasi $pengajuan, array $validated): array
    {
        return [
            'id_pengajuan_restrukturisasi' => $pengajuan->id_pengajuan_restrukturisasi,
            'status' => $pengajuan->status,
            'current_step' => $pengajuan->current_step ?? 1,
            'date' => Carbon::now()->toDateString(),
            'time' => Carbon::now()->toTimeString(),
            'submit_by' => auth()->id(),
            'catatan' => $validated['note'] ?? null,
        ];
    }

    private function handleApproval(PengajuanRestrukturisasi $pengajuan, int $step, array &$history): void
    {
        $pengajuan->current_step = $step + 1;
        $pengajuan->status = $step >= self::STEP_APPROVAL_THRESHOLD
            ? self::STATUS_SELESAI
            : self::STATUS_DALAM_PROSES;
        $pengajuan->save();

        $history['status'] = $pengajuan->status;
        $history['current_step'] = $pengajuan->current_step;
        $history['approve_by'] = auth()->id();
        $history['validasi_dokumen'] = self::VALIDASI_DISETUJUI;
    }

    private function handleRejection(PengajuanRestrukturisasi $pengajuan, int $step, array &$history): void
    {
        $rejectionHandlers = [
            2 => fn() => $this->rejectStep2($pengajuan),
            3 => fn() => $this->rejectStep3($pengajuan),
            4 => fn() => $this->rejectStep4($pengajuan),
        ];

        if (isset($rejectionHandlers[$step])) {
            $rejectionHandlers[$step]();
        } else {
            $this->rejectDefault($pengajuan, $step);
        }

        $history['status'] = $pengajuan->status;
        $history['current_step'] = $pengajuan->current_step;
        $history['reject_by'] = auth()->id();
        $history['validasi_dokumen'] = self::VALIDASI_DITOLAK;
    }

    private function rejectStep2(PengajuanRestrukturisasi $pengajuan): void
    {
        $pengajuan->status = self::STATUS_PERBAIKAN_DOKUMEN;
        $pengajuan->current_step = 1;
        $pengajuan->save();
    }

    private function rejectStep3(PengajuanRestrukturisasi $pengajuan): void
    {
        $pengajuan->status = self::STATUS_PERLU_EVALUASI;
        $pengajuan->current_step = 2;
        $pengajuan->save();
    }

    private function rejectStep4(PengajuanRestrukturisasi $pengajuan): void
    {
        // Kembali ke step 3 untuk persetujuan ulang CEO SKI
        $pengajuan->status = self::STATUS_PERLU_EVALUASI;
        $pengajuan->current_step = 3;
        $pengajuan->save();
    }

    private function rejectDefault(PengajuanRestrukturisasi $pengajuan, int $step): void
    {
        $pengajuan->status = self::STATUS_DITOLAK;
        $pengajuan->current_step = $step;
        $pengajuan->save();
    }

    private function successResponse($data, string $message): JsonResponse
    {
        return response()->json([
            'error' => false,
            'message' => $message,
            'data' => $data
        ]);
    }

    private function errorResponse(string $message, int $code = 500, $errors = null): JsonResponse
    {
        $response = [
            'error' => true,
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
