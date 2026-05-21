<?php

namespace App\Livewire\PengajuanPinjaman\Event;

use Livewire\Attributes\On;
use App\Livewire\Traits\HandleComponentEvent;

trait HandleInvoiceEvents
{
    use HandleComponentEvent;

    #[On('edit-invoice')]
    public function handleEditInvoice($data)
    {
        $this->modal_title = 'Edit Invoice';

        // Set index yang sedang diedit agar validasi unique mengecualikan item ini
        if (isset($data['_edit_index'])) {
            $this->index_data_invoice = $data['_edit_index'];
            unset($data['_edit_index']);
        }

        foreach ($data as $key => $value) {
            // Dokumen: simpan path lama ke *_current, jangan set ke property file
            if (in_array($key, [
                'dokumen_invoice', 
                'dokumen_kontrak', 
                'dokumen_so', 
                'dokumen_bast', 
                'dokumen_lainnya'
            ])) {
                $this->{$key . '_current'} = $value;
                continue;
            }

            // Nilai rupiah: konversi raw number ke format rupiah untuk currency-field
            if (in_array($key, ['nilai_invoice', 'nilai_pinjaman', 'nilai_bunga'])) {
                // Jika sudah berupa string format rupiah, gunakan langsung
                // Jika berupa angka mentah dari DB, format ke rupiah
                if (is_numeric($value)) {
                    $this->{$key} = (string) (int) $value;
                } else {
                    $this->{$key} = $value;
                }
                continue;
            }

            // Tanggal: konversi ke format d/m/Y untuk datepicker
            if (in_array($key, ['invoice_date', 'due_date'])) {
                $parsed = parseCarbonDate($value);
                $this->{$key} = $parsed ? $parsed->format('d/m/Y') : $value;
                continue;
            }

            // Field lainnya: set langsung
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        // Hitung ulang nilai bunga
        $this->calculateNilaiBagiHasil();

        // Dispatch event agar modal terbuka
        $this->dispatch('edit-invoice-ready');
    }
}
