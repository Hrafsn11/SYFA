<?php

namespace App\Livewire\PengajuanPinjaman\Approval;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanPeminjaman;
use App\Models\HistoryStatusPengajuanPinjaman;

/**
 * Trait ApprovalActions
 * 
 * Berisi logika untuk proses approval pada pengajuan peminjaman.
 * Trait ini menangani berbagai status approval dan transisi workflow.
 */
trait ApprovalActions
{
    /**
     * Daftar status yang valid untuk approval.
     */
    protected array $validStatuses = [
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
        'Konfirmasi Ditolak Debitur',
        'Konfirmasi Disetujui Debitur',
    ];

    /**
     * Mapping status ke step workflow.
     */
    protected array $statusToStepMapping = [
        'Draft' => 1,
        'Submit Dokumen' => 2,
        'Dokumen Tervalidasi' => 3,
        'Debitur Setuju' => 4,
        'Disetujui oleh CEO SKI' => 5,
        'Disetujui oleh Direktur SKI' => 6,
        'Generate Kontrak' => 7,
        'Menunggu Konfirmasi Debitur' => 8,
        'Dana Sudah Dicairkan' => 9,
    ];

    /**
     * Submit dokumen pengajuan (Step 1 -> 2).
     */
    public function submitDokumen()
    {
        return $this->processApproval('Submit Dokumen', [
            'submit_step1_by' => auth()->id(),
            'current_step' => 2,
        ]);
    }

    /**
     * Validasi dokumen dan setujui pencairan (Step 2 -> 3).
     */
    public function validasiDokumenSetuju()
    {
        $this->validate([
            'deviasi' => 'required|in:ya,tidak',
            'nominal_yang_disetujui' => 'required|numeric|min:0',
            'tanggal_pencairan' => 'required|date_format:d/m/Y',
            'persentase_bunga' => 'required|numeric|min:0|max:100',
        ]);

        $totalBagiHasil = $this->nominal_yang_disetujui * ($this->persentase_bunga / 100);

        return $this->processApproval('Dokumen Tervalidasi', [
            'validasi_dokumen' => 'disetujui',
            'approve_by' => auth()->id(),
            'deviasi' => $this->deviasi,
            'nominal_yang_disetujui' => $this->nominal_yang_disetujui,
            'tanggal_pencairan' => $this->parseTanggal($this->tanggal_pencairan),
            'persentase_bunga' => $this->persentase_bunga,
            'total_bunga' => $totalBagiHasil,
            'catatan_validasi_dokumen_disetujui' => $this->catatan_approval,
            'current_step' => 3,
        ], function ($pengajuan) use ($totalBagiHasil) {
            // Update data peminjaman
            $pengajuan->update([
                'total_pinjaman' => $this->nominal_yang_disetujui,
                'persentase_bunga' => $this->persentase_bunga,
                'total_bunga' => $totalBagiHasil,
                'pembayaran_total' => $this->nominal_yang_disetujui + $totalBagiHasil,
            ]);
        });
    }

    /**
     * Tolak validasi dokumen.
     */
    public function validasiDokumenTolak()
    {
        $this->validate([
            'catatan_approval' => 'required|string|min:10',
        ], [
            'catatan_approval.required' => 'Catatan penolakan wajib diisi.',
            'catatan_approval.min' => 'Catatan penolakan minimal 10 karakter.',
        ]);

        return $this->processApproval('Validasi Ditolak', [
            'validasi_dokumen' => 'ditolak',
            'reject_by' => auth()->id(),
            'catatan_validasi_dokumen_ditolak' => $this->catatan_approval,
            'current_step' => 1,
        ]);
    }

    /**
     * Persetujuan debitur (Step 3 -> 4).
     */
    public function persetujuanDebiturSetuju()
    {
        $this->validate([
            'catatan_approval' => 'nullable|string',
        ]);

        return $this->processApproval('Debitur Setuju', [
            'approve_by' => auth()->id(),
            'catatan_persetujuan_debitur' => $this->catatan_approval,
            'deviasi' => $this->latestHistory->deviasi ?? null,
            'nominal_yang_disetujui' => $this->latestHistory->nominal_yang_disetujui ?? $this->nominal_yang_disetujui,
            'tanggal_pencairan' => $this->latestHistory->tanggal_pencairan ?? null,
            'current_step' => 4,
        ]);
    }

    /**
     * Penolakan oleh debitur.
     */
    public function persetujuanDebiturTolak()
    {
        $this->validate([
            'catatan_approval' => 'required|string|min:10',
        ], [
            'catatan_approval.required' => 'Catatan penolakan wajib diisi.',
            'catatan_approval.min' => 'Catatan penolakan minimal 10 karakter.',
        ]);

        return $this->processApproval('Pengajuan Ditolak Debitur', [
            'reject_by' => auth()->id(),
            'catatan_penolakan_debitur' => $this->catatan_approval,
            'current_step' => 3,
        ]);
    }

    /**
     * Persetujuan CEO SKI (Step 4 -> 5).
     */
    public function persetujuanCEOSetuju()
    {
        $this->validate([
            'catatan_approval' => 'nullable|string',
        ]);

        return $this->processApproval('Disetujui oleh CEO SKI', [
            'approve_by' => auth()->id(),
            'catatan_persetujuan_ceo' => $this->catatan_approval,
            'deviasi' => $this->latestHistory->deviasi ?? null,
            'nominal_yang_disetujui' => $this->latestHistory->nominal_yang_disetujui ?? $this->nominal_yang_disetujui,
            'tanggal_pencairan' => $this->latestHistory->tanggal_pencairan ?? null,
            'current_step' => 5,
        ]);
    }

    /**
     * Penolakan oleh CEO SKI.
     */
    public function persetujuanCEOTolak()
    {
        $this->validate([
            'catatan_approval' => 'required|string|min:10',
        ], [
            'catatan_approval.required' => 'Catatan penolakan wajib diisi.',
            'catatan_approval.min' => 'Catatan penolakan minimal 10 karakter.',
        ]);

        return $this->processApproval('Ditolak oleh CEO SKI', [
            'reject_by' => auth()->id(),
            'catatan_penolakan_ceo' => $this->catatan_approval,
            'current_step' => 2, // Kembali ke step validasi dokumen
        ]);
    }

    /**
     * Persetujuan Direktur SKI (Step 5 -> 6).
     */
    public function persetujuanDirekturSetuju()
    {
        $this->validate([
            'catatan_approval' => 'nullable|string',
        ]);

        return $this->processApproval('Disetujui oleh Direktur SKI', [
            'approve_by' => auth()->id(),
            'catatan_persetujuan_direktur' => $this->catatan_approval,
            'deviasi' => $this->latestHistory->deviasi ?? null,
            'nominal_yang_disetujui' => $this->latestHistory->nominal_yang_disetujui ?? $this->nominal_yang_disetujui,
            'tanggal_pencairan' => $this->latestHistory->tanggal_pencairan ?? null,
            'current_step' => 6,
        ]);
    }

    /**
     * Penolakan oleh Direktur SKI.
     */
    public function persetujuanDirekturTolak()
    {
        $this->validate([
            'catatan_approval' => 'required|string|min:10',
        ], [
            'catatan_approval.required' => 'Catatan penolakan wajib diisi.',
            'catatan_approval.min' => 'Catatan penolakan minimal 10 karakter.',
        ]);

        return $this->processApproval('Ditolak oleh Direktur SKI', [
            'reject_by' => auth()->id(),
            'catatan_penolakan_direktur' => $this->catatan_approval,
            'current_step' => 2, // Kembali ke step validasi dokumen
        ]);
    }

    /**
     * Generate kontrak peminjaman (Step 6 → 7).
     */
    public function generateKontrak()
    {
        $this->validate([
            'biaya_administrasi' => 'required|numeric|min:0',
        ]);

        // Generate nomor kontrak menggunakan ContractNumberService
        $debitur = $this->pengajuan->debitur;
        $noKontrak = \App\Services\ContractNumberService::generate(
            $debitur->kode_perusahaan,
            $this->jenis_pembiayaan
        );

        return $this->processApproval('Generate Kontrak', [
            'approve_by' => auth()->id(),
            'current_step' => 7,
        ], function ($pengajuan) use ($noKontrak) {
            $pengajuan->update([
                'no_kontrak' => $noKontrak,
                'biaya_administrasi' => $this->biaya_administrasi,
            ]);
        });
    }

    /**
     * Upload dokumen transfer (Step 7 → 8).
     */
    public function uploadDokumenTransfer()
    {
        $this->validate([
            'dokumen_transfer' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $path = $this->dokumen_transfer->store('dokumen-transfer', 'public');

        return $this->processApproval('Menunggu Konfirmasi Debitur', [
            'approve_by' => auth()->id(),
            'current_step' => 8,
        ], function ($pengajuan) use ($path) {
            $pengajuan->update([
                'upload_bukti_transfer' => $path,
            ]);
        });
    }

    /**
     * Konfirmasi debitur terima dana (Step 8 → 9).
     * Otomatis mengisi tanggal_jatuh_tempo berdasarkan tanggal_pencairan + 30 hari
     * (atau tanggal_pencairan + tenor * 30 hari untuk jenis Installment).
     *
     * tanggal_pencairan diambil langsung dari history manapun karena step 7 & 8
     * (generateKontrak & uploadDokumenTransfer) tidak meneruskan field ini ke latestHistory.
     */
    public function konfirmasiDebiturTerima()
    {
        // Ambil tanggal_pencairan dari history yang menyimpannya (step validasi dokumen)
        $tanggalPencairan = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $this->id)
            ->whereNotNull('tanggal_pencairan')
            ->orderBy('created_at', 'desc')
            ->value('tanggal_pencairan');

        return $this->processApproval('Dana Sudah Dicairkan', [
            'approve_by' => auth()->id(),
            'current_step' => 9,
            'tanggal_pencairan' => $tanggalPencairan,
        ], function ($pengajuan) use ($tanggalPencairan) {
            if ($tanggalPencairan) {
                $pencairan = Carbon::parse($tanggalPencairan);
                $isInstallment = ($pengajuan->jenis_pembiayaan === 'Installment');
                $tenor = (int) ($pengajuan->tenor_pembayaran ?? 0);

                $tanggalJatuhTempo = ($isInstallment && $tenor > 0)
                    ? $pencairan->copy()->addDays($tenor * 30)
                    : $pencairan->copy()->addDays(30);

                $pengajuan->update([
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                    'sisa_bayar_pokok'    => $pengajuan->sisa_bayar_pokok ?? $pengajuan->total_pinjaman,
                ]);
            }
        });
    }

    /**
     * Konfirmasi debitur tolak penerimaan dana.
     */
    public function konfirmasiDebiturTolak()
    {
        $this->validate([
            'catatan_approval' => 'required|string|min:10',
        ], [
            'catatan_approval.required' => 'Catatan penolakan wajib diisi.',
            'catatan_approval.min' => 'Catatan penolakan minimal 10 karakter.',
        ]);

        return $this->processApproval('Konfirmasi Ditolak Debitur', [
            'reject_by' => auth()->id(),
            'catatan_penolakan_konfirmasi' => $this->catatan_approval,
            'current_step' => 7,
        ]);
    }

    /**
     * Proses utama untuk semua jenis approval.
     *
     * @param string $status Status baru
     * @param array $historyData Data tambahan untuk history
     * @param callable|null $additionalAction Aksi tambahan setelah update status
     * @return bool
     */
    protected function processApproval(string $status, array $historyData = [], ?callable $additionalAction = null): bool
    {
        if (!in_array($status, $this->validStatuses)) {
            $this->dispatch('approvalError', message: 'Status tidak valid');
            return false;
        }

        DB::beginTransaction();
        try {
            $pengajuan = PengajuanPeminjaman::findOrFail($this->id);

            // Update status pengajuan
            $pengajuan->status = $status;
            $pengajuan->save();

            // Buat history record
            $historyData = array_merge([
                'id_pengajuan_peminjaman' => $pengajuan->id_pengajuan_peminjaman,
                'date' => now()->format('Y-m-d'),
                'status' => $status,
            ], $historyData);

            HistoryStatusPengajuanPinjaman::create($historyData);

            // Jalankan aksi tambahan jika ada
            if ($additionalAction) {
                $additionalAction($pengajuan);
            }

            DB::commit();

            // Refresh data dan kirim event sukses
            $this->refreshData();
            $this->dispatch('approvalSuccess', status: $status);
            $this->dispatch('closeModal');

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('approvalError', message: 'Terjadi kesalahan: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse tanggal dari berbagai format ke Y-m-d.
     */
    protected function parseTanggal($tanggal): ?string
    {
        if (empty($tanggal)) {
            return null;
        }

        try {
            // Format dd/mm/yyyy
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggal)) {
                return Carbon::createFromFormat('d/m/Y', $tanggal)->format('Y-m-d');
            }
            // Format lainnya
            return Carbon::parse($tanggal)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Mendapatkan step berdasarkan status.
     */
    protected function getStepFromStatus(string $status): int
    {
        return $this->statusToStepMapping[$status] ?? 1;
    }

    /**
     * Reset form approval fields.
     */
    protected function resetApprovalForm(): void
    {
        $this->deviasi = null;
        $this->catatan_approval = '';
    }
}
