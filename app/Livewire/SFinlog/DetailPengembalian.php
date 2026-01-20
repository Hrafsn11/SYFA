<?php

namespace App\Livewire\SFinlog;

use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use Livewire\Component;

class DetailPengembalian extends Component
{
    public PeminjamanFinlog $peminjaman;
    public $pengembalianList;

    public function mount(string $id): void
    {
        $this->peminjaman = PeminjamanFinlog::with(['debitur', 'pengembalianPinjaman' => function ($query) {
            $query->orderBy('tanggal_pengembalian', 'desc');
        }])->findOrFail($id);

        $this->pengembalianList = $this->peminjaman->pengembalianPinjaman;
    }

    private function calculatePaymentBreakdown(): array
    {
        $totalPinjaman = (float) $this->peminjaman->nilai_pinjaman;
        $totalBagiHasil = (float) $this->peminjaman->nilai_bagi_hasil;
        $totalTagihan = (float) $this->peminjaman->total_pinjaman;

        $totalDibayarkan = 0.0;
        $sisaPinjaman = $totalPinjaman;
        $sisaBagiHasil = $totalBagiHasil;

        $lastPengembalian = $this->pengembalianList->first();
        if ($lastPengembalian) {
            $sisaPinjaman = (float) $lastPengembalian->sisa_pinjaman;
            $sisaBagiHasil = (float) $lastPengembalian->sisa_bagi_hasil;
        }

        $totalDibayarkan = (float) $this->pengembalianList->sum('jumlah_pengembalian');

        $paidToPokok = $totalPinjaman - $sisaPinjaman;
        $paidToBagiHasil = $totalBagiHasil - $sisaBagiHasil;

        return [
            'total_pinjaman' => $totalPinjaman,
            'total_bagi_hasil' => $totalBagiHasil,
            'total_tagihan' => $totalTagihan,
            'total_dibayarkan' => $totalDibayarkan,
            'paid_to_pokok' => $paidToPokok,
            'paid_to_bagi_hasil' => $paidToBagiHasil,
            'sisa_pinjaman' => $sisaPinjaman,
            'sisa_bagi_hasil' => $sisaBagiHasil,
            'sisa_total' => $sisaPinjaman + $sisaBagiHasil,
        ];
    }

    public function render()
    {
        $breakdown = $this->calculatePaymentBreakdown();
        $lastPengembalian = $this->pengembalianList->first();

        $status = 'Belum Lunas';
        if ($lastPengembalian && $lastPengembalian->status === 'Lunas') {
            $status = 'Lunas';
        }

        return view('livewire.sfinlog.pengembalian-pinjaman.detail', [
            'breakdown' => $breakdown,
            'status' => $status,
        ])->layout('layouts.app', [
            'title' => 'Detail Pengembalian Pinjaman - SFinlog',
        ]);
    }
}
