<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoicePengajuanPinjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->getRules(
            $this->jenis_pembiayaan ?? null,
            $this->form_data_invoice ?? []
        );
    }

    /**
     * Bangun rules validasi invoice.
     *
     * @param string|null $jenisPembiayaan
     * @param array       $formDataInvoice  Data invoice yang sudah ada (untuk cek duplikat dalam list)
     * @param array       $excludeIds       ID bukti_peminjaman yang dikecualikan dari unique check (mode edit)
     */
    public function getRules(
        ?string $jenisPembiayaan = null,
        array   $formDataInvoice = [],
        array   $excludeIds      = []
    ): array {
        $jenisPembiayaan = $jenisPembiayaan ?? $this->jenis_pembiayaan ?? null;
        $formDataInvoice = !empty($formDataInvoice) ? $formDataInvoice : ($this->form_data_invoice ?? []);

        // Rule unique no_invoice — exclude record yang sedang diedit
        $uniqueNoInvoice = empty($excludeIds)
            ? Rule::unique('bukti_peminjaman', 'no_invoice')->withoutTrashed()
            : Rule::unique('bukti_peminjaman', 'no_invoice')->withoutTrashed()->whereNotIn('id_bukti_peminjaman', $excludeIds);

        $noInvoiceRules = ['required', 'string', $uniqueNoInvoice];

        // Rule dokumen — nullable (file lama dipertahankan jika tidak diupload ulang)
        $dokumenRule = 'nullable|file|max:2048|mimes:pdf,png,jpg,jpeg';

        switch ($jenisPembiayaan) {
            case 'Invoice Financing':
                return [
                    'no_invoice'      => $noInvoiceRules,
                    'nama_client'     => 'required|string',
                    'nilai_invoice'   => [
                        'required',
                        fn($attr, $val, $fail) => rupiahToRawValue($val) <= 0
                            ? $fail('Nilai invoice harus lebih dari 0.')
                            : null,
                    ],
                    'nilai_pinjaman'  => [
                        'required',
                        new \App\Rules\PinjamanLteInvoiceRule(),
                    ],
                    'invoice_date'    => 'required|date_format:d/m/Y|before_or_equal:due_date',
                    'due_date'        => 'required|date_format:d/m/Y',
                    'dokumen_invoice' => $dokumenRule,
                    'dokumen_kontrak' => $dokumenRule,
                    'dokumen_so'      => $dokumenRule,
                    'dokumen_bast'    => $dokumenRule,
                ];

            case 'Installment':
                return [
                    'no_invoice'      => $noInvoiceRules,
                    'nama_client'     => 'required|string',
                    'nilai_invoice'   => [
                        'required',
                        fn($attr, $val, $fail) => rupiahToRawValue($val) <= 0
                            ? $fail('Nilai invoice harus lebih dari 0.')
                            : null,
                    ],
                    'invoice_date'    => 'required|date_format:d/m/Y',
                    'nama_barang'     => 'required|string',
                    'dokumen_invoice' => $dokumenRule,
                    'dokumen_lainnya' => $dokumenRule,
                ];

            default:
                return [
                    'no_invoice'      => $noInvoiceRules,
                    'invoice_date'    => 'required|date_format:d/m/Y',
                    'due_date'        => 'required|date_format:d/m/Y',
                    'dokumen_invoice' => $dokumenRule,
                ];
        }
    }

    public function messages(): array
    {
        return [
            'no_invoice.required'          => 'No. Invoice harus diisi.',
            'no_invoice.string'            => 'No. Invoice harus berupa teks.',
            'no_invoice.unique'            => 'No. Invoice sudah digunakan.',
            'nama_client.required'         => 'Nama client harus diisi.',
            'nama_client.string'           => 'Nama client harus berupa teks.',
            'nilai_invoice.required'       => 'Nilai invoice harus diisi.',
            'nilai_pinjaman.required'      => 'Nilai pinjaman harus diisi.',
            'invoice_date.required'        => 'Tanggal invoice harus diisi.',
            'invoice_date.date_format'     => 'Tanggal invoice harus berformat DD/MM/YYYY.',
            'invoice_date.before_or_equal' => 'Tanggal invoice tidak boleh melebihi tanggal jatuh tempo.',
            'due_date.required'            => 'Tanggal jatuh tempo harus diisi.',
            'due_date.date_format'         => 'Tanggal jatuh tempo harus berformat DD/MM/YYYY.',
            'dokumen_invoice.file'         => 'Dokumen invoice harus berupa file.',
            'dokumen_invoice.max'          => 'Ukuran dokumen invoice maksimal 2 MB.',
            'dokumen_invoice.mimes'        => 'Format dokumen invoice harus pdf, png, atau jpg.',
            'dokumen_kontrak.file'         => 'Dokumen kontrak harus berupa file.',
            'dokumen_kontrak.max'          => 'Ukuran dokumen kontrak maksimal 2 MB.',
            'dokumen_kontrak.mimes'        => 'Format dokumen kontrak harus pdf, png, atau jpg.',
            'dokumen_so.file'              => 'Dokumen SO harus berupa file.',
            'dokumen_so.max'               => 'Ukuran dokumen SO maksimal 2 MB.',
            'dokumen_so.mimes'             => 'Format dokumen SO harus pdf, png, atau jpg.',
            'dokumen_bast.file'            => 'Dokumen BAST harus berupa file.',
            'dokumen_bast.max'             => 'Ukuran dokumen BAST maksimal 2 MB.',
            'dokumen_bast.mimes'           => 'Format dokumen BAST harus pdf, png, atau jpg.',
            'dokumen_lainnya.file'         => 'Dokumen lainnya harus berupa file.',
            'dokumen_lainnya.max'          => 'Ukuran dokumen lainnya maksimal 2 MB.',
            'dokumen_lainnya.mimes'        => 'Format dokumen lainnya harus pdf, png, atau jpg.',
            'nama_barang.required'         => 'Nama barang harus diisi.',
            'nama_barang.string'           => 'Nama barang harus berupa teks.',
        ];
    }
}
