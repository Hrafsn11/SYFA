<?php

namespace App\Livewire\PenyesuaianCicilan;

use App\Models\PenyesuaianCicilan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public PenyesuaianCicilan $program;

    public function mount(string $id): void
    {
        $this->program = PenyesuaianCicilan::with([
            'PengajuanCicilan.debitur',
            'jadwalAngsuran' => fn($query) => $query->orderBy('no'),
        ])->findOrFail($id);

        // Authorization check: Debitur hanya bisa lihat data miliknya
        $user = Auth::user();
        $isAdmin = $user && $user->hasRole(['super-admin', 'admin', 'sfinance', 'Finance SKI', 'CEO SKI', 'Direktur SKI']);

        if (!$isAdmin) {
            $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();
            $pengajuanDebiturId = $this->program->PengajuanCicilan->id_debitur ?? null;

            if (!$debitur || $debitur->id_debitur !== $pengajuanDebiturId) {
                abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
            }
        }
    }

    public function render()
    {
        // Hitung sisa pinjaman untuk metode Efektif (Anuitas)
        $sisaPokok = $this->program->plafon_pembiayaan;
        $jadwalWithSisa = $this->program->jadwalAngsuran->map(function ($item) use (&$sisaPokok) {
            $data = $item->toArray();

            // Tambah sisa_pinjaman untuk metode Efektif (Anuitas)
            if ($this->program->metode_perhitungan === 'Anuitas') {
                $data['sisa_pinjaman'] = $sisaPokok;
                if (!$item->is_grace_period) {
                    $sisaPokok -= (float) $item->pokok;
                    if ($sisaPokok < 0)
                        $sisaPokok = 0;
                }
            }

            return $data;
        });

        return view('livewire.penyesuaian-cicilan.show', [
            'jadwal' => $this->program->jadwalAngsuran,
            'jadwalWithSisa' => $jadwalWithSisa,
        ])->layout('layouts.app');
    }
}
