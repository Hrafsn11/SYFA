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
            'tanggal_pencairan' => 'required_unless:jenis_pembiayaan,Installment|date_format:d/m/Y',
            'tanggal_pembayaran' => 'required_unless:jenis_pembiayaan,Installment|date_format:d/m/Y',
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
}
