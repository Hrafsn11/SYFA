<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengembalianPinjamanRequest extends FormRequest
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
            'kode_peminjaman' => 'required|exists:pengajuan_peminjaman,id_pengajuan_peminjaman',
            'nama_perusahaan' => 'required|string|max:255',
            'total_pinjaman' => 'required|numeric|min:0',
            'total_bunga' => 'required|numeric|min:0',
            'tanggal_pencairan' => 'required|string',
            'lama_pemakaian' => 'required|integer|min:0',
            'nominal_invoice' => 'required|numeric|min:0',
            'invoice_dibayarkan' => 'required|string|max:255',
            'bulan_pembayaran' => 'nullable|string|max:20',
            'yang_harus_dibayarkan' => 'nullable|numeric|min:0',
            'sisa_utang' => 'required|numeric|min:0',
            'sisa_bunga' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'pengembalian_invoices' => 'required|array|min:1',
            'pengembalian_invoices.*.nominal' => 'required|numeric|min:1',
            'pengembalian_invoices.*.file' => 'nullable', // Bisa berupa Object File (saat upload) atau String Path (setelah disave di Livewire)
        ];
    }

    public function messages(): array
    {
        return [
            'kode_peminjaman.required' => 'Kode peminjaman harus dipilih.',
            'kode_peminjaman.exists' => 'Kode peminjaman tidak valid.',
            'nama_perusahaan.required' => 'Nama perusahaan harus diisi.',
            'total_pinjaman.required' => 'Total pinjaman harus diisi.',
            'total_pinjaman.numeric' => 'Total pinjaman harus berupa angka.',
            'total_bunga.required' => 'Total bagi hasil harus diisi.',
            'total_bunga.numeric' => 'Total bagi hasil harus berupa angka.',
            'tanggal_pencairan.required' => 'Tanggal pencairan harus diisi.',
            'lama_pemakaian.required' => 'Lama pemakaian harus diisi.',
            'lama_pemakaian.integer' => 'Lama pemakaian harus berupa angka.',
            'nominal_invoice.required' => 'Nominal invoice harus diisi.',
            'invoice_dibayarkan.required' => 'Invoice yang dibayarkan harus dipilih.',
            'bulan_pembayaran.string' => 'Bulan pembayaran harus berupa teks.',
            'yang_harus_dibayarkan.numeric' => 'Yang harus dibayarkan harus berupa angka.',
            'sisa_utang.required' => 'Sisa utang harus diisi.',
            'sisa_bunga.required' => 'Sisa bagi hasil harus diisi.',
            'pengembalian_invoices.required' => 'Data pengembalian invoice harus diisi.',
            'pengembalian_invoices.min' => 'Data pengembalian invoice minimal 1 item.',
            'pengembalian_invoices.*.nominal.required' => 'Nominal yang dibayarkan harus diisi.',
            'pengembalian_invoices.*.nominal.min' => 'Nominal yang dibayarkan minimal 1.',
        ];
    }
}
