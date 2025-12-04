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
        return view('livewire.program-restrukturisasi-show', [
            'jadwal' => $this->program->jadwalAngsuran,
        ])->layout('layouts.app');
    }
}
