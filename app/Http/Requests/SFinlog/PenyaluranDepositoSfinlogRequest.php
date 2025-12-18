<?php

namespace App\Http\Requests\SFinlog;

use App\Models\PengajuanInvestasiFinlog;
use Illuminate\Foundation\Http\FormRequest;

class PenyaluranDepositoSfinlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('sfinlog.penyaluran-deposito-sfinlog.upload-bukti')) {
            return [
                'bukti_pengembalian' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ];
        }

        // Rules untuk store/update
        $rules = [
            'id_pengajuan_investasi_finlog' => 'required|exists:pengajuan_investasi_finlog,id_pengajuan_investasi_finlog',
            'id_cells_project' => 'required|exists:cells_projects,id_cells_project',
            'id_project' => 'required|exists:projects,id_project',
            'nominal_yang_disalurkan' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (!$this->id_pengajuan_investasi_finlog) {
                        return;
                    }

                    $pengajuan = PengajuanInvestasiFinlog::find($this->id_pengajuan_investasi_finlog);

                    if (!$pengajuan) {
                        $fail('Pengajuan investasi tidak ditemukan.');
                        return;
                    }

                    // Hitung total yang sudah disalurkan
                    $totalDisalurkan = \App\Models\PenyaluranDepositoSfinlog::where('id_pengajuan_investasi_finlog', $this->id_pengajuan_investasi_finlog)
                        ->sum('nominal_yang_disalurkan');

                    $sisaDana = $pengajuan->nominal_investasi - $totalDisalurkan;

                    // Jika update, tambahkan kembali nominal lama
                    if ($this->route('id')) {
                        $penyaluranLama = \App\Models\PenyaluranDepositoSfinlog::find($this->route('id'));
                        
                        if ($penyaluranLama && $penyaluranLama->id_pengajuan_investasi_finlog == $this->id_pengajuan_investasi_finlog) {
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
            'id_pengajuan_investasi_finlog.required' => 'No kontrak harus diisi.',
            'id_pengajuan_investasi_finlog.exists' => 'No kontrak tidak valid.',
            'id_cells_project.required' => 'Cell bisnis harus diisi.',
            'id_cells_project.exists' => 'Cell bisnis tidak valid.',
            'id_project.required' => 'Project harus diisi.',
            'id_project.exists' => 'Project tidak valid.',
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

