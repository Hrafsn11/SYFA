<?php

namespace App\Http\Requests;

use App\Models\PengajuanInvestasi;
use Illuminate\Foundation\Http\FormRequest;

class PenyaluranDepositoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('penyaluran-deposito.upload-bukti')) {
            return [
                'bukti_pengembalian' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ];
        }

        // Rules untuk store/update
        $rules = [
            'id_pengajuan_investasi' => 'required|exists:pengajuan_investasi,id_pengajuan_investasi',
            'id_debitur' => 'required|exists:master_debitur_dan_investor,id_debitur',
            'nominal_yang_disalurkan' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (!$this->id_pengajuan_investasi) {
                        return;
                    }

                    $pengajuan = PengajuanInvestasi::withSisaDana()
                        ->where('pengajuan_investasi.id_pengajuan_investasi', $this->id_pengajuan_investasi)
                        ->first();

                    if (!$pengajuan) {
                        $fail('Pengajuan investasi tidak ditemukan.');
                        return;
                    }

                    $sisaDana = $pengajuan->sisa_dana;

                    if ($this->route('id')) {
                        $penyaluranLama = \App\Models\PenyaluranDeposito::find($this->route('id'));
                        
                        if ($penyaluranLama && $penyaluranLama->id_pengajuan_investasi == $this->id_pengajuan_investasi) {
                            $sisaDana += $penyaluranLama->nominal_yang_disalurkan;
                        }
                    }

                    if ($value > $sisaDana) {
                        $fail('Nominal tidak boleh lebih dari sisa dana yang tersedia (Rp ' . number_format($sisaDana, 0, ',', '.') . ')');
                    }
                },
            ],
            'tanggal_pengiriman_dana' => 'required|date',
            'tanggal_pengembalian' => 'required|date|after:tanggal_pengiriman_dana',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'id_pengajuan_investasi.required' => 'No kontrak harus diisi.',
            'id_pengajuan_investasi.exists' => 'No kontrak tidak valid.',
            'id_debitur.required' => 'Nama perusahaan harus diisi.',
            'id_debitur.exists' => 'Nama perusahaan tidak valid.',
            'nominal_yang_disalurkan.required' => 'Nominal yang disalurkan harus diisi.',
            'nominal_yang_disalurkan.numeric' => 'Nominal yang disalurkan harus berupa angka.',
            'nominal_yang_disalurkan.min' => 'Nominal yang disalurkan minimal Rp 1.',
            'tanggal_pengiriman_dana.required' => 'Tanggal pengiriman dana harus diisi.',
            'tanggal_pengiriman_dana.date' => 'Tanggal pengiriman dana tidak valid.',
            'tanggal_pengembalian.required' => 'Tanggal pengembalian harus diisi.',
            'tanggal_pengembalian.date' => 'Tanggal pengembalian tidak valid.',
            'tanggal_pengembalian.after' => 'Tanggal pengembalian harus setelah tanggal pengiriman dana.',
            'bukti_pengembalian.required' => 'File bukti pengembalian harus diupload.',
            'bukti_pengembalian.file' => 'Bukti pengembalian harus berupa file.',
            'bukti_pengembalian.mimes' => 'Bukti pengembalian harus berupa file PDF, JPG, JPEG, atau PNG.',
            'bukti_pengembalian.max' => 'Bukti pengembalian tidak boleh lebih besar dari 5MB.',
        ];
    }
}
