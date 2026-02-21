<?php

namespace App\Livewire\PenyesuaianCicilan;

use App\Models\PenyesuaianCicilan;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PreviewKontrak
{
    public function __invoke(string $id)
    {
        $program = PenyesuaianCicilan::with([
            'PengajuanCicilan.debitur',
        ])->findOrFail($id);

        // Authorization check
        $user = Auth::user();
        $isAdmin = $user && $user->hasRole(['super-admin', 'admin', 'sfinance', 'Finance SKI', 'CEO SKI', 'Direktur SKI']);

        if (!$isAdmin) {
            $debitur = MasterDebiturDanInvestor::where('user_id', Auth::id())->first();
            $pengajuanDebiturId = $program->PengajuanCicilan->id_debitur ?? null;

            if (!$debitur || $debitur->id_debitur !== $pengajuanDebiturId) {
                abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
            }
        }

        // Generate preview nomor jika belum ada nomor kontrak
        $previewNomor = null;
        if (!$program->nomor_kontrak_restrukturisasi) {
            $previewNomor = $this->generatePreviewNomor($program);
        }

        return view('livewire.penyesuaian-cicilan.preview-kontrak', [
            'program' => $program,
            'previewNomor' => $previewNomor,
        ]);
    }

    protected function generatePreviewNomor($program): string
    {
        $pengajuan = $program->PengajuanCicilan;
        $debitur = $pengajuan->debitur;

        $kodePerusahaan = $debitur->kode_perusahaan ?? 'XXX';
        $tanggal = Carbon::now()->format('dmY');

        $countThisMonth = PenyesuaianCicilan::whereNotNull('nomor_kontrak_restrukturisasi')
            ->whereYear('kontrak_generated_at', Carbon::now()->year)
            ->whereMonth('kontrak_generated_at', Carbon::now()->month)
            ->count();

        $runningNumber = str_pad($countThisMonth + 1, 2, '0', STR_PAD_LEFT);

        // Format: KODE-RUNNING_NUMBER-DDMMYYYY
        return strtoupper($kodePerusahaan) . '-' . $runningNumber . '-' . $tanggal;
    }
}
