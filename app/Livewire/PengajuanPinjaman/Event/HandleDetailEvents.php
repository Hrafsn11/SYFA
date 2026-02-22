<?php

namespace App\Livewire\PengajuanPinjaman\Event;

use Livewire\Attributes\On;
use App\Livewire\Traits\HandleComponentEvent;

/**
 * Trait HandleDetailEvents
 * 
 * Menangani event-event pada halaman detail peminjaman.
 * Termasuk event dari modal approval dan perubahan state.
 */
trait HandleDetailEvents
{
    use HandleComponentEvent;

    /**
     * Event listener ketika approval berhasil dilakukan.
     * Memperbarui state dan data setelah approval.
     */
    #[On('approvalSuccess')]
    public function handleApprovalSuccess($status, $message = null)
    {
        $this->refreshData();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message ?? 'Status berhasil diperbarui ke: ' . $status
        ]);
    }

    /**
     * Event listener ketika approval gagal.
     */
    #[On('approvalError')]
    public function handleApprovalError($message = 'Terjadi kesalahan saat memproses approval')
    {
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => $message
        ]);
    }

    /**
     * Event listener untuk refresh data dari modal.
     */
    #[On('refreshDetail')]
    public function handleRefreshDetail()
    {
        $this->refreshData();
    }

    /**
     * Event listener ketika modal approval dibuka.
     * Mempersiapkan data yang diperlukan untuk form approval.
     */
    #[On('openApprovalModal')]
    public function handleOpenApprovalModal($modalType)
    {
        $this->prepareApprovalData($modalType);
        $this->dispatch('showModal', modalType: $modalType);
    }

    /**
     * Event listener untuk memperbarui step saat ini.
     */
    #[On('stepUpdated')]
    public function handleStepUpdated($step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
        }
    }

    /**
     * Mempersiapkan data untuk form approval berdasarkan tipe modal.
     */
    protected function prepareApprovalData($modalType)
    {
        $isTolakModal = str_contains($modalType, 'tolak');

        if ($isTolakModal) {
            // Hanya reset catatan untuk modal tolak
            $this->catatan_approval = '';
            return;
        }

        // Untuk modal persetujuan, set nilai dari latest history
        $this->deviasi = $this->latestHistory->deviasi ?? null;
        $this->catatan_approval = '';

        // Set nilai-nilai dari latest history jika ada
        if ($this->latestHistory) {
            $this->nominal_yang_disetujui = $this->latestHistory->nominal_yang_disetujui ?? $this->nominal_pinjaman;
            $this->tanggal_pencairan = $this->latestHistory->tanggal_pencairan ?? null;
            $this->persentase_bunga = $this->latestHistory->persentase_bunga ?? null;
        }
    }

    /**
     * Property untuk menyimpan detail history yang sedang ditampilkan.
     */
    public $selectedHistory = null;
    public $showHistoryModal = false;

    /**
     * Menampilkan detail history status pengajuan.
     * 
     * @param string|int $historyId
     */
    public function showHistoryDetail($historyId)
    {
        $history = \App\Models\HistoryStatusPengajuanPinjaman::with(['approvedBy', 'rejectedBy', 'submittedBy'])
            ->find($historyId);

        if ($history) {
            $this->selectedHistory = [
                'id' => $history->id_history_status_pengajuan_pinjaman,
                'status' => $history->status,
                'date' => $history->date,
                'created_at' => $history->created_at,
                'deviasi' => $history->deviasi,
                'nominal_yang_disetujui' => $history->nominal_yang_disetujui,
                'tanggal_pencairan' => $history->tanggal_pencairan,
                'persentase_bunga' => $history->persentase_bunga,
                'total_bunga' => $history->total_bunga,
                'catatan' => $history->catatan_validasi_dokumen_disetujui
                    ?? $history->catatan_validasi_dokumen_ditolak
                    ?? $history->catatan_persetujuan_debitur
                    ?? $history->catatan_penolakan_debitur
                    ?? $history->catatan_persetujuan_ceo
                    ?? $history->catatan_penolakan_ceo
                    ?? $history->catatan_persetujuan_direktur
                    ?? $history->catatan_penolakan_direktur
                    ?? '-',
                'approved_by' => $history->approvedBy?->name ?? null,
                'rejected_by' => $history->rejectedBy?->name ?? null,
                'submitted_by' => $history->submittedBy?->name ?? null,
            ];

            $this->dispatch('open-modal', modal: 'modal-history-detail');
        }
    }
}
