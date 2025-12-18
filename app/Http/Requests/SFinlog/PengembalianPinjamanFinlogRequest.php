<?php

namespace App\Http\Requests\SFinlog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengembalianPinjamanFinlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Root validation
            'id_pinjaman_finlog' => ['required', 'string', 'exists:peminjaman_finlog,id_peminjaman_finlog'],


            // Array validation
            'pengembalian_list' => ['required', 'array', 'min:1'],
            'pengembalian_list.*.id_cells_project' => ['required', 'string', 'exists:cells_projects,id_cells_project'],
            'pengembalian_list.*.id_project' => ['required', 'string', 'exists:projects,id_project'],
            'pengembalian_list.*.nominal' => ['required', 'numeric', 'min:0'],
            'pengembalian_list.*.sisa_pinjaman' => ['required', 'numeric', 'min:0'],
            'pengembalian_list.*.sisa_bagi_hasil' => ['required', 'numeric', 'min:0'],
            'pengembalian_list.*.total_sisa_pinjaman' => ['required', 'numeric', 'min:0'],
            'pengembalian_list.*.bukti_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'pengembalian_list.*.jatuh_tempo' => ['nullable', 'date'],
            'pengembalian_list.*.catatan' => ['nullable', 'string', 'max:1000'],
            'pengembalian_list.*.status' => ['required', Rule::in(['Lunas', 'Belum Lunas', 'Terlambat'])],
        ];
    }

    public function messages(): array
    {
        return [
            'id_peminjaman_finlog.required' => 'Peminjaman Finlog wajib dipilih.',
            'id_peminjaman_finlog.exists' => 'Peminjaman Finlog tidak ditemukan.',
            'pengembalian_list.required' => 'Data pengembalian tidak boleh kosong.',
            'pengembalian_list.min' => 'Data pengembalian minimal 1.',
            'pengembalian_list.*.nominal.required' => 'Nominal pengembalian wajib diisi.',
            'pengembalian_list.*.nominal.min' => 'Nominal pengembalian tidak boleh kurang dari 0.',
            'pengembalian_list.*.bukti_file.mimes' => 'Bukti pembayaran harus PDF/Gambar.',
            'pengembalian_list.*.bukti_file.max' => 'Ukuran file per bukti maksimal 2MB.',
        ];
    }

    protected function prepareForValidation()
    {
        // Ensure id_pinjaman_finlog exists if id_peminjaman_finlog is provided
        if ($this->has('id_peminjaman_finlog') && !$this->has('id_pinjaman_finlog')) {
            $this->merge(['id_pinjaman_finlog' => $this->input('id_peminjaman_finlog')]);
        }

        // Handle single request support (legacy) if needed, but we focus on list now
    }
}
