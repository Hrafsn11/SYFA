<?php

namespace App\Livewire\PengajuanPinjaman\FieldsInput;

use App\Attributes\FieldInput;
use App\Models\MasterSumberPendanaanEksternal;

trait FieldInputInvoice
{
    #[FieldInput]
    public $no_invoice, $nama_client,
        $nilai_invoice, $nilai_pinjaman,
        $invoice_date, $due_date, $dokumen_invoice,
        $dokumen_kontrak, $dokumen_so, $dokumen_bast,
        $no_kontrak, $kontrak_date, $dokumen_lainnya, $nama_barang;

    /**
     * Menyiapkan data form berdasarkan jenis pembiayaan
     */
    private function prepareFormDataByJenisPembiayaan(): array
    {
        return match ($this->jenis_pembiayaan) {
            'Invoice Financing' => $this->prepareInvoiceFinancingData(),
            'PO Financing' => $this->preparePOFinancingData(),
            'Installment' => $this->prepareInstallmentData(),
            'Factoring' => $this->prepareFactoringData(),
            default => $this->prepareDefaultData(),
        };
    }

    /**
     * Menyiapkan data untuk Invoice Financing
     */
    private function prepareInvoiceFinancingData(): array
    {
        $nilaiPinjaman = rupiahToRawValue($this->nilai_pinjaman ?? 0);
        $nilaiBagiHasil = $this->calculateNilaiBagiHasilFromPinjaman($nilaiPinjaman);

        return [
            'no_invoice' => $this->no_invoice ?? '',
            'nama_client' => $this->nama_client ?? '',
            'nilai_invoice' => rupiahToRawValue($this->nilai_invoice ?? 0),
            'nilai_pinjaman' => $nilaiPinjaman,
            'nilai_bagi_hasil' => $nilaiBagiHasil,
            'invoice_date' => parseCarbonDate($this->invoice_date)?->format('d/m/Y') ?? '',
            'due_date' => parseCarbonDate($this->due_date)?->format('d/m/Y') ?? '',
            'dokumen_invoice' => $this->dokumen_invoice,
            'dokumen_kontrak' => $this->dokumen_kontrak,
            'dokumen_so' => $this->dokumen_so,
            'dokumen_bast' => $this->dokumen_bast,
        ];
    }

    /**
     * Menyiapkan data untuk PO Financing
     */
    private function preparePOFinancingData(): array
    {
        $nilaiPinjaman = rupiahToRawValue($this->nilai_pinjaman ?? 0);
        $nilaiBagiHasil = $this->calculateNilaiBagiHasilFromPinjaman($nilaiPinjaman);

        return [
            'no_kontrak' => $this->no_kontrak ?? '',
            'nama_client' => $this->nama_client ?? '',
            'nilai_invoice' => rupiahToRawValue($this->nilai_invoice ?? 0),
            'nilai_pinjaman' => $nilaiPinjaman,
            'nilai_bagi_hasil' => $nilaiBagiHasil,
            'kontrak_date' => parseCarbonDate($this->kontrak_date)?->format('d/m/Y') ?? '',
            'due_date' => parseCarbonDate($this->due_date) ?? '',
            'dokumen_kontrak' => $this->dokumen_kontrak,
            'dokumen_so' => $this->dokumen_so,
            'dokumen_bast' => $this->dokumen_bast,
            'dokumen_lainnya' => $this->dokumen_lainnya,
        ];
    }

    /**
     * Menyiapkan data untuk Installment
     */
    private function prepareInstallmentData(): array
    {
        return [
            'no_invoice' => $this->no_invoice ?? '',
            'nama_client' => $this->nama_client ?? '',
            'nilai_invoice' => rupiahToRawValue($this->nilai_invoice ?? 0),
            'invoice_date' => parseCarbonDate($this->invoice_date) ?? '',
            'nama_barang' => $this->nama_barang ?? '',
            'dokumen_invoice' => $this->dokumen_invoice,
            'dokumen_lainnya' => $this->dokumen_lainnya,
        ];
    }

    /**
     * Menyiapkan data untuk Factoring
     */
    private function prepareFactoringData(): array
    {
        $nilaiPinjaman = rupiahToRawValue($this->nilai_pinjaman ?? 0);
        $nilaiBagiHasil = $this->calculateNilaiBagiHasilFromPinjaman($nilaiPinjaman);

        return [
            'no_kontrak' => $this->no_kontrak ?? '',
            'nama_client' => $this->nama_client ?? '',
            'nilai_invoice' => rupiahToRawValue($this->nilai_invoice ?? 0),
            'nilai_pinjaman' => $nilaiPinjaman,
            'nilai_bagi_hasil' => $nilaiBagiHasil,
            'kontrak_date' => parseCarbonDate($this->kontrak_date)?->format('d/m/Y') ?? '',
            'due_date' => parseCarbonDate($this->due_date) ?? '',
            'dokumen_invoice' => $this->dokumen_invoice,
            'dokumen_kontrak' => $this->dokumen_kontrak,
            'dokumen_so' => $this->dokumen_so,
            'dokumen_bast' => $this->dokumen_bast,
        ];
    }

    /**
     * Menyiapkan data default (fallback)
     */
    private function prepareDefaultData(): array
    {
        $nilaiPinjaman = rupiahToRawValue($this->nilai_pinjaman ?? 0);
        $nilaiBagiHasil = $this->calculateNilaiBagiHasilFromPinjaman($nilaiPinjaman);

        return [
            'no_invoice' => $this->no_invoice ?? '',
            'nama_client' => $this->nama_client ?? '',
            'nilai_invoice' => rupiahToRawValue($this->nilai_invoice ?? 0),
            'nilai_pinjaman' => $nilaiPinjaman,
            'nilai_bagi_hasil' => $nilaiBagiHasil,
            'invoice_date' => parseCarbonDate($this->invoice_date)?->format('d/m/Y') ?? '',
            'due_date' => parseCarbonDate($this->due_date)?->format('d/m/Y') ?? '',
            'dokumen_invoice' => $this->dokumen_invoice,
            'dokumen_kontrak' => $this->dokumen_kontrak,
            'dokumen_so' => $this->dokumen_so,
            'dokumen_bast' => $this->dokumen_bast,
            'no_kontrak' => $this->no_kontrak ?? '',
            'kontrak_date' => parseCarbonDate($this->kontrak_date)?->format('d/m/Y') ?? '',
            'dokumen_lainnya' => $this->dokumen_lainnya,
            'nama_barang' => $this->nama_barang ?? '',
        ];
    }

    /**
     * Menghitung nilai bagi hasil dari nilai pinjaman
     */
    private function calculateNilaiBagiHasilFromPinjaman(float $nilaiPinjaman): float
    {
        return $nilaiPinjaman * (double) ($this->persentase_bagi_hasil ?? 0);
    }

    public function updatedNilaiPinjaman($value)
    {
        $this->preparePersentaseBagiHasil();
        $this->calculateNilaiBagiHasil();
    }

    protected function setAdditionalValidationData(): array
    {
        return [
            'jenis_pembiayaan' => $this->jenis_pembiayaan,
            'form_data_invoice' => $this->form_data_invoice ?? [],
        ];
    }

    /**
     * Menghitung nilai_bagi_hasil berdasarkan nilai_pinjaman * persentase_bagi_hasil
     */
    private function calculateNilaiBagiHasil()
    {
        // Parse nilai_pinjaman dari format "Rp 200,000" menjadi angka normal
        $nilaiPinjaman = rupiahToRawValue($this->nilai_pinjaman ?? 0);
        $persentase = (double) ($this->persentase_bagi_hasil ?? 0);
        
        $nilaiBagiHasil = $nilaiPinjaman * $persentase;
        $this->nilai_bagi_hasil = rupiahFormatter($nilaiBagiHasil);
    }

    private function edit()
    {
        $jenisPembiayaan = $this->jenis_pembiayaan;
        $this->{str_replace(' ', '_', strtolower($jenisPembiayaan)) . '_data'} = $this->pengajuan->buktiPeminjaman;
    }

    private function prepareFormData()
    {
        $this->invoice_financing_data = [];
        $this->po_financing_data = [];
        $this->installment_data = [];
        $this->factoring_data = [];

        $modalTitle = [
            'Invoice Financing' => 'Tambah Invoice Financing',
            'PO Financing' => 'Tambah PO Financing',
            'Installment' => 'Tambah Invoice Penjamin',
            'Factoring' => 'Tambah Kontrak Penjamin',
        ];

        $this->modal_title = $modalTitle[$this->jenis_pembiayaan];
    }

    private function prepareFormInvoice()
    {
        $this->preparePersentaseBagiHasil();
    }

    private function preparePersentaseBagiHasil()
    {
        if ($this->sumber_pembiayaan === 'Internal') {
            $this->persentase_bagi_hasil = (double) 2/100;
            $this->id_instansi = null;
        } else {
            $sumberPendanaanEksternal = MasterSumberPendanaanEksternal::where('id_instansi', $this->id_instansi)->first();
            if ($sumberPendanaanEksternal) {
                $this->persentase_bagi_hasil = (double) $sumberPendanaanEksternal->persentase_bagi_hasil / 100;
            }
        }
    }
}
