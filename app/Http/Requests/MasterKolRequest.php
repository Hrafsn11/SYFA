<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasterKolRequest extends FormRequest
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
        return [
            'kol' => 'required|integer',
            'persentase_pencairan' => 'required|numeric|min:0|max:100',
            'jmlh_hari_keterlambatan' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'kol.required' => 'Kol harus diisi.',
            'kol.integer' => 'Kol harus berupa angka.',
            'persentase_pencairan.required' => 'Persentase pencairan harus diisi.',
            'persentase_pencairan.numeric' => 'Persentase pencairan harus berupa angka.',
            'persentase_pencairan.min' => 'Persentase pencairan tidak boleh kurang dari :min.',
            'persentase_pencairan.max' => 'Persentase pencairan tidak boleh lebih dari :max.',
            'jmlh_hari_keterlambatan.required' => 'Jumlah hari keterlambatan harus diisi.',
            'jmlh_hari_keterlambatan.integer' => 'Jumlah hari keterlambatan harus berupa angka.'
        ];
    }
}
