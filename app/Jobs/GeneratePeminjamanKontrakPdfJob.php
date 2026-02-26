<?php

namespace App\Jobs;

use App\Models\PengajuanPeminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class GeneratePeminjamanKontrakPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum attempts before the job is marked as failed.
     */
    public int $tries = 3;

    /**
     * Timeout (seconds) for this job.
     * DomPDF with complex HTML can be slow; allow up to 120s.
     */
    public int $timeout = 120;

    public function __construct(
        protected string $idPengajuan,
        protected ?string $userId = null,
    ) {}

    public function handle(): void
    {
        $pengajuan = PengajuanPeminjaman::with('debitur')->findOrFail($this->idPengajuan);

        // Build the kontrak data inline (mirrors KontrakPdfHandler::prepareKontrakData)
        $latestHistory = $pengajuan->historyStatus()
            ->whereNotNull('nominal_yang_disetujui')
            ->orderBy('created_at', 'desc')
            ->first();

        $nilaiPembiayaan = $latestHistory->nominal_yang_disetujui ?? $pengajuan->total_pinjaman ?? 0;
        $biayaAdmin      = 0;

        $kontrak = [
            'id_peminjaman'        => $pengajuan->id_pengajuan_peminjaman,
            'no_kontrak'           => $pengajuan->no_kontrak ?? '',
            'no_kontrak2'          => $pengajuan->no_kontrak ?? '',
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
            'biaya_admin'          => 'Rp. ' . number_format($biayaAdmin, 0, ',', '.'),
            'biaya_admin_raw'      => $biayaAdmin,
            'nisbah'               => ($pengajuan->persentase_bunga ?? 2) . '% flat / bulan',
            'denda_keterlambatan'  => ($pengajuan->persentase_bunga ?? 2) . '% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut',
            'jaminan'              => $pengajuan->jenis_pembiayaan ?? 'Invoice Financing',
            'tanda_tangan'         => $pengajuan->debitur->tanda_tangan ?? null,
        ];

        // Generate PDF using the same view used by the preview page
        $pdf = Pdf::loadView('livewire.pengajuan-pinjaman.preview-kontrak', ['kontrak' => $kontrak]);
        $pdf->setPaper('A4', 'portrait');

        // Persist to public storage so the user can download via a URL
        $noKontrak = $kontrak['no_kontrak'] ?: $this->idPengajuan;
        $filename  = 'Kontrak_Peminjaman_' . str_replace(['/', '\\'], '_', $noKontrak) . '.pdf';
        $path      = 'kontrak/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        Log::info("Kontrak PDF generated: {$path}");

        cache()->put(
            "kontrak_pdf_ready:{$this->idPengajuan}",
            Storage::disk('public')->url($path),
            now()->addHours(6),
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GeneratePeminjamanKontrakPdfJob failed for {$this->idPengajuan}: " . $exception->getMessage());
    }
}
