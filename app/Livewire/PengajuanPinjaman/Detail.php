<?php

namespace App\Livewire\PengajuanPinjaman;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Enums\JenisPembiayaanEnum;
use App\Models\PengajuanPeminjaman;
use App\Attributes\ParameterIDRoute;
use App\Models\HistoryStatusPengajuanPinjaman;
use App\Livewire\PengajuanPinjaman\Approval\ApprovalActions;
use App\Livewire\PengajuanPinjaman\Event\HandleDetailEvents;
use App\Livewire\PengajuanPinjaman\FieldsInput\FieldInputDetail;

/**
 * Class Detail
 * 
 * Livewire component untuk halaman detail pengajuan peminjaman.
 * Menampilkan informasi lengkap pengajuan dan menangani proses approval.
 */
class Detail extends Component
{
    use WithFileUploads,
        FieldInputDetail,      // Properties untuk detail
        HandleDetailEvents,    // Event handlers
        ApprovalActions;       // Approval logic

    #[ParameterIDRoute]
    public $id;

    /**
     * Model pengajuan peminjaman.
     */
    public $pengajuan;

    /**
     * Judul halaman.
     */
    public $title;

    /**
     * Tipe header berdasarkan jenis pembiayaan.
     */
    public $headerType;

    /**
     * Mount component dengan ID pengajuan.
     */
    public function mount($id)
    {
        $this->id = $id;
        $this->loadData();
    }

    /**
     * Render view component.
     */
    public function render()
    {
        return view('livewire.pengajuan-pinjaman.detail');
    }

    /**
     * Load semua data pengajuan peminjaman.
     */
    protected function loadData(): void
    {
        $this->pengajuan = PengajuanPeminjaman::with([
            'debitur.kol', 
            'instansi', 
            'buktiPeminjaman'
        ])->findOrFail($this->id);

        $this->setDataPerusahaan();
        $this->setDataPeminjaman();
        $this->setDataWorkflow();
        $this->setDetailsData();
        
        $this->title = 'Detail Pengajuan Peminjaman - ' . $this->nomor_peminjaman;
    }

    /**
     * Refresh data setelah approval atau perubahan.
     */
    public function refreshData(): void
    {
        $this->loadData();
    }

    /**
     * Set data perusahaan dari debitur.
     */
    protected function setDataPerusahaan(): void
    {
        $debitur = $this->pengajuan->debitur;

        $this->nama_perusahaan = $debitur->nama ?? '';
        $this->nama_ceo = $debitur->nama_ceo ?? '';
        $this->alamat = $debitur->alamat ?? '';
        $this->nama_bank = $this->pengajuan->nama_bank;
        $this->no_rekening = $this->pengajuan->no_rekening;
        $this->nama_rekening = $this->pengajuan->nama_rekening;
        $this->lampiran_sid = $this->pengajuan->lampiran_sid;
        $this->nilai_kol = $this->pengajuan->nilai_kol;
        $this->tanda_tangan = $debitur->tanda_tangan ?? null;
    }

    /**
     * Set data peminjaman utama.
     */
    protected function setDataPeminjaman(): void
    {
        $persentase = $this->pengajuan->persentase_bagi_hasil 
            ?? ($this->pengajuan->instansi?->persentase_bagi_hasil ?? null);

        $this->nomor_peminjaman = $this->pengajuan->nomor_peminjaman;
        $this->no_kontrak = $this->pengajuan->no_kontrak;
        $this->jenis_pembiayaan = $this->pengajuan->jenis_pembiayaan;
        $this->sumber_pembiayaan = $this->pengajuan->sumber_pembiayaan;
        $this->instansi = $this->pengajuan->instansi?->nama_instansi ?? null;
        $this->tujuan_pembiayaan = $this->pengajuan->tujuan_pembiayaan;
        $this->catatan_lainnya = $this->pengajuan->catatan_lainnya;
        $this->status = $this->pengajuan->status;

        // Nominal & Tanggal
        $this->nominal_pinjaman = $this->pengajuan->nominal_pengajuan_awal 
            ?? $this->pengajuan->total_pinjaman;
        $this->harapan_tanggal_pencairan = $this->pengajuan->harapan_tanggal_pencairan;
        $this->rencana_tgl_pembayaran = $this->pengajuan->rencana_tgl_pembayaran;
        $this->persentase_bagi_hasil = $persentase;
        $this->total_bagi_hasil = $this->pengajuan->total_bagi_hasil;
        $this->pembayaran_total = $this->pengajuan->pembayaran_total;

        // Installment specific
        $this->tenor_pembayaran = $this->pengajuan->tenor_pembayaran;
        $this->pps = $this->pengajuan->pps;
        $this->s_finance = $this->pengajuan->s_finance;
        $this->yang_harus_dibayarkan = $this->pengajuan->yang_harus_dibayarkan;

        // Factoring specific
        $this->total_nominal_yang_dialihkan = $this->pengajuan->total_nominal_yang_dialihkan;

        // Upload
        $this->upload_bukti_transfer = $this->pengajuan->upload_bukti_transfer;

        // Header type untuk tampilan
        $this->headerType = strtolower(str_replace(' ', '_', $this->jenis_pembiayaan ?? 'invoice_financing'));
    }

    /**
     * Set data workflow (step, history).
     */
    protected function setDataWorkflow(): void
    {
        // Get latest history
        $this->latestHistory = HistoryStatusPengajuanPinjaman::where(
            'id_pengajuan_peminjaman', 
            $this->pengajuan->id_pengajuan_peminjaman
        )
            ->orderBy('created_at', 'desc')
            ->first();

        // Get all history
        $this->allHistory = HistoryStatusPengajuanPinjaman::where(
            'id_pengajuan_peminjaman', 
            $this->pengajuan->id_pengajuan_peminjaman
        )
            ->orderBy('created_at', 'desc')
            ->with(['approvedBy', 'rejectedBy', 'submittedBy'])
            ->get();

        // Set current step
        if ($this->latestHistory?->current_step) {
            $this->currentStep = $this->latestHistory->current_step;
        } else {
            $this->currentStep = $this->getStepFromStatus($this->status);
        }

        // Set data dari latest history
        if ($this->latestHistory) {
            $this->nominal_yang_disetujui = $this->latestHistory->nominal_yang_disetujui;
            $this->tanggal_pencairan = $this->latestHistory->tanggal_pencairan;
        }

        // Generate preview nomor kontrak jika belum ada dan sudah di step 6+
        if (empty($this->no_kontrak) && $this->currentStep >= 6) {
            $debitur = $this->pengajuan->debitur;
            if ($debitur && !empty($debitur->kode_perusahaan)) {
                $this->preview_no_kontrak = \App\Services\ContractNumberService::generate(
                    $debitur->kode_perusahaan,
                    $this->jenis_pembiayaan
                );
            }
        }
    }

    /**
     * Set data detail (bukti peminjaman/invoice).
     */
    protected function setDetailsData(): void
    {
        $this->detailsData = $this->pengajuan->buktiPeminjaman->map(function ($bukti) {
            $baseData = [
                'id' => $bukti->id_bukti_peminjaman,
                'no_invoice' => $bukti->no_invoice,
                'no_kontrak' => $bukti->no_kontrak,
                'nama_client' => $bukti->nama_client,
                'nilai_invoice' => $bukti->nilai_invoice,
                'nilai_pinjaman' => $bukti->nilai_pinjaman,
                'nilai_bagi_hasil' => $bukti->nilai_bagi_hasil,
                'invoice_date' => $bukti->invoice_date,
                'kontrak_date' => $bukti->kontrak_date,
                'due_date' => $bukti->due_date,
                'nama_barang' => $bukti->nama_barang,
                'dokumen_invoice' => $bukti->dokumen_invoice,
                'dokumen_kontrak' => $bukti->dokumen_kontrak,
                'dokumen_lainnya' => $bukti->dokumen_lainnya,
            ];

            return $baseData;
        })->toArray();
    }

    /**
     * Cek apakah user memiliki permission tertentu.
     */
    public function hasPermission(string $permission): bool
    {
        return auth()->user()->can($permission);
    }

    /**
     * Format tanggal untuk tampilan.
     */
    public function formatTanggal($tanggal, string $format = 'd/m/Y'): string
    {
        if (empty($tanggal)) {
            return '-';
        }

        try {
            return Carbon::parse($tanggal)->format($format);
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Format nominal ke Rupiah.
     */
    public function formatRupiah($nominal): string
    {
        if (empty($nominal) || !is_numeric($nominal)) {
            return 'Rp 0';
        }

        return 'Rp ' . number_format((float) $nominal, 0, ',', '.');
    }

    /**
     * Cek apakah jenis pembiayaan adalah Installment.
     */
    public function isInstallment(): bool
    {
        return $this->jenis_pembiayaan === JenisPembiayaanEnum::INSTALLMENT;
    }

    /**
     * Cek apakah jenis pembiayaan adalah Factoring.
     */
    public function isFactoring(): bool
    {
        return $this->jenis_pembiayaan === JenisPembiayaanEnum::FACTORING;
    }

    /**
     * Cek apakah tombol approval tertentu harus ditampilkan.
     */
    public function shouldShowButton(string $buttonType): bool
    {
        return match ($buttonType) {
            'submit_dokumen' => $this->status === 'Draft',
            'setujui_peminjaman' => in_array($this->status, ['Submit Dokumen', 'Ditolak oleh CEO SKI']),
            'persetujuan_debitur' => $this->status === 'Dokumen Tervalidasi',
            'persetujuan_ceo' => $this->status === 'Debitur Setuju',
            'persetujuan_direktur' => $this->status === 'Disetujui oleh CEO SKI',
            default => false,
        };
    }

    /**
     * Cek apakah alert peninjauan harus ditampilkan.
     */
    public function shouldShowAlertPeninjauan(): bool
    {
        return in_array($this->status, ['Submit Dokumen', 'Debitur Setuju']);
    }
}
