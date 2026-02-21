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
        if (property_exists($this, 'total_bunga')) {
            $this->total_bunga = $totalBagiHasil;
        }
        if (property_exists($this, 'total_pinjaman') && property_exists($this, 'total_bunga')) {
            $this->pembayaran_total = $this->total_pinjaman + $this->total_bunga;
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

    /**
     * Handler ketika jenis_pembiayaan berubah.
     * Semua jenis pembiayaan menggunakan sumber_pembiayaan = Internal.
     */
    public function updatedJenisPembiayaan($value)
    {
        // Pastikan sumber_pembiayaan selalu Internal
        $this->sumber_pembiayaan = 'Internal';
        $this->id_instansi = null;
        
        // Reset form data invoice ketika jenis pembiayaan berubah
        $this->form_data_invoice = [];
    }

}
