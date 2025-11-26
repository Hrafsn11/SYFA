<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengajuanRestrukturisasiRequest extends FormRequest
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
        $validate = [
            // Step 1: Identitas Debitur
            'id_debitur' => 'required|exists:master_debitur_dan_investor,id_debitur',
            'id_pengajuan_peminjaman' => 'required|exists:pengajuan_peminjaman,id_pengajuan_peminjaman',
            'nama_perusahaan' => 'required|string|max:255',
            'npwp' => 'nullable|string|max:20',
            'alamat_kantor' => 'nullable|string|max:500',
            'nomor_telepon' => 'nullable|string|max:20',
            'nama_pic' => 'required|string|max:255',
            'jabatan_pic' => 'required|string|max:255',
            
            // Step 2: Data Pembiayaan
            'nomor_kontrak_pembiayaan' => 'required|string|max:255',
            'tanggal_akad' => 'required|date',
            'jenis_pembiayaan' => 'required|string|max:100',
            'jumlah_plafon_awal' => 'nullable|numeric|min:0',
            'sisa_pokok_belum_dibayar' => 'nullable|numeric|min:0',
            'tunggakan_pokok' => 'nullable|numeric|min:0',
            'tunggakan_margin_bunga' => 'nullable|numeric|min:0',
            'jatuh_tempo_terakhir' => 'nullable|date',
            'status_dpd' => 'nullable|string|max:100',
            'alasan_restrukturisasi' => 'required|string',
            
            // Step 3: Permohonan Restrukturisasi
            'jenis_restrukturisasi' => 'required|array|min:1',
            'jenis_restrukturisasi.*' => 'string',
            'jenis_restrukturisasi_lainnya' => 'nullable|string|max:255',
            'rencana_pemulihan_usaha' => 'required|string',
            
            // Step 4: Dokumen Pendukung
            'dokumen_ktp_pic' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'dokumen_npwp_perusahaan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'dokumen_laporan_keuangan' => 'nullable|file|mimes:pdf,xlsx,xls|max:5120',
            'dokumen_arus_kas' => 'nullable|file|mimes:pdf,xlsx,xls|max:5120',
            'dokumen_kondisi_eksternal' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'dokumen_kontrak_pembiayaan' => 'nullable|file|mimes:pdf|max:5120',
            'dokumen_lainnya' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:5120',
            'dokumen_tanda_tangan' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'tempat' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
        ];

        if ($this->id) {
            $validate['dokumen_ktp_pic'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $validate['dokumen_npwp_perusahaan'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $validate['dokumen_laporan_keuangan'] = 'nullable|file|mimes:pdf,xlsx,xls|max:5120';
            $validate['dokumen_arus_kas'] = 'nullable|file|mimes:pdf,xlsx,xls|max:5120';
        }

        return $validate;
    }

    public function messages()
    {
        return [
            // Step 1
            'id_debitur.required' => 'Debitur harus dipilih.',
            'id_debitur.exists' => 'Debitur tidak valid.',
            'id_pengajuan_peminjaman.required' => 'Pengajuan peminjaman harus dipilih.',
            'id_pengajuan_peminjaman.exists' => 'Pengajuan peminjaman tidak valid.',
            'nama_perusahaan.required' => 'Nama perusahaan harus diisi.',
            'nama_pic.required' => 'Nama PIC harus diisi.',
            'jabatan_pic.required' => 'Jabatan PIC harus diisi.',
            
            // Step 2
            'nomor_kontrak_pembiayaan.required' => 'Nomor kontrak pembiayaan harus dipilih.',
            'nomor_kontrak_pembiayaan.exists' => 'Nomor kontrak pembiayaan tidak valid.',
            'tanggal_akad.required' => 'Tanggal akad harus diisi.',
            'tanggal_akad.date' => 'Format tanggal akad tidak valid.',
            'jenis_pembiayaan.required' => 'Jenis pembiayaan harus dipilih.',
            'sisa_pokok_belum_dibayar.numeric' => 'Sisa pokok harus berupa angka.',
            'sisa_pokok_belum_dibayar.min' => 'Sisa pokok tidak boleh kurang dari 0.',
            'alasan_restrukturisasi.required' => 'Alasan restrukturisasi harus diisi.',
            
            // Step 3
            'jenis_restrukturisasi.required' => 'Jenis restrukturisasi harus dipilih minimal 1.',
            'jenis_restrukturisasi.array' => 'Jenis restrukturisasi harus berupa array.',
            'jenis_restrukturisasi.min' => 'Pilih minimal 1 jenis restrukturisasi.',
            'rencana_pemulihan_usaha.required' => 'Rencana pemulihan usaha harus diisi.',
            
            // Step 4
            'dokumen_ktp_pic.file' => 'Dokumen KTP PIC harus berupa file.',
            'dokumen_ktp_pic.mimes' => 'Dokumen KTP PIC harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen_ktp_pic.max' => 'Ukuran dokumen KTP PIC maksimal 2MB.',
            'dokumen_laporan_keuangan.mimes' => 'Laporan keuangan harus berformat PDF, XLSX, atau XLS.',
            'dokumen_laporan_keuangan.max' => 'Ukuran laporan keuangan maksimal 5MB.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ];
    }
}
