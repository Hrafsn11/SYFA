<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PortofolioRequest extends FormRequest
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
            'nama_sbu' => 'required|string|in:'.implode(',', \App\Enums\NamaSBUEnum::getConstants()),
            'tahun' => [
                'required',
                'integer',
                'date_format:Y',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_sbu.required' => 'Nama SBU wajib diisi.',
            'nama_sbu.string' => 'Nama SBU harus berupa teks.',
            'nama_sbu.in' => 'Nama SBU tidak valid.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'tahun.date_format' => 'Tahun harus dalam format YYYY.',
        ];
    }
}
