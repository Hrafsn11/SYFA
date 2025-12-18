<?php

namespace App\Http\Requests;

use App\Enums\JenisPembiayaanEnum;
use Illuminate\Foundation\Http\FormRequest;

class PengajuanPinjamanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * Normalize sumber_pembiayaan to proper case for consistency.
     */
    protected function prepareForValidation()
    {
        if ($this->has('sumber_pembiayaan')) {
            $sumber = $this->input('sumber_pembiayaan');
            if (strtolower($sumber) === 'eksternal') {
                $this->merge(['sumber_pembiayaan' => 'Eksternal']);
            } elseif (strtolower($sumber) === 'internal') {
                $this->merge(['sumber_pembiayaan' => 'Internal']);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $jenisPembiayaan = $this->input('jenis_pembiayaan');
        
        $validate = [
            // Always required for all types
            'nama_rekening' => 'required',
            'tujuan_pembiayaan' => 'nullable|required_unless:jenis_pembiayaan,Factoring,Installment',
            'jenis_pembiayaan' => 'required|in:' . implode(',', JenisPembiayaanEnum::getConstants()),
            'catatan_lainnya' => 'nullable',
            
            // Only for Invoice Financing & PO Financing
            'sumber_pembiayaan' => 'nullable|required_if:jenis_pembiayaan,Invoice Financing,PO Financing|in:Eksternal,Internal,eksternal,internal',
            'id_instansi' => 'nullable|required_if:sumber_pembiayaan,Eksternal,eksternal|exists:master_sumber_pendanaan_eksternal,id_instansi',
            'lampiran_sid' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $jenisPembiayaan = $this->input('jenis_pembiayaan');
                    // Only validate for Invoice Financing or PO Financing
                    if (in_array($jenisPembiayaan, ['Invoice Financing', 'PO Financing'])) {
                        // Check if file is required
                        if (!$value && !$this->input('lampiran_sid_current')) {
                            $fail('Lampiran SID harus diupload untuk ' . $jenisPembiayaan . '.');
                            return;
                        }
                        // Validate file type if uploaded
                        if ($value) {
                            if (!$value->isValid()) {
                                $fail('Lampiran SID tidak valid.');
                                return;
                            }
                            $allowedMimes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/png', 'image/jpeg', 'image/jpg', 'application/x-rar-compressed', 'application/zip'];
                            $allowedExtensions = ['pdf', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg', 'rar', 'zip'];
                            $extension = strtolower($value->getClientOriginalExtension());
                            
                            if (!in_array($value->getMimeType(), $allowedMimes) && !in_array($extension, $allowedExtensions)) {
                                $fail('Lampiran SID harus berupa file PDF, DOCX, XLS, PNG, RAR, atau ZIP.');
                                return;
                            }
                            if ($value->getSize() > 2048 * 1024) {
                                $fail('Lampiran SID maksimal 2MB.');
                                return;
                            }
                        }
                    }
                },
            ],
            
            // Required for Invoice Financing, PO Financing, and Factoring
            'harapan_tanggal_pencairan' => 'required_unless:jenis_pembiayaan,Installment|date_format:Y-m-d',
            'rencana_tgl_pembayaran' => 'required_unless:jenis_pembiayaan,Installment|date_format:Y-m-d',
            
            // Only for Installment
            'tenor_pembayaran' => 'nullable|required_if:jenis_pembiayaan,Installment|in:3,6,9,12',
        ];

        $jenisPembiayaan = $this->input('jenis_pembiayaan');
        // Handle both 'form_data_invoice' (Invoice/PO) and 'details' (Factoring/Installment) keys
        $formDataInvoice = $this->input('form_data_invoice', $this->input('details', []));
        $invoiceKey = $this->has('form_data_invoice') ? 'form_data_invoice' : 'details';
        
        if ($jenisPembiayaan && !empty($formDataInvoice)) {
            $invoiceRequest = new InvoicePengajuanPinjamanRequest();
            $invoiceRules = $invoiceRequest->getRules($jenisPembiayaan, $formDataInvoice);
            
            foreach ($invoiceRules as $key => $rule) {
                if ($key == 'no_invoice' || $key == 'no_kontrak') {
                    $rule = array_merge($rule, ['distinct']);
                }

                // Apply rules to the correct key (form_data_invoice or details)
                $validate["{$invoiceKey}.*.{$key}"] = $rule;
            }
        }

        return $validate;
    }

    public function messages(): array
    {
        return [
            // Common fields
            'nama_rekening.required' => 'Nama rekening harus diisi.',
            'tujuan_pembiayaan.required' => 'Tujuan pembiayaan harus diisi.',
            'jenis_pembiayaan.required' => 'Jenis pembiayaan harus dipilih.',
            'jenis_pembiayaan.in' => 'Jenis pembiayaan tidak valid.',
            
            // Invoice & PO Financing specific
            'sumber_pembiayaan.required_if' => 'Sumber pembiayaan harus dipilih untuk Invoice Financing atau PO Financing.',
            'sumber_pembiayaan.in' => 'Sumber pembiayaan tidak valid.',
            'id_instansi.required_if' => 'Instansi harus dipilih ketika sumber pembiayaan Eksternal.',
            'id_instansi.exists' => 'Instansi tidak valid.',
            'lampiran_sid.required_if' => 'Lampiran SID harus diupload untuk Invoice Financing atau PO Financing.',
            'lampiran_sid.image' => 'Lampiran SID harus berupa gambar.',
            'lampiran_sid.mimes' => 'Lampiran SID harus berupa gambar JPEG, PNG, atau JPG.',
            'lampiran_sid.max' => 'Lampiran SID tidak boleh lebih besar dari 2MB.',
            
            // Form data invoice/contract
            'form_data_invoice.required' => 'Data invoice harus diisi.',
            'form_data_invoice.min' => 'Data invoice minimal 1 item.',
            'form_data_invoice.*.no_invoice.required' => 'No. invoice harus diisi.',
            'form_data_invoice.*.no_invoice.string' => 'No. invoice harus berupa teks.',
            'form_data_invoice.*.no_invoice.unique' => 'No. invoice sudah digunakan.',
            'form_data_invoice.*.invoice_date.required' => 'Tanggal invoice harus diisi.',
            'form_data_invoice.*.invoice_date.date_format' => 'Tanggal invoice harus berupa tanggal.',
            'form_data_invoice.*.due_date.required' => 'Tanggal jatuh tempo harus diisi.',
            'form_data_invoice.*.due_date.date_format' => 'Tanggal jatuh tempo harus berupa tanggal.',
            'form_data_invoice.*.dokumen_invoice.required' => 'Dokumen invoice harus diupload.',
            'form_data_invoice.*.dokumen_invoice.file' => 'Dokumen invoice harus berupa file.',
            'form_data_invoice.*.dokumen_invoice.mimes' => 'Dokumen invoice harus berupa file PDF, DOCX, XLS, PNG, RAR, atau ZIP.',
            'form_data_invoice.*.dokumen_invoice.max' => 'Dokumen invoice tidak boleh lebih besar dari 2MB.',
            'form_data_invoice.*.dokumen_kontrak.required' => 'Dokumen kontrak harus diupload.',
            'form_data_invoice.*.dokumen_kontrak.file' => 'Dokumen kontrak harus berupa file.',
            'form_data_invoice.*.dokumen_kontrak.mimes' => 'Dokumen kontrak harus berupa file PDF, DOCX, XLS, PNG, RAR, atau ZIP.',
            'form_data_invoice.*.dokumen_kontrak.max' => 'Dokumen kontrak tidak boleh lebih besar dari 2MB.',
            'form_data_invoice.*.dokumen_so.required' => 'Dokumen SO harus diupload.',
            'form_data_invoice.*.dokumen_so.file' => 'Dokumen SO harus berupa file.',
            'form_data_invoice.*.dokumen_so.mimes' => 'Dokumen SO harus berupa file PDF, DOCX, XLS, PNG, RAR, atau ZIP.',
            'form_data_invoice.*.dokumen_so.max' => 'Dokumen SO tidak boleh lebih besar dari 2MB.',
            'form_data_invoice.*.dokumen_bast.required' => 'Dokumen BAST harus diupload.',
            'form_data_invoice.*.dokumen_bast.file' => 'Dokumen BAST harus berupa file.',
            'form_data_invoice.*.dokumen_bast.mimes' => 'Dokumen BAST harus berupa file PDF, DOCX, XLS, PNG, RAR, atau ZIP.',
            'form_data_invoice.*.dokumen_bast.max' => 'Dokumen BAST tidak boleh lebih besar dari 2MB.',
            'harapan_tanggal_pencairan.required_unless' => 'Harapan tanggal pencairan harus diisi.',
            'harapan_tanggal_pencairan.date_format' => 'Harapan tanggal pencairan harus berupa tanggal dengan format yang valid (YYYY-MM-DD).',
            'rencana_tgl_pembayaran.required_unless' => 'Rencana tanggal pembayaran harus diisi.',
            'rencana_tgl_pembayaran.date_format' => 'Rencana tanggal pembayaran harus berupa tanggal dengan format yang valid (YYYY-MM-DD).',
            'tenor_pembayaran.required_if' => 'Tenor pembayaran harus diisi untuk Installment.',
            'tenor_pembayaran.in' => 'Tenor pembayaran harus 3, 6, 9, atau 12 bulan.',
        ];
    }
}
