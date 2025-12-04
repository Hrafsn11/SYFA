<?php

namespace App\Livewire;

use App\Models\ProgramRestrukturisasi;
use App\Models\JadwalAngsuran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProgramRestrukturisasiEdit extends ProgramRestrukturisasiCreate
{
    public bool $isEdit = true;
    public ProgramRestrukturisasi $program;

    public string $pageTitle = 'Edit Program Restrukturisasi';
    public string $pageSubtitle = 'Perbarui parameter program restrukturisasi';
    public string $submitLabel = 'Perbarui Program Restrukturisasi';

    public function mount(?string $id = null): void
    {
        $this->tanggal_mulai_cicilan = date('Y-m-d');

        if ($id === null) {
            abort(404);
        }

        $this->program = ProgramRestrukturisasi::with([
            'pengajuanRestrukturisasi.debitur',
            'jadwalAngsuran' => fn ($query) => $query->orderBy('no'),
        ])->findOrFail($id);

        $pengajuan = $this->program->pengajuanRestrukturisasi;

        $this->id_pengajuan_restrukturisasi = $this->program->id_pengajuan_restrukturisasi;
        $this->nama_debitur = $pengajuan?->debitur?->nama ?? $pengajuan?->nama_perusahaan ?? '-';
        $this->nomor_kontrak = $pengajuan?->nomor_kontrak_pembiayaan ?? '-';
        $this->metode_perhitungan = $this->program->metode_perhitungan;
        
        // [FIX] Float cast
        $this->plafon_pembiayaan = (float) $this->program->plafon_pembiayaan;
        $this->suku_bunga_per_tahun = (float) $this->program->suku_bunga_per_tahun;
        
        $this->jangka_waktu_total = (int) $this->program->jangka_waktu_total;
        $this->masa_tenggang = (int) $this->program->masa_tenggang;
        $this->tanggal_mulai_cicilan = optional($this->program->tanggal_mulai_cicilan)->format('Y-m-d');

        $this->total_pokok = (float) $this->program->total_pokok;
        $this->total_margin = (float) $this->program->total_margin;
        $this->total_cicilan = (float) $this->program->total_cicilan;

        $this->jadwal_angsuran = $this->program->jadwalAngsuran->map(function ($item) {
            return [
                'no' => $item->no,
                'tanggal_jatuh_tempo' => optional($item->tanggal_jatuh_tempo)->format('d F Y') ?? '-',
                'tanggal_jatuh_tempo_raw' => optional($item->tanggal_jatuh_tempo)->format('Y-m-d'),
                'pokok' => (float) $item->pokok,
                'margin' => (float) $item->margin,
                'total_cicilan' => (float) $item->total_cicilan,
                'catatan' => $item->catatan,
                'is_grace_period' => (bool) $item->is_grace_period,
                'status' => $item->status,
            ];
        })->toArray();

        $this->show_jadwal = true;
        $this->loadApprovedRestrukturisasi($this->id_pengajuan_restrukturisasi);
    }

    public function simpan()
    {
        if (empty($this->jadwal_angsuran) || !$this->show_jadwal) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Belum Dihitung',
                'text' => 'Silakan klik tombol "Hitung Jadwal Angsuran" terlebih dahulu.',
            ]);
            return;
        }

        try {
            $this->validate([
                'id_pengajuan_restrukturisasi' => 'required|exists:pengajuan_restrukturisasi,id_pengajuan_restrukturisasi',
                'metode_perhitungan' => 'required|in:Flat,Anuitas',
                'plafon_pembiayaan' => 'required|numeric|min:0',
                'suku_bunga_per_tahun' => 'required|numeric|min:0|max:100',
                'jangka_waktu_total' => 'required|integer|min:1',
                'masa_tenggang' => 'required|integer|min:0',
                'tanggal_mulai_cicilan' => 'required|date',
                'jadwal_angsuran' => 'required|array|min:1',
            ], [
                'id_pengajuan_restrukturisasi.required' => 'Silakan pilih pengajuan restrukturisasi.',
                'suku_bunga_per_tahun.max' => 'Suku bunga tidak boleh lebih dari 100%.',
                'jadwal_angsuran.required' => 'Mohon hitung jadwal angsuran sebelum menyimpan.',
            ]);

        } catch (ValidationException $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Gagal Menyimpan',
                'text' => implode("\n", collect($e->errors())->flatten()->toArray()),
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->program->update([
                'id_pengajuan_restrukturisasi' => $this->id_pengajuan_restrukturisasi,
                'metode_perhitungan' => $this->metode_perhitungan,
                'plafon_pembiayaan' => $this->plafon_pembiayaan,
                'suku_bunga_per_tahun' => $this->suku_bunga_per_tahun,
                'jangka_waktu_total' => $this->jangka_waktu_total,
                'masa_tenggang' => $this->masa_tenggang,
                'tanggal_mulai_cicilan' => $this->tanggal_mulai_cicilan,
                'total_pokok' => $this->total_pokok,
                'total_margin' => $this->total_margin,
                'total_cicilan' => $this->total_cicilan,
                'updated_by' => Auth::id(),
            ]);

            $this->program->jadwalAngsuran()->delete();

            foreach ($this->jadwal_angsuran as $item) {
                $this->program->jadwalAngsuran()->create([
                    'no' => $item['no'],
                    'tanggal_jatuh_tempo' => \Carbon\Carbon::parse($item['tanggal_jatuh_tempo_raw']),
                    'pokok' => $item['pokok'],
                    'margin' => $item['margin'],
                    'total_cicilan' => $item['total_cicilan'],
                    'catatan' => $item['catatan'],
                    'is_grace_period' => $item['is_grace_period'] ?? false,
                    'status' => $item['status'] ?? 'Belum Jatuh Tempo',
                ]);
            }

            DB::commit();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Program restrukturisasi berhasil diperbarui!',
                'redirect_url' => route('program-restrukturisasi.index')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating: ' . $e->getMessage());
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'System Error',
                'text' => 'Gagal memperbarui: ' . $e->getMessage(),
            ]);
        }
    }
}