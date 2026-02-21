<?php

namespace App\Livewire\PenyesuaianCicilan;

use App\Models\PenyesuaianCicilan;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class GenerateKontrak extends Component
{
    public PenyesuaianCicilan $program;

    // Form fields
    public $jaminan = '';

    // Data kontrak
    public $dataKontrak = [];
    public $previewNomorKontrak = '';

    public function mount(string $id): void
    {
        $this->program = PenyesuaianCicilan::with([
            'PengajuanCicilan.debitur',
            'jadwalAngsuran' => fn($query) => $query->orderBy('no'),
        ])->findOrFail($id);

        // Authorization check
        $user = Auth::user();
        $isAdmin = $user && $user->hasRole(['super-admin', 'admin', 'sfinance', 'Finance SKI', 'CEO SKI', 'Direktur SKI']);

        if (!$isAdmin) {
            $debitur = MasterDebiturDanInvestor::where('user_id', Auth::id())->first();
            $pengajuanDebiturId = $this->program->PengajuanCicilan->id_debitur ?? null;

            if (!$debitur || $debitur->id_debitur !== $pengajuanDebiturId) {
                abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
            }
        }

        // Jika kontrak sudah di-generate, redirect ke halaman show
        if ($this->program->kontrak_generated_at) {
            redirect()->route('penyesuaian-cicilan.show', $this->program->id_penyesuaian_cicilan)->send();
            return;
        }

        // Load data kontrak
        $this->loadDataKontrak();

        // Load existing jaminan if any
        $this->jaminan = $this->program->jaminan ?? '';
    }

    protected function loadDataKontrak(): void
    {
        $pengajuan = $this->program->PengajuanCicilan;
        $debitur = $pengajuan->debitur;

        // Generate preview nomor kontrak
        $this->previewNomorKontrak = $this->generateNomorKontrak(true);

        $this->dataKontrak = [
            // Data dari Pengajuan Restrukturisasi
            'jenis_restrukturisasi' => $pengajuan->jenis_restrukturisasi ?? [],
            'nama_perusahaan' => $pengajuan->nama_perusahaan ?? '-',
            'nama_pimpinan' => $pengajuan->nama_pic ?? '-',
            'alamat_perusahaan' => $pengajuan->alamat_kantor ?? ($debitur->alamat ?? '-'),
            'tujuan_restrukturisasi' => $pengajuan->alasan_restrukturisasi ?? '-',
            'nilai_plafon_awal' => $pengajuan->jumlah_plafon_awal ?? 0,
            'nomor_kontrak_pembiayaan' => $pengajuan->nomor_kontrak_pembiayaan ?? '-',

            // Data dari Program Restrukturisasi
            'nilai_plafon_pembiayaan' => $this->program->plafon_pembiayaan ?? 0,
            'metode_perhitungan' => $this->program->metode_perhitungan ?? '-',
            'jangka_waktu' => $this->program->jangka_waktu_total ?? 0,
            'tanggal_mulai_cicilan' => $this->program->tanggal_mulai_cicilan,
            'suku_bunga' => $this->program->suku_bunga_per_tahun ?? 0,
            'masa_tenggang' => $this->program->masa_tenggang ?? 0,

            // Nama CEO Kreditur (untuk tanda tangan)
            'nama_ceo_kreditur' => 'Muhamad Kurniawan',
        ];
    }

    /**
     * Generate nomor kontrak format: [KODE_PERUSAHAAN]-[NO]-[DDMMYYYY]
     */
    protected function generateNomorKontrak(bool $isPreview = false): string
    {
        $pengajuan = $this->program->PengajuanCicilan;
        $debitur = $pengajuan->debitur;

        // Get kode perusahaan
        $kodePerusahaan = $debitur->kode_perusahaan ?? 'XXX';

        // Get date in format DDMMYYYY
        $tanggal = Carbon::now()->format('dmY');

        // Get running number for this month
        $countThisMonth = PenyesuaianCicilan::whereNotNull('nomor_kontrak_restrukturisasi')
            ->whereYear('kontrak_generated_at', Carbon::now()->year)
            ->whereMonth('kontrak_generated_at', Carbon::now()->month)
            ->count();

        $runningNumber = str_pad($countThisMonth + 1, 2, '0', STR_PAD_LEFT);

        // Format: KODE-RUNNING_NUMBER-DDMMYYYY
        return strtoupper($kodePerusahaan) . '-' . $runningNumber . '-' . $tanggal;
    }

    /**
     * Generate dan simpan kontrak
     */
    public function generateKontrak()
    {
        // Validate jaminan is required
        $this->validate([
            'jaminan' => 'required|string|min:3',
        ], [
            'jaminan.required' => 'Jaminan harus diisi.',
            'jaminan.min' => 'Jaminan minimal 3 karakter.',
        ]);

        try {
            DB::beginTransaction();

            // Generate final nomor kontrak
            $nomorKontrak = $this->generateNomorKontrak();

            // Update program
            $this->program->update([
                'nomor_kontrak_restrukturisasi' => $nomorKontrak,
                'jaminan' => $this->jaminan,
                'kontrak_generated_at' => Carbon::now(),
                'status' => 'Berjalan',
            ]);

            DB::commit();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Kontrak Berhasil Di-generate!',
                'text' => 'Nomor Kontrak: ' . $nomorKontrak,
                'redirect_url' => route('penyesuaian-cicilan.show', $this->program->id_penyesuaian_cicilan),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Generate Kontrak Error: ' . $e->getMessage());

            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Gagal Generate Kontrak',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.penyesuaian-cicilan.generate-kontrak');
    }

    public function getLayout()
    {
        return 'layouts.app';
    }
}
