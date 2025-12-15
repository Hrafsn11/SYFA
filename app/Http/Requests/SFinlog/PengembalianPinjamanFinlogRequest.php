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
            'id_pinjaman_finlog' => ['required', 'string', 'exists:peminjaman_finlog,id_peminjaman_finlog'],
            'id_cells_project' => ['required', 'string', 'exists:cells_projects,id_cells_project'],
            'id_project' => ['required', 'string', 'exists:projects,id_project'],
            'jumlah_pengembalian' => ['required', 'numeric', 'min:0'],
            'sisa_pinjaman' => ['required', 'numeric', 'min:0'],
            'sisa_bagi_hasil' => ['required', 'numeric', 'min:0'],
            'total_sisa_pinjaman' => ['required', 'numeric', 'min:0'],
            'tanggal_pengembalian' => ['required', 'date'],
            'bukti_pembayaran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'jatuh_tempo' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['Lunas', 'Belum Lunas', 'Terlambat'])],
        ];
    }

    public function messages(): array
    {
        return [
            'id_pinjaman_finlog.required' => 'Peminjaman Finlog wajib dipilih.',
            'id_pinjaman_finlog.exists' => 'Peminjaman Finlog tidak ditemukan.',
            'id_cells_project.required' => 'Cells Project wajib dipilih.',
            'id_cells_project.exists' => 'Cells Project tidak ditemukan.',
            'id_project.required' => 'Project wajib dipilih.',
            'id_project.exists' => 'Project tidak ditemukan.',
            'jumlah_pengembalian.required' => 'Jumlah pengembalian wajib diisi.',
            'jumlah_pengembalian.numeric' => 'Jumlah pengembalian harus berupa angka.',
            'jumlah_pengembalian.min' => 'Jumlah pengembalian tidak boleh kurang dari 0.',
            'sisa_pinjaman.required' => 'Sisa pinjaman wajib diisi.',
            'sisa_bagi_hasil.required' => 'Sisa bagi hasil wajib diisi.',
            'total_sisa_pinjaman.required' => 'Total sisa pinjaman wajib diisi.',
            'tanggal_pengembalian.required' => 'Tanggal pengembalian wajib diisi.',
            'tanggal_pengembalian.date' => 'Format tanggal pengembalian tidak valid.',
            'bukti_pembayaran.mimes' => 'Bukti pembayaran harus berupa file PDF, JPG, JPEG, atau PNG.',
            'bukti_pembayaran.max' => 'Ukuran file bukti pembayaran maksimal 2MB.',
            'jatuh_tempo.date' => 'Format tanggal jatuh tempo tidak valid.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}
