<?php

namespace App\Http\Requests;

use App\Models\PengajuanInvestasi;
use App\Models\PengembalianInvestasi;
use Illuminate\Foundation\Http\FormRequest;

class PengembalianInvestasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pengajuan_investasi' => 'required|exists:pengajuan_investasi,id_pengajuan_investasi',
            'dana_pokok_dibayar' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (!$this->id_pengajuan_investasi) {
                        return;
                    }

                    $investasi = PengajuanInvestasi::find($this->id_pengajuan_investasi);
                    if (!$investasi) {
                        $fail('Data investasi tidak ditemukan.');
                        return;
                    }

                  
                    $danaTersedia = $investasi->dana_tersedia; 
                    $sisaDiPerusahaan = $investasi->sisa_dana_di_perusahaan; 

                    // Jika dana tersedia masih ada, field ini wajib diisi
                    if ($danaTersedia > 0 && (is_null($value) || $value === '')) {
                        $fail('Dana Pokok harus diisi karena masih ada dana tersedia Rp ' . number_format($danaTersedia, 0, ',', '.'));
                        return;
                    }

                    if ($value > $danaTersedia) {
                        $errorMsg = 'Dana tidak tersedia! Yang bisa dikembalikan: Rp ' . number_format($danaTersedia, 0, ',', '.');
                        
                        if ($sisaDiPerusahaan > 0) {
                            $errorMsg .= ' (Dana Rp ' . number_format($sisaDiPerusahaan, 0, ',', '.') . ' masih di perusahaan, menunggu pembayaran)';
                        }
                        
                        $fail($errorMsg);
                    }
                },
            ],
            'bunga_dibayar' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (!$this->id_pengajuan_investasi) {
                        return;
                    }

                    $investasi = PengajuanInvestasi::find($this->id_pengajuan_investasi);
                    if (!$investasi) {
                        $fail('Data investasi tidak ditemukan.');
                        return;
                    }

                    // Best Practice: Langsung pakai kolom sisa_bunga (fast & accurate!)
                    $sisaBunga = $investasi->sisa_bunga ?? 0;

                    // Jika sisa bunga masih ada, field ini wajib diisi
                    if ($sisaBunga > 0 && (is_null($value) || $value === '')) {
                        $fail('Bunga harus diisi karena masih ada sisa Rp ' . number_format($sisaBunga, 0, ',', '.'));
                        return;
                    }

                    if ($value > $sisaBunga) {
                        $fail('Bunga tidak boleh lebih dari Sisa Bunga (Rp ' . number_format($sisaBunga, 0, ',', '.') . ')');
                    }
                },
            ],
            'bukti_transfer' => $this->isMethod('PUT') || $this->isMethod('PATCH') 
                ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048' 
                : 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tanggal_pengembalian' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'id_pengajuan_investasi.required' => 'No Kontrak harus dipilih.',
            'id_pengajuan_investasi.exists' => 'No Kontrak tidak valid.',
            'dana_pokok_dibayar.required' => 'Dana Pokok harus diisi.',
            'dana_pokok_dibayar.numeric' => 'Dana Pokok harus berupa angka.',
            'dana_pokok_dibayar.min' => 'Dana Pokok minimal 0.',
            'bunga_dibayar.required' => 'Bunga harus diisi.',
            'bunga_dibayar.numeric' => 'Bunga harus berupa angka.',
            'bunga_dibayar.min' => 'Bunga minimal 0.',
            'bukti_transfer.required' => 'Bukti Transfer harus diupload.',
            'bukti_transfer.file' => 'Bukti Transfer harus berupa file.',
            'bukti_transfer.mimes' => 'Bukti Transfer harus berupa file PDF, JPG, JPEG, atau PNG.',
            'bukti_transfer.max' => 'Bukti Transfer tidak boleh lebih besar dari 2MB.',
            'tanggal_pengembalian.required' => 'Tanggal Pengembalian harus diisi.',
            'tanggal_pengembalian.date' => 'Tanggal Pengembalian tidak valid.',
        ];
    }
}
