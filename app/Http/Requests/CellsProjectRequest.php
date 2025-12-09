<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CellsProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $validate = [
            'nama_project' => [
                'required',
                'string',
                'max:255',
                'unique:cells_projects,nama_project'
            ]
        ];

        if ($this->id_cells_project) {
            $validate['nama_project'] = [
                'required',
                'string',
                'max:255',
                'unique:cells_projects,nama_project,' . $this->id_cells_project . ',id_cells_project'
            ];
        }

        return $validate;
    }

    public function messages(): array
    {
        return [
            'nama_project.required' => 'Nama project harus diisi',
            'nama_project.string' => 'Nama project harus berupa text',
            'nama_project.max' => 'Nama project maksimal 255 karakter',
            'nama_project.unique' => 'Nama project sudah terdaftar',
        ];
    }
}

