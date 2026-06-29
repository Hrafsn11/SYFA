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
     */
    protected function prepareForValidation()
    {
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
            'nama_bank' => 'nullable',
            'no_rekening' => 'nullable',
            'nama_rekening' => 'required',
            'tujuan_pembiayaan' => 'nullable|required_unless:jenis_pembiayaan,Installment',
            'jenis_pembiayaan' => 'required|in:' . implode(',', JenisPembiayaanEnum::getConstants()),
            'catatan_lainnya' => 'nullable',
            
            'sumber_pembiayaan' => 'nullable',
            'id_instansi' => 'nullable|exists:master_sumber_pendanaan_eksternal,id_instansi',
            'lampiran_sid' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $jenisPembiayaan = $this->input('jenis_pembiayaan');
                    // Only validate for Invoice Financing
                    if (in_array($jenisPembiayaan, ['Invoice Financing'])) {
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
                            $allowedMimes = ['application/pdf', 'image/png', 'image/jpeg'];
                            $allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg'];
                            $extension = strtolower($value->getClientOriginalExtension());
                            
                            if (!in_array($value->getMimeType(), $allowedMimes) && !in_array($extension, $allowedExtensions)) {
                                $fail('Lampiran SID harus berupa file PDF, PNG, atau JPG.');
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
            
            // Required for Invoice Financing
            'harapan_tanggal_pencairan' => 'required_unless:jenis_pembiayaan,Installment|date_format:d/m/Y',
            'rencana_tgl_pembayaran' => 'required_unless:jenis_pembiayaan,Installment|date_format:d/m/Y',
            
            // Only for Installment
            'tenor_pembayaran' => 'nullable|required_if:jenis_pembiayaan,Installment|in:3,6,9,12',
        ];

        $jenisPembiayaan = $this->input('jenis_pembiayaan');
        // Handle 'form_data_invoice' keys (Invoice Financing / Installment)
        $formDataInvoice = $this->input('form_data_invoice', []);
        $invoiceKey = 'form_data_invoice';
        
        if ($jenisPembiayaan && !empty($formDataInvoice)) {
            $invoiceRequest = new InvoicePengajuanPinjamanRequest();
            $invoiceRules = $invoiceRequest->getRules($jenisPembiayaan, $formDataInvoice);
            
            foreach ($invoiceRules as $key => $rule) {
                if (is_string($rule)) {
                    $ruleArray = explode('|', $rule);
                } else {
                    $ruleArray = (array) $rule;
                }
                
                if ($key === 'no_invoice' || $key === 'no_kontrak') {
                    $ruleArray[] = 'distinct';
                }

                $validate["{$invoiceKey}.*.{$key}"] = $ruleArray;
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
            
            'id_instansi.exists' => 'Instansi tidak valid.',
            'lampiran_sid.required_if' => 'Lampiran SID harus diupload untuk Invoice Financing.',
            'lampiran_sid.image' => 'Lampiran SID harus berupa gambar.',
            'lampiran_sid.mimes' => 'Lampiran SID harus berupa file PDF, PNG, atau JPG.',
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
            'harapan_tanggal_pencairan.date_format' => 'Harapan tanggal pencairan harus berupa tanggal dengan format DD/MM/YYYY.',
            'rencana_tgl_pembayaran.required_unless' => 'Rencana tanggal pembayaran harus diisi.',
            'rencana_tgl_pembayaran.date_format' => 'Rencana tanggal pembayaran harus berupa tanggal dengan format DD/MM/YYYY.',
            'tenor_pembayaran.required_if' => 'Tenor pembayaran harus diisi untuk Installment.',
            'tenor_pembayaran.in' => 'Tenor pembayaran harus 3, 6, 9, atau 12 bulan.',
        ];
    }
}
