<?php

namespace App\Http\Requests\SFinlog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PeminjamanRequest extends FormRequest
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
            'id_debitur' => ['required', 'exists:master_debitur_dan_investor,id_debitur'],
            'id_cells_project' => ['nullable', 'exists:cells_projects,id_cells_project'],
            'nama_project' => ['required', 'string', 'max:255'],
            'durasi_project' => ['required', 'integer', 'min:0'],
            'durasi_project_hari' => ['required', 'integer', 'min:0'],
            'nib_perusahaan' => ['nullable', 'string', 'max:255'],
            
            'nilai_pinjaman' => ['required', 'numeric', 'min:0'],
            'presentase_bagi_hasil' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_bagi_hasil' => ['nullable', 'numeric', 'min:0'],
            'total_pinjaman' => ['nullable', 'numeric', 'min:0'],
            
            'harapan_tanggal_pencairan' => ['required', 'date'],
            'top' => ['required', 'integer', 'min:1'],
            'rencana_tgl_pengembalian' => ['nullable', 'date'],
            
            // File uploads - accept both file objects and string paths
            'dokumen_mitra' => ['nullable', function ($attribute, $value, $fail) {
                if (is_string($value)) return;
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    $fail('Dokumen mitra harus berupa file.');
                    return;
                }
                if (!in_array($value->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $fail('Dokumen mitra harus berformat PDF, JPG, JPEG, atau PNG.');
                }
                if ($value->getSize() > 2048 * 1024) {
                    $fail('Ukuran dokumen mitra maksimal 2MB.');
                }
            }],
            'form_new_customer' => ['required', function ($attribute, $value, $fail) {
                if (is_string($value)) return; 
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    $fail('Form new customer harus diupload.');
                    return;
                }
                if (!in_array($value->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $fail('Form new customer harus berformat PDF, JPG, JPEG, atau PNG.');
                }
                if ($value->getSize() > 2048 * 1024) {
                    $fail('Ukuran form new customer maksimal 2MB.');
                }
            }],
            'dokumen_kerja_sama' => ['required', function ($attribute, $value, $fail) {
                if (is_string($value)) return; // Already a stored path from Livewire
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    $fail('Dokumen kerja sama harus diupload.');
                    return;
                }
                if (!in_array($value->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $fail('Dokumen kerja sama harus berformat PDF, JPG, JPEG, atau PNG.');
                }
                if ($value->getSize() > 2048 * 1024) {
                    $fail('Ukuran dokumen kerja sama maksimal 2MB.');
                }
            }],
            'dokumen_npa' => ['required', function ($attribute, $value, $fail) {
                if (is_string($value)) return; // Already a stored path from Livewire
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    $fail('Dokumen NPA harus diupload.');
                    return;
                }
                if (!in_array($value->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $fail('Dokumen NPA harus berformat PDF, JPG, JPEG, atau PNG.');
                }
                if ($value->getSize() > 2048 * 1024) {
                    $fail('Ukuran dokumen NPA maksimal 2MB.');
                }
            }],
            'akta_perusahaan' => ['nullable', function ($attribute, $value, $fail) {
                if (is_string($value)) return; // Already a stored path from Livewire
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    $fail('Akta perusahaan harus diupload.');
                    return;
                }
                if (!in_array($value->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $fail('Akta perusahaan harus berformat PDF, JPG, JPEG, atau PNG.');
                }
                if ($value->getSize() > 2048 * 1024) {
                    $fail('Ukuran akta perusahaan maksimal 2MB.');
                }
            }],
            'ktp_owner' => ['nullable', function ($attribute, $value, $fail) {
                if (is_string($value)) return; // Already a stored path from Livewire
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    $fail('KTP owner harus diupload.');
                    return;
                }
                if (!in_array($value->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $fail('KTP owner harus berformat PDF, JPG, JPEG, atau PNG.');
                }
                if ($value->getSize() > 2048 * 1024) {
                    $fail('Ukuran KTP owner maksimal 2MB.');
                }
            }],
            'ktp_pic' => ['required', function ($attribute, $value, $fail) {
                if (is_string($value)) return; // Already a stored path from Livewire
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    $fail('KTP PIC harus diupload.');
                    return;
                }
                if (!in_array($value->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $fail('KTP PIC harus berformat PDF, JPG, JPEG, atau PNG.');
                }
                if ($value->getSize() > 2048 * 1024) {
                    $fail('Ukuran KTP PIC maksimal 2MB.');
                }
            }],
            'surat_izin_usaha' => ['nullable', function ($attribute, $value, $fail) {
                if (is_string($value)) return; // Already a stored path from Livewire
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    $fail('Surat izin usaha harus berupa file.');
                    return;
                }
                if (!in_array($value->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $fail('Surat izin usaha harus berformat PDF, JPG, JPEG, atau PNG.');
                }
                if ($value->getSize() > 2048 * 1024) {
                    $fail('Ukuran surat izin usaha maksimal 2MB.');
                }
            }],
            
            'catatan' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'id_debitur.required' => 'Debitur harus dipilih.',
            'id_debitur.exists' => 'Debitur tidak valid.',
            'id_cells_project.exists' => 'Project tidak valid.',
            
            'nama_project.required' => 'Nama project harus diisi.',
            'nama_project.string' => 'Nama project harus berupa teks.',
            'nama_project.max' => 'Nama project maksimal 255 karakter.',
            
            'durasi_project.required' => 'Durasi project harus diisi.',
            'durasi_project.integer' => 'Durasi project harus berupa angka bulat.',
            'durasi_project.min' => 'Durasi project minimal 0 bulan.',
            
            'durasi_project_hari.required' => 'Durasi project (hari) harus diisi.',
            'durasi_project_hari.integer' => 'Durasi project (hari) harus berupa angka bulat.',
            'durasi_project_hari.min' => 'Durasi project (hari) minimal 0 hari.',
            
            'nib_perusahaan.string' => 'NIB perusahaan harus berupa teks.',
            'nib_perusahaan.max' => 'NIB perusahaan maksimal 255 karakter.',
            
            'nilai_pinjaman.required' => 'Nilai pinjaman harus diisi.',
            'nilai_pinjaman.numeric' => 'Nilai pinjaman harus berupa angka.',
            'nilai_pinjaman.min' => 'Nilai pinjaman minimal 0.',
            
            'presentase_bagi_hasil.required' => 'Persentase bagi hasil harus diisi.',
            'presentase_bagi_hasil.numeric' => 'Persentase bagi hasil harus berupa angka.',
            'presentase_bagi_hasil.min' => 'Persentase bagi hasil minimal 0%.',
            'presentase_bagi_hasil.max' => 'Persentase bagi hasil maksimal 100%.',
            
            'nilai_bagi_hasil.numeric' => 'Nilai bagi hasil harus berupa angka.',
            'total_pinjaman.numeric' => 'Total pinjaman harus berupa angka.',
            
            'harapan_tanggal_pencairan.required' => 'Harapan tanggal pencairan harus diisi.',
            'harapan_tanggal_pencairan.date' => 'Harapan tanggal pencairan harus berupa tanggal yang valid.',
            
            'top.required' => 'TOP (Term of Payment) harus diisi.',
            'top.integer' => 'TOP harus berupa angka bulat.',
            'top.min' => 'TOP minimal 1 hari.',
            
            'rencana_tgl_pengembalian.date' => 'Rencana tanggal pengembalian harus berupa tanggal yang valid.',
            
            'dokumen_mitra.file' => 'Dokumen mitra harus berupa file.',
            'dokumen_mitra.mimes' => 'Dokumen mitra harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen_mitra.max' => 'Ukuran dokumen mitra maksimal 2MB.',
            
            'form_new_customer.required' => 'Form new customer harus diupload.',
            'form_new_customer.file' => 'Form new customer harus berupa file.',
            'form_new_customer.mimes' => 'Form new customer harus berformat PDF, JPG, JPEG, atau PNG.',
            'form_new_customer.max' => 'Ukuran form new customer maksimal 2MB.',
            
            'dokumen_kerja_sama.required' => 'Dokumen kerja sama harus diupload.',
            'dokumen_kerja_sama.file' => 'Dokumen kerja sama harus berupa file.',
            'dokumen_kerja_sama.mimes' => 'Dokumen kerja sama harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen_kerja_sama.max' => 'Ukuran dokumen kerja sama maksimal 2MB.',
            
            'dokumen_npa.required' => 'Dokumen NPA harus diupload.',
            'dokumen_npa.file' => 'Dokumen NPA harus berupa file.',
            'dokumen_npa.mimes' => 'Dokumen NPA harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen_npa.max' => 'Ukuran dokumen NPA maksimal 2MB.',
            
            'akta_perusahaan.file' => 'Akta perusahaan harus berupa file.',
            'akta_perusahaan.mimes' => 'Akta perusahaan harus berformat PDF, JPG, JPEG, atau PNG.',
            'akta_perusahaan.max' => 'Ukuran akta perusahaan maksimal 2MB.',
            
            'ktp_owner.file' => 'KTP owner harus berupa file.',
            'ktp_owner.mimes' => 'KTP owner harus berformat PDF, JPG, JPEG, atau PNG.',
            'ktp_owner.max' => 'Ukuran KTP owner maksimal 2MB.',
            
            'ktp_pic.required' => 'KTP PIC harus diupload.',
            'ktp_pic.file' => 'KTP PIC harus berupa file.',
            'ktp_pic.mimes' => 'KTP PIC harus berformat PDF, JPG, JPEG, atau PNG.',
            'ktp_pic.max' => 'Ukuran KTP PIC maksimal 2MB.',
            
            'surat_izin_usaha.file' => 'Surat izin usaha harus berupa file.',
            'surat_izin_usaha.mimes' => 'Surat izin usaha harus berformat PDF, JPG, JPEG, atau PNG.',
            'surat_izin_usaha.max' => 'Ukuran surat izin usaha maksimal 2MB.',
            
            'catatan.string' => 'Catatan harus berupa teks.',
        ];
    }
}
