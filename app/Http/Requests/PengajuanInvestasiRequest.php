<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengajuanInvestasiRequest extends FormRequest
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
        // Detect which validation rules to use based on input fields
        // This is more reliable than checking method names
        
        // Rules for uploadBuktiTransfer (has 'file' field)
        if ($this->hasFile('file')) {
            return [
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ];
        }

        // Rules for generateKontrak (has 'nomor_kontrak' field)
        if ($this->has('nomor_kontrak')) {
            return [
                'nomor_kontrak' => 'required|string|max:255',
                'tanggal_kontrak' => 'required|date',
                'catatan_kontrak' => 'nullable|string',
            ];
        }

        // Rules for updateStatus (has 'status' field but not CRUD fields)
        if ($this->has('status') && !$this->has('id_debitur_dan_investor')) {
            return [
                'status' => 'required|string',
                'catatan' => 'nullable|string',
            ];
        }

        // Rules for store/update (CRUD operations - has main fields)
        if ($this->has('id_debitur_dan_investor')) {
            return [
                'id_debitur_dan_investor' => 'required|exists:master_debitur_dan_investor,id_debitur',
                'nama_investor' => 'required|string|max:255',
                'deposito' => 'required|in:Reguler,Khusus',
                'tanggal_investasi' => 'required|date',
                'lama_investasi' => 'required|integer|min:1',
                'jumlah_investasi' => 'required|numeric|min:0',
                'bagi_hasil_pertahun' => 'required|integer|min:0|max:100',
            ];
        }

        return [];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // CRUD messages
            'id_debitur_dan_investor.required' => 'Investor harus dipilih.',
            'id_debitur_dan_investor.exists' => 'Investor tidak valid.',
            'nama_investor.required' => 'Nama investor harus diisi.',
            'nama_investor.string' => 'Nama investor harus berupa teks.',
            'nama_investor.max' => 'Nama investor maksimal 255 karakter.',
            'deposito.required' => 'Jenis deposito harus dipilih.',
            'deposito.in' => 'Jenis deposito harus Reguler atau Khusus.',
            'tanggal_investasi.required' => 'Tanggal investasi harus diisi.',
            'tanggal_investasi.date' => 'Tanggal investasi harus berupa tanggal yang valid.',
            'lama_investasi.required' => 'Lama investasi harus diisi.',
            'lama_investasi.integer' => 'Lama investasi harus berupa angka bulat.',
            'lama_investasi.min' => 'Lama investasi minimal :min bulan.',
            'jumlah_investasi.required' => 'Jumlah investasi harus diisi.',
            'jumlah_investasi.numeric' => 'Jumlah investasi harus berupa angka.',
            'jumlah_investasi.min' => 'Jumlah investasi minimal :min.',
            'bagi_hasil_pertahun.required' => 'Bagi hasil per tahun harus diisi.',
            'bagi_hasil_pertahun.integer' => 'Bagi hasil per tahun harus berupa angka bulat.',
            'bagi_hasil_pertahun.min' => 'Bagi hasil per tahun minimal :min%.',
            'bagi_hasil_pertahun.max' => 'Bagi hasil per tahun maksimal :max%.',

            // updateStatus messages
            'status.required' => 'Status harus diisi',
            'status.string' => 'Status harus berupa teks',
            'catatan.string' => 'Catatan harus berupa teks',

            // uploadBuktiTransfer messages
            'file.required' => 'File bukti transfer harus diupload',
            'file.file' => 'File yang diupload harus berupa file',
            'file.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG',
            'file.max' => 'Ukuran file maksimal 2MB',

            // generateKontrak messages
            'nomor_kontrak.required' => 'Nomor kontrak harus diisi',
            'nomor_kontrak.string' => 'Nomor kontrak harus berupa teks',
            'nomor_kontrak.max' => 'Nomor kontrak maksimal 255 karakter',
            'tanggal_kontrak.required' => 'Tanggal kontrak harus diisi',
            'tanggal_kontrak.date' => 'Tanggal kontrak harus berupa tanggal yang valid',
            'catatan_kontrak.string' => 'Catatan kontrak harus berupa teks',
        ];
    }
}
