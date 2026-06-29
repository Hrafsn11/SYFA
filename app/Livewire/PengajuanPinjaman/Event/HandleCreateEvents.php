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

        $this->recalculateInstallment();
    }

    public function updatedLampiranSID($lampiranSID)
    {
        if (property_exists($this, 'lampiran_sid_current')) {
            $this->lampiran_sid_current = $lampiranSID;
        }
    }

    public function updatedHarapanTanggalPencairan($value)
    {
        if (!empty($value)) {
            try {
                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $value);
                // Tambah 1 bulan tanpa overflow (misal 31 Jan -> 28 Feb)
                $newDate = $date->addMonthNoOverflow()->format('d/m/Y');
                
                $this->rencana_tgl_pembayaran = $newDate;
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
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
        $this->recalculateInstallment();
    }

    public function updatedTenorPembayaran($value)
    {
        $this->recalculateInstallment();
    }

    /**
     * Recalculate all installment fields based on current invoice data
     */
    private function recalculateInstallment(): void
    {
        if ($this->jenis_pembiayaan !== \App\Enums\JenisPembiayaanEnum::INSTALLMENT) {
            return;
        }

        // 1. Total Pinjaman = SUM of all nilai_invoice
        $rawTotalPinjaman = collect($this->form_data_invoice ?? [])
            ->sum(fn($row) => (int) str_replace(['.', ',', 'Rp', ' '], '', $row['nilai_invoice'] ?? 0));
        
        $this->nominal_pinjaman = rupiahFormatter($rawTotalPinjaman);

        // 2. Persentase Bunga (Debit Cost) = 10% of Total Pinjaman
        $rawPersentaseBunga = $rawTotalPinjaman * 0.10;
        $this->pps_debit = rupiahFormatter($rawPersentaseBunga);

        // 3. PPS = 60% of Persentase Bunga
        $this->pps_percentage = rupiahFormatter($rawPersentaseBunga * 0.60);

        // 4. S Finance = 40% of Persentase Bunga
        $this->s_finance = rupiahFormatter($rawPersentaseBunga * 0.40);

        // 5. Total Pembayaran = Total Pinjaman + Persentase Bunga
        $rawTotalPembayaran = $rawTotalPinjaman + $rawPersentaseBunga;
        $this->total_pembayaran_installment = rupiahFormatter($rawTotalPembayaran);

        // 6. Yang harus dibayarkan per bulan = Total Pembayaran / Tenor
        $tenor = (int) ($this->tenor_pembayaran ?? 0);
        $rawBayarPerBulan = $tenor > 0 ? round($rawTotalPembayaran / $tenor) : 0;
        $this->bayar_per_bulan = rupiahFormatter($rawBayarPerBulan);
    }

}
