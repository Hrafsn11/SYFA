<?php

namespace App\Http\Requests\SFinlog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengajuanInvestasiFinlogRequest extends FormRequest
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
        if ($this->hasFile('file')) {
            return [
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ];
        }

        if ($this->has('nomor_kontrak')) {
            $idPengajuan = $this->route('id');
            
            $rules = [
                'nomor_kontrak' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('pengajuan_investasi_finlog', 'nomor_kontrak')
                        ->ignore($idPengajuan, 'id_pengajuan_investasi_finlog')
                ],
            ];
            
            if ($this->has('tanggal_kontrak')) {
                $rules['tanggal_kontrak'] = 'required|date';
            }
            if ($this->has('catatan_kontrak')) {
                $rules['catatan_kontrak'] = 'nullable|string';
            }
            
            return $rules;
        }

        if ($this->has('status') && !$this->has('id_debitur_dan_investor')) {
            return [
                'status' => 'required|string',
                'catatan' => 'nullable|string',
            ];
        }

        if ($this->has('id_debitur_dan_investor')) {
            return [
                'id_debitur_dan_investor' => 'required|exists:master_debitur_dan_investor,id_debitur',
                'id_cells_project' => 'required|exists:cells_projects,id_cells_project',
                'nama_investor' => 'required|string|max:255',
                'tanggal_investasi' => 'required|date',
                'lama_investasi' => 'required|integer|min:1',
                'nominal_investasi' => 'required|numeric|min:0',
                'persentase_bagi_hasil' => 'required|numeric|min:12|max:15',
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
            'id_cells_project.required' => 'Project harus dipilih.',
            'id_cells_project.exists' => 'Project tidak valid.',
            'nama_investor.required' => 'Nama investor harus diisi.',
            'nama_investor.string' => 'Nama investor harus berupa teks.',
            'nama_investor.max' => 'Nama investor maksimal 255 karakter.',
            'tanggal_investasi.required' => 'Tanggal investasi harus diisi.',
            'tanggal_investasi.date' => 'Tanggal investasi harus berupa tanggal yang valid.',
            'lama_investasi.required' => 'Lama investasi harus diisi.',
            'lama_investasi.integer' => 'Lama investasi harus berupa angka bulat.',
            'lama_investasi.min' => 'Lama investasi minimal :min bulan.',
            'nominal_investasi.required' => 'Nominal investasi harus diisi.',
            'nominal_investasi.numeric' => 'Nominal investasi harus berupa angka.',
            'nominal_investasi.min' => 'Nominal investasi minimal :min.',
            'persentase_bagi_hasil.required' => 'Persentase bagi hasil harus diisi.',
            'persentase_bagi_hasil.numeric' => 'Persentase bagi hasil harus berupa angka.',
            'persentase_bagi_hasil.min' => 'Persentase bagi hasil minimal :min%.',
            'persentase_bagi_hasil.max' => 'Persentase bagi hasil maksimal :max%.',

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
            'nomor_kontrak.required' => 'Nomor kontrak harus diisi.',
            'nomor_kontrak.string' => 'Nomor kontrak harus berupa teks.',
            'nomor_kontrak.max' => 'Nomor kontrak maksimal 255 karakter.',
            'nomor_kontrak.unique' => 'Nomor kontrak sudah digunakan.',
            'tanggal_kontrak.required' => 'Tanggal kontrak harus diisi.',
            'tanggal_kontrak.date' => 'Tanggal kontrak harus berupa tanggal yang valid.',
            'catatan_kontrak.string' => 'Catatan kontrak harus berupa teks.',
        ];
    }
}
