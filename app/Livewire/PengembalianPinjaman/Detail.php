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
     * Logic: Payments go to Bunga first, then to Pokok after Bunga is fully paid
     * 
     * @return array{total: float, to_bunga: float, to_pokok: float}
     */
    private function calculatePaymentBreakdown(): array
    {
        $totalDibayarkan = (float) $this->pengembalian->pengembalianInvoices->sum('nominal_yg_dibayarkan');
        $totalBunga = (float) $this->pengembalian->total_bunga;
        $totalPokok = (float) $this->pengembalian->total_pinjaman;

        // Payment allocation: Bunga first, then Pokok
        $paidToBunga = min($totalDibayarkan, $totalBunga);
        $remainingAfterBunga = max(0, $totalDibayarkan - $totalBunga);
        $paidToPokok = min($remainingAfterBunga, $totalPokok);

        return [
            'total' => $totalDibayarkan,
            'to_bunga' => $paidToBunga,
            'to_pokok' => $paidToPokok,
        ];
    }

    public function render()
    {
        $breakdown = $this->calculatePaymentBreakdown();

        return view('livewire.pengembalian-pinjaman.detail', [
            'totalDibayarkan' => $breakdown['total'],
            'dibayarkanKeBunga' => $breakdown['to_bunga'],
            'dibayarkanKePokok' => $breakdown['to_pokok'],
        ])->layout('layouts.app', [
            'title' => 'Detail Pengembalian Pinjaman',
        ]);
    }
}
