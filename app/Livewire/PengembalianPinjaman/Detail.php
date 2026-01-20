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

    /**
     * Calculate how total payments are allocated between profit sharing and principal
     * 
     * Logic: Payments go to Bagi Hasil first, then to Pokok after Bagi Hasil is fully paid
     * 
     * @return array{total: float, to_bagi_hasil: float, to_pokok: float}
     */
    private function calculatePaymentBreakdown(): array
    {
        $totalDibayarkan = (float) $this->pengembalian->pengembalianInvoices->sum('nominal_yg_dibayarkan');
        $totalBagiHasil = (float) $this->pengembalian->total_bagi_hasil;
        $totalPokok = (float) $this->pengembalian->total_pinjaman;

        // Payment allocation: Bagi Hasil first, then Pokok
        $paidToBagiHasil = min($totalDibayarkan, $totalBagiHasil);
        $remainingAfterBagiHasil = max(0, $totalDibayarkan - $totalBagiHasil);
        $paidToPokok = min($remainingAfterBagiHasil, $totalPokok);

        return [
            'total' => $totalDibayarkan,
            'to_bagi_hasil' => $paidToBagiHasil,
            'to_pokok' => $paidToPokok,
        ];
    }

    public function render()
    {
        $breakdown = $this->calculatePaymentBreakdown();

        return view('livewire.pengembalian-pinjaman.detail', [
            'totalDibayarkan' => $breakdown['total'],
            'dibayarkanKeBagiHasil' => $breakdown['to_bagi_hasil'],
            'dibayarkanKePokok' => $breakdown['to_pokok'],
        ])->layout('layouts.app', [
            'title' => 'Detail Pengembalian Pinjaman',
        ]);
    }
}
