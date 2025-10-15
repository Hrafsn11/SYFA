<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    /**
     * Display the specified resource detail view.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // For now, pass minimal data required by the view. The Livewire detail view
        // in this project expects the UI only; we'll pass a sample array so the
        // blade can render if it needs an id or basic data.
        $peminjaman = [
            'id' => $id,
            'nama_perusahaan' => 'Techno Infinity',
            'nilai_kol' => 'A',
        ];

        // Provide sumber_eksternal list (same shape as used in Livewire create component)
        $sumber_eksternal = [
            ['id' => 1, 'nama' => 'Pemberi Dana A'],
            ['id' => 2, 'nama' => 'Pemberi Dana B'],
            ['id' => 3, 'nama' => 'Pemberi Dana C'],
        ];

        // Banks list
        $banks = [
            'BCA','Mandiri','BNI','BRI','CIMB Niaga','Danamon','Permata Bank','OCBC NISP','UOB Indonesia','Panin Bank'
        ];

        // Tenor options
        $tenor_pembayaran = [
            ['value' => '3', 'label' => '3 Bulan'],
            ['value' => '6', 'label' => '6 Bulan'],
            ['value' => '9', 'label' => '9 Bulan'],
            ['value' => '12', 'label' => '12 Bulan'],
        ];

        // Minimal sample financing data for included invoice tables
        $invoice_financing_data = [
            [
                'no_invoice' => 'INV-2025-001',
                'nama_client' => 'PT Maju Jaya',
                'nilai_invoice' => '15000000',
                'nilai_pinjaman' => '12000000',
                'nilai_bagi_hasil' => '300000',
                'invoice_date' => '2025-01-15',
                'due_date' => '2025-02-15',
                'dokumen_invoice' => 'invoice_001.pdf',
                'dokumen_kontrak' => 'kontrak_001.pdf',
                'dokumen_so' => 'so_001.pdf',
                'dokumen_bast' => 'bast_001.pdf',
            ],
        ];

        // Sample PO Financing data
        $po_financing_data = [
            [
                'no_invoice' => 'PO-2025-010',
                'nama_client' => 'CV Sumber Niaga',
                'nilai_invoice' => '8000000',
                'nilai_pinjaman' => '7000000',
                'nilai_bagi_hasil' => '200000',
                'invoice_date' => '2025-03-10',
                'due_date' => '2025-04-10',
                'dokumen_kontrak' => 'kontrak_po_010.pdf',
                'dokumen_so' => 'so_po_010.pdf',
                'dokumen_bast' => 'bast_po_010.pdf',
                'dokumen_lainnya' => 'lainnya_po_010.pdf',
            ],
        ];

        // Sample Installment data
        $installment_data = [
            [
                'no_invoice' => 'INST-2025-05',
                'nama_client' => 'PT Retailindo',
                'nilai_invoice' => '2500000',
                'invoice_date' => '2025-05-01',
                'nama_barang' => 'Mesin Kasir',
                'dokumen_invoice' => 'invoice_inst_05.pdf',
                'dokumen_lainnya' => 'lainnya_inst_05.pdf',
            ],
        ];

        // Sample Factoring data
        $factoring_data = [
            [
                'no_invoice' => 'FAC-2025-02',
                'nama_client' => 'PT Ekspor Jaya',
                'nilai_invoice' => '50000000',
                'nilai_pinjaman' => '45000000',
                'nilai_bagi_hasil' => '1500000',
                'invoice_date' => '2025-02-20',
                'due_date' => '2025-03-20',
                'dokumen_invoice' => 'invoice_fac_02.pdf',
                'dokumen_kontrak' => 'kontrak_fac_02.pdf',
                'dokumen_so' => 'so_fac_02.pdf',
                'dokumen_bast' => 'bast_fac_02.pdf',
            ],
        ];

        return view('livewire.peminjaman.detail', compact(
            'peminjaman', 'sumber_eksternal', 'banks', 'tenor_pembayaran',
            'invoice_financing_data', 'po_financing_data', 'installment_data', 'factoring_data'
        ));
    }

    /**
     * Display the peminjaman index page (list).
     */
    public function index()
    {
        // copy sample data from Livewire\Peminjaman\PeminjamanIndex
        $peminjaman_data = [
            ['id' => 1, 'nama_perusahaan' => 'PT Maju Jaya', 'lampiran_sid' => 'sid_001.pdf', 'nilai_kol' => 'A'],
            ['id' => 2, 'nama_perusahaan' => 'CV Sukses Makmur', 'lampiran_sid' => 'sid_002.pdf', 'nilai_kol' => 'B'],
            ['id' => 3, 'nama_perusahaan' => 'PT Sejahtera Abadi', 'lampiran_sid' => 'sid_003.pdf', 'nilai_kol' => 'D'],
            ['id' => 4, 'nama_perusahaan' => 'PT Teknologi Maju', 'lampiran_sid' => 'sid_004.pdf', 'nilai_kol' => 'A'],
            ['id' => 5, 'nama_perusahaan' => 'CV Digital Solution', 'lampiran_sid' => 'sid_005.pdf', 'nilai_kol' => 'C'],
            ['id' => 6, 'nama_perusahaan' => 'PT Pelabuhan Indonesia', 'lampiran_sid' => 'sid_006.pdf', 'nilai_kol' => 'A'],
            ['id' => 7, 'nama_perusahaan' => 'PT Angkasa Pura', 'lampiran_sid' => 'sid_007.pdf', 'nilai_kol' => 'B'],
        ];

        return view('livewire.peminjaman.index', compact('peminjaman_data'));
    }

    /**
     * Display the create peminjaman page (form).
     */
    public function create()
    {
        // copy data from Livewire\Peminjaman\PeminjamanCreate
        $sumber_eksternal = [
            ['id' => 1, 'nama' => 'Pemberi Dana A'],
            ['id' => 2, 'nama' => 'Pemberi Dana B'],
            ['id' => 3, 'nama' => 'Pemberi Dana C'],
        ];

        $tenor_pembayaran = [
            ['value' => '3', 'label' => '3 Bulan'],
            ['value' => '6', 'label' => '6 Bulan'],
            ['value' => '9', 'label' => '9 Bulan'],
            ['value' => '12', 'label' => '12 Bulan'],
        ];

        $kebutuhan_pinjaman = [
            ['value' => 'Lainnya', 'label' => 'Lainnya'],
            ['value' => 'Modal Usaha', 'label' => 'Modal Usaha'],
            ['value' => 'Pengembangan Bisnis', 'label' => 'Pengembangan Bisnis'],
            ['value' => 'Operasional', 'label' => 'Operasional'],
            ['value' => 'Investasi', 'label' => 'Investasi'],
        ];

        // minimal sample invoice data arrays
        $invoice_financing_data = [
            [
                'no_invoice' => 'INV-2025-001',
                'nama_client' => 'PT Maju Jaya',
                'nilai_invoice' => '15000000',
                'nilai_pinjaman' => '12000000',
                'nilai_bagi_hasil' => '300000',
                'invoice_date' => '2025-01-15',
                'due_date' => '2025-02-15',
                'dokumen_invoice' => 'invoice_001.pdf',
                'dokumen_kontrak' => 'kontrak_001.pdf',
                'dokumen_so' => 'so_001.pdf',
                'dokumen_bast' => 'bast_001.pdf',
            ],
        ];

        $po_financing_data = [
            [
                'no_invoice' => 'PO-2025-010',
                'nama_client' => 'CV Sumber Niaga',
                'nilai_invoice' => '8000000',
                'nilai_pinjaman' => '7000000',
                'nilai_bagi_hasil' => '200000',
                'invoice_date' => '2025-03-10',
                'due_date' => '2025-04-10',
                'dokumen_kontrak' => 'kontrak_po_010.pdf',
                'dokumen_so' => 'so_po_010.pdf',
                'dokumen_bast' => 'bast_po_010.pdf',
                'dokumen_lainnya' => 'lainnya_po_010.pdf',
            ],
        ];

        $installment_data = [
            [
                'no_invoice' => 'INST-2025-05',
                'nama_client' => 'PT Retailindo',
                'nilai_invoice' => '2500000',
                'invoice_date' => '2025-05-01',
                'nama_barang' => 'Mesin Kasir',
                'dokumen_invoice' => 'invoice_inst_05.pdf',
                'dokumen_lainnya' => 'lainnya_inst_05.pdf',
            ],
        ];

        $factoring_data = [
            [
                'no_invoice' => 'FAC-2025-02',
                'nama_client' => 'PT Ekspor Jaya',
                'nilai_invoice' => '50000000',
                'nilai_pinjaman' => '45000000',
                'nilai_bagi_hasil' => '1500000',
                'invoice_date' => '2025-02-20',
                'due_date' => '2025-03-20',
                'dokumen_invoice' => 'invoice_fac_02.pdf',
                'dokumen_kontrak' => 'kontrak_fac_02.pdf',
                'dokumen_so' => 'so_fac_02.pdf',
                'dokumen_bast' => 'bast_fac_02.pdf',
            ],
        ];

        $banks = ['BCA','Mandiri','BNI','BRI','CIMB Niaga','Danamon','Permata Bank','OCBC NISP','UOB Indonesia','Panin Bank'];

        return view('livewire.peminjaman.create', compact(
            'sumber_eksternal','tenor_pembayaran','kebutuhan_pinjaman','invoice_financing_data','po_financing_data','installment_data','factoring_data','banks'
        ));
    }
}
