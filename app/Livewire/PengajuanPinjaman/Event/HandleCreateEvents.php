<?php

namespace App\Livewire\PengajuanPinjaman\Event;

use Livewire\Attributes\On;
use App\Livewire\Traits\HandleComponentEvent;

trait HandleCreateEvents
{
    use HandleComponentEvent;

    #[On('invoiceTotalsUpdated')]
    public function handleInvoiceTotalsUpdated($totalPinjaman, $totalBagiHasil, $formDataInvoice = [])
    {
        if (property_exists($this, 'total_pinjaman')) {
            $this->total_pinjaman = $totalPinjaman;
        }
        if (property_exists($this, 'total_bagi_hasil')) {
            $this->total_bagi_hasil = $totalBagiHasil;
        }
        if (property_exists($this, 'total_pinjaman') && property_exists($this, 'total_bagi_hasil')) {
            $this->pembayaran_total = $this->total_pinjaman + $this->total_bagi_hasil;
        }
        if (property_exists($this, 'form_data_invoice')) {
            $this->form_data_invoice = $formDataInvoice;
        }
    }

    public function updatedLampiranSID($lampiranSID)
    {
        if (property_exists($this, 'lampiran_sid_current')) {
            $this->lampiran_sid_current = $lampiranSID;
        }
    }

}

