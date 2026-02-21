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

        foreach ($data as $key => $value) {
            if (in_array($key, [
                'dokumen_invoice', 
                'dokumen_kontrak', 
                'dokumen_so', 
                'dokumen_bast', 
                'dokumen_lainnnya'
            ])) {
                $this->{$key . '_current'} = $value;
                continue;
            }

            if (in_array($key, ['nilai_invoice', 'nilai_pinjaman', 'nilai_bagi_hasil'])) {
                $this->{$key} = rupiahFormatter($value);
            } else if (in_array($key, ['invoice_date', 'due_date'])) {
                $this->{$key} = parseCarbonDate($value)->format('d/m/Y');
            } else {
                $this->{$key} = $value;
            }

            // $this->{$key} = $value;
        }
    }
}

