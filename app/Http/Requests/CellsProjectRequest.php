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
            'nama_cells_bisnis' => [
                'required',
                'string',
                'max:255',
                'unique:cells_projects,nama_cells_bisnis'
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
            'projects' => [
                'nullable',
                'array'
            ],
            'projects.*' => [
                'required',
                'string',
                'max:255'
            ],
            'tanda_tangan_pic' => [
                'nullable',
            ],
            'profile_pict' => [
                'nullable',
            ],
        ];

        if ($this->id_cells_project) {
            $validate['nama_cells_bisnis'] = [
                'required',
                'string',
                'max:255',
                'unique:cells_projects,nama_cells_bisnis,' . $this->id_cells_project . ',id_cells_project'
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
            $validate['projects'] = [
                'nullable',
                'array'
            ];
            $validate['projects.*'] = [
                'required',
                'string',
                'max:255'
            ];
            $validate['tanda_tangan_pic'] = [
                'nullable', // Validation image dilakukan di Livewire sebelum convert ke path
            ];
            $validate['profile_pict'] = [
                'nullable', // Validation image dilakukan di Livewire sebelum convert ke path
            ];
        } else {
            $validate['tanda_tangan_pic'] = [
                'nullable',
            ];
            $validate['profile_pict'] = [
                'nullable',
            ];
        }

        return $validate;
    }

    public function messages(): array
    {
        return [
            'nama_cells_bisnis.required' => 'Nama cells bisnis harus diisi',
            'nama_cells_bisnis.string' => 'Nama cells bisnis harus berupa text',
            'nama_cells_bisnis.max' => 'Nama cells bisnis maksimal 255 karakter',
            'nama_cells_bisnis.unique' => 'Nama cells bisnis sudah terdaftar',
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
            'projects.array' => 'Projects harus berupa array',
            'projects.*.required' => 'Nama project harus diisi',
            'projects.*.string' => 'Nama project harus berupa text',
            'projects.*.max' => 'Nama project maksimal 255 karakter',
        ];
    }
}
