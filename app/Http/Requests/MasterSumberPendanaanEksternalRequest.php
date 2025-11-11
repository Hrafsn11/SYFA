<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasterSumberPendanaanEksternalRequest extends FormRequest
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
            'nama_instansi' => 'required|string|max:255',
            'persentase_bagi_hasil' => 'nullable|integer|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_instansi.required' => 'Nama instansi harus diisi.',
            'persentase_bagi_hasil.integer' => 'Persentase bagi hasil harus berupa angka.',
            'persentase_bagi_hasil.min' => 'Persentase bagi hasil tidak boleh kurang dari :min.',
            'persentase_bagi_hasil.max' => 'Persentase bagi hasil tidak boleh lebih dari :max.',
        ];
    }
}
