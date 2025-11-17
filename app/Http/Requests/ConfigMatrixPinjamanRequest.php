<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigMatrixPinjamanRequest extends FormRequest
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
            'nominal' => 'required|numeric|min:0',
            'approve_oleh' => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'nominal.required' => 'Nominal harus diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal minimal adalah 0.',
            'approve_oleh.required' => 'Approve oleh harus diisi.',
            'approve_oleh.string' => 'Approve oleh harus berupa teks.',
            'approve_oleh.max' => 'Approve oleh maksimal 255 karakter.'
        ];
    }
}
