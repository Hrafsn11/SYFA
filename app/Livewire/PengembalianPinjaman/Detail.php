<?php

namespace App\Livewire\PengembalianPinjaman;

use App\Models\PengembalianPinjaman;
use Livewire\Component;

class Detail extends Component
{
    public PengembalianPinjaman $pengembalian;

    public function mount(string $id): void
    {
        $this->pengembalian = PengembalianPinjaman::with(['pengajuanPeminjaman', 'pengembalianInvoices'])
            ->findOrFail($id);
    }

    public function render()
    {
        $totalDibayarkan = $this->pengembalian->pengembalianInvoices->sum('nominal_yg_dibayarkan');

        return view('livewire.pengembalian-pinjaman.detail', [
            'totalDibayarkan' => $totalDibayarkan,
        ])->layout('layouts.app', [
            'title' => 'Detail Pengembalian Pinjaman',
        ]);
    }
}

