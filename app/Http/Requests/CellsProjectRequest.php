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
            ],
            'nama_pic' => [
                'required',
                'string',
                'max:255',
                'unique:cells_projects,nama_pic'
            ],
            'alamat' => [
                'required',
                'string',
                'max:255',
            ],
            'deskripsi_bidang' => [
                'required',
                'string',
                'max:255',
            ],
        ];

        if ($this->id_cells_project) {
            $validate['nama_project'] = [
                'required',
                'string',
                'max:255',
                'unique:cells_projects,nama_project,' . $this->id_cells_project . ',id_cells_project'
            ];
            $validate['nama_pic'] = [
                'required',
                'string',
                'max:255',
                'unique:cells_projects,nama_pic,' . $this->id_cells_project . ',id_cells_project'
            ];
            $validate['alamat'] = [
                'required',
                'string',
                'max:255',
            ];
            $validate['deskripsi_bidang'] = [
                'required',
                'string',
                'max:255',
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
            'nama_pic.required' => 'Nama PIC harus diisi',
            'nama_pic.string' => 'Nama PIC harus berupa text',
            'nama_pic.max' => 'Nama PIC maksimal 255 karakter',
            'nama_pic.unique' => 'Nama PIC sudah terdaftar',
            'alamat.required' => 'Alamat harus diisi',
            'alamat.string' => 'Alamat harus berupa text',
            'alamat.max' => 'Alamat maksimal 255 karakter',
            'deskripsi_bidang.required' => 'Deskripsi bidang harus diisi',
            'deskripsi_bidang.string' => 'Deskripsi bidang harus berupa text',
            'deskripsi_bidang.max' => 'Deskripsi bidang maksimal 255 karakter',
        ];
    }
}

