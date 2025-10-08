<?php

namespace App\Livewire\Peminjaman;

use Livewire\Component;
use Livewire\WithFileUploads;

class PeminjamanCreate extends Component
{
    use WithFileUploads;

    public $sumber_pembiayaan = 'Eksternal';
    public $sumber_eksternal_id;
    public $sumber_eksternal = [];
    public $jenis_pembiayaan = 'Invoice Financing';
    public $nama_bank;
    public $banks = [];
    public $invoices = [];
    public $tenor_pembayaran = [];
    public $kebutuhan_pinjaman = [];
    public $invoice_financing_data = [];
    public $po_financing_data = [];
    public $installment_data = [];
    public $factoring_data = [];

    public $modal_jenis_pembiayaan = 'Invoice Financing';

    public $new_no_invoice;
    public $new_nama_client;
    public $new_nilai_invoice;
    public $new_nilai_pinjaman;
    public $new_nilai_bagi_hasil;
    public $new_invoice_date;
    public $new_due_date;
    public $new_nama_barang;
    public $new_dokumen_invoice;
    public $new_dokumen_kontrak;
    public $new_dokumen_so;
    public $new_dokumen_bast;
    public $new_dokumen_lainnya;

    public function updatedJenisPembiayaan($value)
    {
        $this->modal_jenis_pembiayaan = $value;
    }

    public function mount()
    {
        $this->sumber_eksternal = [
            ['id' => 1, 'nama' => 'Pemberi Dana A'],
            ['id' => 2, 'nama' => 'Pemberi Dana B'],
            ['id' => 3, 'nama' => 'Pemberi Dana C'],
        ];

        $this->tenor_pembayaran = [
            ['value' => '3', 'label' => '3 Bulan'],
            ['value' => '6', 'label' => '6 Bulan'],
            ['value' => '9', 'label' => '9 Bulan'],
            ['value' => '12', 'label' => '12 Bulan'],
        ];

        $this->kebutuhan_pinjaman = [
            ['value' => 'Lainnya', 'label' => 'Lainnya'],
            ['value' => 'Modal Usaha', 'label' => 'Modal Usaha'],
            ['value' => 'Pengembangan Bisnis', 'label' => 'Pengembangan Bisnis'],
            ['value' => 'Operasional', 'label' => 'Operasional'],
            ['value' => 'Investasi', 'label' => 'Investasi'],
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

        // Data untuk Invoice Financing
        $this->invoice_financing_data = [
            [
                'no_invoice' => 'INV-2025-001',
                'nama_client' => 'PT Maju Jaya',
                'nilai_invoice' => '15.000.000',
                'nilai_pinjaman' => '12.000.000',
                'nilai_bagi_hasil' => '300.000',
                'invoice_date' => '2025-01-15',
                'due_date' => '2025-02-15',
                'dokumen_invoice' => 'invoice_001.pdf',
                'dokumen_kontrak' => 'kontrak_001.pdf',
                'dokumen_so' => 'so_001.pdf',
                'dokumen_bast' => 'bast_001.pdf',
            ],
            [
                'no_invoice' => 'INV-2025-002',
                'nama_client' => 'CV Sukses Makmur',
                'nilai_invoice' => '25.000.000',
                'nilai_pinjaman' => '20.000.000',
                'nilai_bagi_hasil' => '500.000',
                'invoice_date' => '2025-01-20',
                'due_date' => '2025-03-20',
                'dokumen_invoice' => 'invoice_002.pdf',
                'dokumen_kontrak' => 'kontrak_002.pdf',
                'dokumen_so' => 'so_002.pdf',
                'dokumen_bast' => 'bast_002.pdf',
            ],
            [
                'no_invoice' => 'INV-2025-003',
                'nama_client' => 'PT Sejahtera Abadi',
                'nilai_invoice' => '18.500.000',
                'nilai_pinjaman' => '15.000.000',
                'nilai_bagi_hasil' => '375.000',
                'invoice_date' => '2025-02-01',
                'due_date' => '2025-03-01',
                'dokumen_invoice' => 'invoice_003.pdf',
                'dokumen_kontrak' => 'kontrak_003.pdf',
                'dokumen_so' => 'so_003.pdf',
                'dokumen_bast' => 'bast_003.pdf',
            ]
        ];

        // Data untuk PO Financing
        $this->po_financing_data = [
            [
                'no_invoice' => 'PO-2025-001',
                'nama_client' => 'PT Pelabuhan Indonesia',
                'nilai_invoice' => '50.000.000',
                'nilai_pinjaman' => '40.000.000',
                'nilai_bagi_hasil' => '1.000.000',
                'invoice_date' => '2025-01-10',
                'due_date' => '2025-04-10',
                'dokumen_kontrak' => 'po_kontrak_001.pdf',
                'dokumen_so' => 'po_so_001.pdf',
                'dokumen_bast' => 'po_bast_001.pdf',
                'dokumen_lainnya' => 'po_lain_001.pdf',
            ],
            [
                'no_invoice' => 'PO-2025-002',
                'nama_client' => 'PT Angkasa Pura',
                'nilai_invoice' => '75.000.000',
                'nilai_pinjaman' => '60.000.000',
                'nilai_bagi_hasil' => '1.500.000',
                'invoice_date' => '2025-01-25',
                'due_date' => '2025-05-25',
                'dokumen_kontrak' => 'po_kontrak_002.pdf',
                'dokumen_so' => 'po_so_002.pdf',
                'dokumen_bast' => 'po_bast_002.pdf',
                'dokumen_lainnya' => 'po_lain_002.pdf',
            ]
        ];

        // Data untuk Installment
        $this->installment_data = [
            [
                'no_invoice' => 'INST-2025-001',
                'nama_client' => 'PT Teknologi Maju',
                'nilai_invoice' => '30.000.000',
                'invoice_date' => '2025-02-01',
                'nama_barang' => 'Laptop Dell XPS 15',
                'dokumen_invoice' => 'inst_invoice_001.pdf',
                'dokumen_lainnya' => 'inst_lain_001.pdf',
            ],
            [
                'no_invoice' => 'INST-2025-002',
                'nama_client' => 'CV Digital Solution',
                'nilai_invoice' => '45.000.000',
                'invoice_date' => '2025-02-10',
                'nama_barang' => 'Server HP ProLiant',
                'dokumen_invoice' => 'inst_invoice_002.pdf',
                'dokumen_lainnya' => 'inst_lain_002.pdf',
            ],
            [
                'no_invoice' => 'INST-2025-003',
                'nama_client' => 'PT Software House',
                'nilai_invoice' => '22.000.000',
                'invoice_date' => '2025-02-15',
                'nama_barang' => 'Macbook Pro M3',
                'dokumen_invoice' => 'inst_invoice_003.pdf',
                'dokumen_lainnya' => 'inst_lain_003.pdf',
            ]
        ];

        // Data untuk Factoring
        $this->factoring_data = [
            [
                'no_invoice' => 'FACT-2025-001',
                'nama_client' => 'PT Garuda Indonesia',
                'nilai_invoice' => '100.000.000',
                'nilai_pinjaman' => '85.000.000',
                'nilai_bagi_hasil' => '2.550.000',
                'invoice_date' => '2025-01-05',
                'due_date' => '2025-04-05',
                'dokumen_invoice' => 'fact_invoice_001.pdf',
                'dokumen_kontrak' => 'fact_kontrak_001.pdf',
                'dokumen_so' => 'fact_so_001.pdf',
                'dokumen_bast' => 'fact_bast_001.pdf',
            ],
            [
                'no_invoice' => 'FACT-2025-002',
                'nama_client' => 'PT Kereta Api Indonesia',
                'nilai_invoice' => '120.000.000',
                'nilai_pinjaman' => '100.000.000',
                'nilai_bagi_hasil' => '3.000.000',
                'invoice_date' => '2025-01-15',
                'due_date' => '2025-05-15',
                'dokumen_invoice' => 'fact_invoice_002.pdf',
                'dokumen_kontrak' => 'fact_kontrak_002.pdf',
                'dokumen_so' => 'fact_so_002.pdf',
                'dokumen_bast' => 'fact_bast_002.pdf',
            ]
        ];

        $this->banks = [
            'BCA',
            'Mandiri',
            'BNI',
            'BRI',
            'CIMB Niaga',
            'Danamon',
            'Permata Bank',
            'OCBC NISP',
            'UOB Indonesia',
            'Panin Bank'
        ];
    }

    public function tambahInvoice()
    {
        $newData = [
            'no_invoice' => $this->new_no_invoice ?? 'INV-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'nama_client' => $this->new_nama_client ?? 'Client Baru',
            'nilai_invoice' => $this->new_nilai_invoice ?? '10.000.000',
        ];

        switch ($this->jenis_pembiayaan) {
            case 'Invoice Financing':
                $newData['nilai_pinjaman'] = $this->new_nilai_pinjaman ?? '8.000.000';
                $newData['nilai_bagi_hasil'] = $this->new_nilai_bagi_hasil ?? '200.000';
                $newData['invoice_date'] = $this->new_invoice_date ?? date('Y-m-d');
                $newData['due_date'] = $this->new_due_date ?? date('Y-m-d', strtotime('+30 days'));
                $newData['dokumen_invoice'] = 'invoice_new.pdf';
                $newData['dokumen_kontrak'] = 'kontrak_new.pdf';
                $newData['dokumen_so'] = 'so_new.pdf';
                $newData['dokumen_bast'] = 'bast_new.pdf';
                
                $this->invoice_financing_data[] = $newData;
                break;

            case 'PO Financing':
                $newData['nilai_pinjaman'] = $this->new_nilai_pinjaman ?? '8.000.000';
                $newData['nilai_bagi_hasil'] = $this->new_nilai_bagi_hasil ?? '200.000';
                $newData['invoice_date'] = $this->new_invoice_date ?? date('Y-m-d');
                $newData['due_date'] = $this->new_due_date ?? date('Y-m-d', strtotime('+60 days'));
                $newData['dokumen_kontrak'] = 'po_kontrak_new.pdf';
                $newData['dokumen_so'] = 'po_so_new.pdf';
                $newData['dokumen_bast'] = 'po_bast_new.pdf';
                $newData['dokumen_lainnya'] = 'po_lain_new.pdf';
                
                $this->po_financing_data[] = $newData;
                break;

            case 'Installment':
                $newData['invoice_date'] = $this->new_invoice_date ?? date('Y-m-d');
                $newData['nama_barang'] = $this->new_nama_barang ?? 'Barang Baru';
                $newData['dokumen_invoice'] = 'inst_invoice_new.pdf';
                $newData['dokumen_lainnya'] = 'inst_lain_new.pdf';
                
                $this->installment_data[] = $newData;
                break;

            case 'Factoring':
                $newData['nilai_pinjaman'] = $this->new_nilai_pinjaman ?? '8.000.000';
                $newData['nilai_bagi_hasil'] = $this->new_nilai_bagi_hasil ?? '200.000';
                $newData['invoice_date'] = $this->new_invoice_date ?? date('Y-m-d');
                $newData['due_date'] = $this->new_due_date ?? date('Y-m-d', strtotime('+90 days'));
                $newData['dokumen_invoice'] = 'fact_invoice_new.pdf';
                $newData['dokumen_kontrak'] = 'fact_kontrak_new.pdf';
                $newData['dokumen_so'] = 'fact_so_new.pdf';
                $newData['dokumen_bast'] = 'fact_bast_new.pdf';
                
                $this->factoring_data[] = $newData;
                break;
        }

        $this->resetNewInvoiceForm();
    }

    public function openModal()
    {
        $this->modal_jenis_pembiayaan = $this->jenis_pembiayaan;
        
        $this->dispatch('open-modal', jenisPembiayaan: $this->modal_jenis_pembiayaan);
    }

    private function resetNewInvoiceForm()
    {
        $this->new_no_invoice = null;
        $this->new_nama_client = null;
        $this->new_nilai_invoice = null;
        $this->new_nilai_pinjaman = null;
        $this->new_nilai_bagi_hasil = null;
        $this->new_invoice_date = null;
        $this->new_due_date = null;
        $this->new_nama_barang = null;
        $this->new_dokumen_invoice = null;
        $this->new_dokumen_kontrak = null;
        $this->new_dokumen_so = null;
        $this->new_dokumen_bast = null;
        $this->new_dokumen_lainnya = null;
    }

    public function render()
    {
        return view('livewire.peminjaman.create')
        ->layout('layouts.app');
    }

}
