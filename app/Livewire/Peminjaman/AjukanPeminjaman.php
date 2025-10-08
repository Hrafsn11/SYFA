<?php

namespace App\Livewire\Peminjaman;

use Livewire\Component;

class AjukanPeminjaman extends Component
{
    public $sumber_pembiayaan = 'Eksternal';
    public $jenis_pembiayaan = 'Invoice Financing';
    public $nama_bank;
    public $banks = [];
    public $invoices = [];

    public function mount()
    {
        $this->banks = [
            'Bank A',
            'Bank B',
            'Bank C',
        ];

        $this->invoices = [
            [
                'no_invoice' => '2222',
                'nama_client' => 'Pelni',
                'nilai_invoice' => '10.000.000',
                'nilai_pinjaman' => '10.000.000',
                'nilai_bagi_hasil' => '10.000.000',
                'invoice_date' => '2025-08-15',
                'due_date' => '2025-08-24',
                'dokumen_invoice' => 'dokumen.pdf',
                'dokumen_kontrak' => 'dokumen.pdf',
                'dokumen_so' => 'dokumen.pdf',
                'dokumen_bast' => 'dokumen.pdf',
            ]
        ];
    }

    public function render()
    {
        return view('livewire.peminjaman.ajukan-peminjaman')
        ->layout('layouts.app');
    }

}
