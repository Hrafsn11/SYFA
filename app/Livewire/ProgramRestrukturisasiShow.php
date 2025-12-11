<?php

namespace App\Livewire;

use App\Models\ProgramRestrukturisasi;
use Livewire\Component;

class ProgramRestrukturisasiShow extends Component
{
    public ProgramRestrukturisasi $program;

    public function mount(string $id): void
    {
        $this->program = ProgramRestrukturisasi::with([
            'pengajuanRestrukturisasi.debitur',
            'jadwalAngsuran' => fn ($query) => $query->orderBy('no'),
        ])->findOrFail($id);
    }

    public function render()
    {
        // Hitung sisa pinjaman untuk metode Efektif (Anuitas)
        $sisaPokok = $this->program->plafon_pembiayaan;
        $jadwalWithSisa = $this->program->jadwalAngsuran->map(function ($item) use (&$sisaPokok) {
            $data = $item->toArray();
            
            // Tambah sisa_pinjaman untuk metode Efektif (Anuitas)
            if ($this->program->metode_perhitungan === 'Efektif (Anuitas)') {
                $data['sisa_pinjaman'] = $sisaPokok;
                if (!$item->is_grace_period) {
                    $sisaPokok -= (float) $item->pokok;
                    if ($sisaPokok < 0) $sisaPokok = 0;
                }
            }
            
            return $data;
        });

        return view('livewire.program-restrukturisasi-show', [
            'jadwal' => $this->program->jadwalAngsuran,
            'jadwalWithSisa' => $jadwalWithSisa,
        ])->layout('layouts.app');
    }
}
