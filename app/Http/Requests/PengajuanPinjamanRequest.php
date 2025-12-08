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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validate = [
            'sumber_pembiayaan' => 'required|in:Eksternal,Internal',
            'id_instansi' => 'required_if:sumber_pembiayaan,Eksternal|exists:master_sumber_pendanaan_eksternal,id_instansi',
            'nama_rekening' => 'required',
            'lampiran_sid' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'tujuan_pembiayaan' => 'required',
            'jenis_pembiayaan' => 'required|in:' . implode(',', JenisPembiayaanEnum::getConstants()),
            'harapan_tanggal_pencairan' => 'required_unless:jenis_pembiayaan,Installment|date_format:d/m/Y',
            'rencana_tgl_pembayaran' => 'required_unless:jenis_pembiayaan,Installment|date_format:d/m/Y',
            'tenor_pembayaran' => 'required_if:jenis_pembiayaan,Installment|in:3,6,9,12',
            'catatan_lainnya' => 'required',
        ];

        $jenisPembiayaan = $this->input('jenis_pembiayaan');
        $formDataInvoice = $this->input('form_data_invoice', []);
        
        if ($jenisPembiayaan) {
            $invoiceRequest = new InvoicePengajuanPinjamanRequest();
            $invoiceRules = $invoiceRequest->getRules($jenisPembiayaan, $formDataInvoice);
            
            foreach ($invoiceRules as $key => $rule) {
                if ($key == 'no_invoice' || $key == 'no_kontrak') {
                    $rule = array_merge($rule, ['distinct']);
                }

                $validate["form_data_invoice.*.{$key}"] = $rule;
            }
        }

        return $validate;
    }

    public function messages(): array
    {
        return [
            'sumber_pembiayaan.required' => 'Sumber pembiayaan harus dipilih.',
            'sumber_pembiayaan.in' => 'Sumber pembiayaan harus diisi.',
            'id_instansi.required_if' => 'Instansi harus dipilih.',
            'id_instansi.exists' => 'Instansi tidak valid.',
            'nama_rekening.required' => 'Nama rekening harus diisi.',
            'lampiran_sid.required' => 'Lampiran SID harus diupload.',
            'lampiran_sid.image' => 'Lampiran SID harus berupa gambar.',
            'lampiran_sid.mimes' => 'Lampiran SID harus berupa gambar JPEG, PNG, atau JPG.',
            'lampiran_sid.max' => 'Lampiran SID tidak boleh lebih besar dari 2MB.',
            'tujuan_pembiayaan.required' => 'Tujuan pembiayaan harus diisi.',
            'jenis_pembiayaan.required' => 'Jenis pembiayaan harus dipilih.',
            'jenis_pembiayaan.in' => 'Jenis pembiayaan harus diisi.',
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
            'harapan_tanggal_pencairan.date_format' => 'Harapan tanggal pencairan harus berupa tanggal.',
            'rencana_tgl_pembayaran.required_unless' => 'Rencana tanggal pembayaran harus diisi.',
            'rencana_tgl_pembayaran.date_format' => 'Rencana tanggal pembayaran harus berupa tanggal.',
            'tenor_pembayaran.required_if' => 'Tenor pembayaran harus diisi.',
            'tenor_pembayaran.in' => 'Tenor pembayaran harus diisi.',
            'catatan_lainnya.required' => 'Catatan lainnya harus diisi.',
        ];
    }
}
