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
        'Konfirmasi Ditolak Debitur'
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
            'tanggal_pencairan' => 'required|date',
            'persentase_bagi_hasil' => 'required|numeric|min:0|max:100',
        ]);

        $totalBagiHasil = $this->nominal_yang_disetujui * ($this->persentase_bagi_hasil / 100);

        return $this->processApproval('Dokumen Tervalidasi', [
            'validasi_dokumen' => 'disetujui',
            'approve_by' => auth()->id(),
            'deviasi' => $this->deviasi,
            'nominal_yang_disetujui' => $this->nominal_yang_disetujui,
            'tanggal_pencairan' => $this->parseTanggal($this->tanggal_pencairan),
            'persentase_bagi_hasil' => $this->persentase_bagi_hasil,
            'total_bagi_hasil' => $totalBagiHasil,
            'catatan_validasi_dokumen_disetujui' => $this->catatan_approval,
            'current_step' => 3,
        ], function ($pengajuan) use ($totalBagiHasil) {
            // Update data peminjaman
            $pengajuan->update([
                'total_pinjaman' => $this->nominal_yang_disetujui,
                'persentase_bagi_hasil' => $this->persentase_bagi_hasil,
                'total_bagi_hasil' => $totalBagiHasil,
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
