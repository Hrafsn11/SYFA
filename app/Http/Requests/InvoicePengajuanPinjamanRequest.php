<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoicePengajuanPinjamanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->getRules(
            $this->jenis_pembiayaan ?? null,
            $this->form_data_invoice ?? []
        );
    }

    /**
     * Get validation rules with parameters (for use in other request classes)
     *
     * @param string|null $jenisPembiayaan
     * @param int|null $indexDataInvoice
     * @param array $formDataInvoice
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function getRules(?string $jenisPembiayaan = null, array $formDataInvoice = []): array
    {        
        // Base rules yang berlaku untuk semua jenis pembiayaan
        $validate = [];

        $jenisPembiayaan = $jenisPembiayaan ?? $this->jenis_pembiayaan ?? null;
        $formDataInvoice = !empty($formDataInvoice) ? $formDataInvoice : ($this->form_data_invoice ?? []);

        switch ($jenisPembiayaan) {
            case 'Invoice Financing':
                $validate = array_merge($validate, [
                    'no_invoice' => [
                        'required',
                        'string',
                        'unique:bukti_peminjaman,no_invoice',
                        function ($attribute, $value, $fail) use ($formDataInvoice) {
                            if (is_array($formDataInvoice)) {
                                $collection = collect($formDataInvoice);
                                $max = 2;

                                if (!is_null($this->index_data_invoice) && $collection->has($this->index_data_invoice)) {
                                    $collection->forget($this->index_data_invoice);
                                    $max = 1;
                                }

                                $existingNoInvoice = $collection->where('no_invoice', $value)->count();
                                if ($existingNoInvoice >= $max) {
                                    $fail('No. Invoice sudah digunakan dalam list invoice yang akan ditambahkan.');
                                }
                            }
                        },
                    ],
                    'nama_client' => 'required|string',
                    'nilai_invoice' => 'required',
                    'nilai_pinjaman' => 'required',
                    'invoice_date' => 'required|date_format:d/m/Y|before_or_equal:due_date',
                    'due_date' => 'required|date_format:d/m/Y',
                    'dokumen_invoice' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_kontrak' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_so' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_bast' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                ]);

                if ($this->index_data_invoice !== null) $validate['dokumen_invoice'] = 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip';
                break;

            case 'PO Financing':
                $validate = array_merge($validate, [
                    'no_kontrak' => [
                        'required',
                        'string',
                        function ($attribute, $value, $fail) use ($formDataInvoice) {
                            if (is_array($formDataInvoice)) {
                                $collection = collect($formDataInvoice);
                                $max = 2;

                                if (!is_null($this->index_data_invoice) && $collection->has($this->index_data_invoice)) {
                                    $collection->forget($this->index_data_invoice);
                                    $max = 1;
                                }

                                $existingNoKontrak = $collection->where('no_kontrak', $value)->count();
                                if ($existingNoKontrak >= $max) {
                                    $fail('No. Kontrak sudah digunakan dalam list kontrak yang akan ditambahkan.');
                                }
                            }
                        },
                    ],
                    'nama_client' => 'required|string',
                    'nilai_invoice' => 'required',
                    'nilai_pinjaman' => 'required',
                    'kontrak_date' => 'required|date_format:d/m/Y',
                    'due_date' => 'required|date_format:d/m/Y',
                    'dokumen_kontrak' => 'required|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_so' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_bast' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_lainnya' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                ]);

                if ($this->index_data_invoice !== null) $validate['dokumen_kontrak'] = 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip';
                break;

            case 'Installment':
                $validate = array_merge($validate, [
                    'no_invoice' => [
                        'required',
                        'string',
                        'unique:bukti_peminjaman,no_invoice',
                        function ($attribute, $value, $fail) use ($formDataInvoice) {
                            if (is_array($formDataInvoice)) {
                                $collection = collect($formDataInvoice);
                                $max = 2;

                                if (!is_null($this->index_data_invoice) && $collection->has($this->index_data_invoice)) {
                                    $collection->forget($this->index_data_invoice);
                                    $max = 1;
                                }

                                $existingNoInvoice = $collection->where('no_invoice', $value)->count();
                                if ($existingNoInvoice >= $max) {
                                    $fail('No. Invoice sudah digunakan dalam list invoice yang akan ditambahkan.');
                                }
                            }
                        },
                    ],
                    'nama_client' => 'required|string',
                    'nilai_invoice' => 'required',
                    'invoice_date' => 'required|date_format:d/m/Y',
                    'nama_barang' => 'required|string',
                    'dokumen_invoice' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_lainnya' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                ]);

                if ($this->index_data_invoice !== null) $validate['dokumen_invoice'] = 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip';
                break;

            case 'Factoring':
                $validate = array_merge($validate, [
                    'no_kontrak' => [
                        'required',
                        'string',
                        function ($attribute, $value, $fail) use ($formDataInvoice) {
                            if (is_array($formDataInvoice)) {
                                $collection = collect($formDataInvoice);
                                $max = 2;

                                if (!is_null($this->index_data_invoice) && $collection->has($this->index_data_invoice)) {
                                    $collection->forget($this->index_data_invoice);
                                    $max = 1;
                                }

                                $existingNoKontrak = $collection->where('no_kontrak', $value)->count();

                                if ($existingNoKontrak >= $max) {
                                    $fail('No. Kontrak sudah digunakan dalam list kontrak yang akan ditambahkan.');
                                }
                            }
                        },
                    ],
                    'nama_client' => 'required|string',
                    'nilai_invoice' => 'required',
                    'nilai_pinjaman' => 'required',
                    'kontrak_date' => 'required|date_format:d/m/Y',
                    'due_date' => 'required|date_format:d/m/Y',
                    'dokumen_invoice' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_kontrak' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_so' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                    'dokumen_bast' => 'nullable|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                ]);
                break;

            default:
                // Default rules jika jenis pembiayaan tidak dikenali
                $validate = array_merge($validate, [
                    'no_invoice' => [
                        'required',
                        'string',
                        'unique:bukti_peminjaman,no_invoice',
                        function ($attribute, $value, $fail) use ($formDataInvoice) {
                            if (is_array($formDataInvoice)) {
                                $existingNoInvoice = collect($formDataInvoice)->pluck('no_invoice')->toArray();
                                if (in_array($value, $existingNoInvoice)) {
                                    $fail('No. Invoice sudah digunakan dalam list invoice yang akan ditambahkan.');
                                }
                            }
                        },
                    ],
                    'invoice_date' => 'required|date_format:d/m/Y',
                    'due_date' => 'required|date_format:d/m/Y',
                    'dokumen_invoice' => 'required|file|max:2048|mimes:pdf,docx,xls,png,rar,zip',
                ]);
                break;
        }

        return $validate;
    }

    public function messages(): array
    {
        return [
            'no_invoice.required' => 'No. Invoice harus diisi.',
            'no_invoice.string' => 'No. Invoice harus berupa teks.',
            'no_invoice.unique' => 'No. Invoice sudah digunakan.',
            'nama_client.required' => 'Nama client harus diisi.',
            'nama_client.string' => 'Nama client harus berupa teks.',
            'nilai_invoice.required' => 'Nilai invoice harus diisi.',
            'nilai_invoice.numeric' => 'Nilai invoice harus berupa angka.',
            'invoice_date.required' => 'Tanggal invoice harus diisi.',
            'invoice_date.date_format' => 'Tanggal invoice harus berupa tanggal.',
            'invoice_date.before_or_equal' => 'Tanggal invoice tidak boleh melebihi tanggal jatuh tempo.',
            'due_date.required' => 'Tanggal jatuh tempo harus diisi.',
            'due_date.date_format' => 'Tanggal jatuh tempo harus berupa tanggal.',
            'dokumen_invoice.required' => 'Dokumen invoice harus diisi.',
            'dokumen_invoice.file' => 'Dokumen invoice harus berupa file.',
            'dokumen_invoice.max' => 'Ukuran dokumen invoice maksimal 2 MB.',
            'dokumen_invoice.mimes' => 'Format dokumen invoice harus pdf, docx, xls, png, rar, atau zip.',
            'dokumen_kontrak.required' => 'Dokumen kontrak harus diisi.',
            'dokumen_kontrak.file' => 'Dokumen kontrak harus berupa file.',
            'dokumen_kontrak.max' => 'Ukuran dokumen kontrak maksimal 2 MB.',
            'dokumen_kontrak.mimes' => 'Format dokumen kontrak harus pdf, docx, xls, png, rar, atau zip.',
            'dokumen_so.required' => 'Dokumen SO harus diisi.',
            'dokumen_so.file' => 'Dokumen SO harus berupa file.',
            'dokumen_so.max' => 'Ukuran dokumen SO maksimal 2 MB.',
            'dokumen_so.mimes' => 'Format dokumen SO harus pdf, docx, xls, png, rar, atau zip.',
            'dokumen_bast.required' => 'Dokumen BAST harus diisi.',
            'dokumen_bast.file' => 'Dokumen BAST harus berupa file.',
            'dokumen_bast.max' => 'Ukuran dokumen BAST maksimal 2 MB.',
            'dokumen_bast.mimes' => 'Format dokumen BAST harus pdf, docx, xls, png, rar, atau zip.',
            'dokumen_lainnya.required' => 'Dokumen lainnya harus diisi.',
            'dokumen_lainnya.file' => 'Dokumen lainnya harus berupa file.',
            'dokumen_lainnya.max' => 'Ukuran dokumen lainnya maksimal 2 MB.',
            'dokumen_lainnya.mimes' => 'Format dokumen lainnya harus pdf, docx, xls, png, rar, atau zip.',
            'nama_barang.required' => 'Nama barang harus diisi.',
            'nama_barang.string' => 'Nama barang harus berupa teks.',
        ];
    }
}
