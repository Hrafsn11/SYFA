<?php

namespace App\Http\Requests\SFinlog;

use App\Models\PengajuanInvestasiFinlog;
use App\Models\PengembalianInvestasiFinlog;
use Illuminate\Foundation\Http\FormRequest;

class PengembalianInvestasiFinlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pengajuan_investasi_finlog' => 'required|exists:pengajuan_investasi_finlog,id_pengajuan_investasi_finlog',
            'dana_pokok_dibayar' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (!$this->id_pengajuan_investasi_finlog) {
                        return;
                    }

                    // Jika kosong atau 0, tidak ada pembayaran pokok
                    if ($value === null || $value === '' || $value <= 0) {
                        return;
                    }

                    $pengajuan = PengajuanInvestasiFinlog::find($this->id_pengajuan_investasi_finlog);
                    if (!$pengajuan) {
                        $fail('Data investasi tidak ditemukan.');
                        return;
                    }

                    $totalBagiHasil = $pengajuan->nominal_bagi_hasil_yang_didapat ?? 0;

                    $totalSudahDibayar = PengembalianInvestasiFinlog::getTotalDikembalikan(
                        $pengajuan->id_pengajuan_investasi_finlog
                    );

                    $totalBagiHasilSudahDibayar = $totalSudahDibayar->total_bagi_hasil ?? 0;
                    $totalPokokSudahDibayar     = $totalSudahDibayar->total_pokok ?? 0;

                    $sisaBagiHasil = max(0, $totalBagiHasil - $totalBagiHasilSudahDibayar);
                    $sisaPokok     = max(0, ($pengajuan->nominal_investasi ?? 0) - $totalPokokSudahDibayar);

                    // 1) Pokok tidak boleh dibayar sebelum bagi hasil lunas
                    if ($sisaBagiHasil > 0) {
                        $fail('Dana pokok tidak dapat dibayarkan sebelum Bagi Hasil lunas.');
                        return;
                    }

                    // 2) Jika pokok sudah lunas, tidak boleh ada pembayaran lagi
                    if ($sisaPokok <= 0) {
                        $fail('Dana pokok sudah lunas, tidak dapat dibayarkan lagi.');
                        return;
                    }

                    // 3) Nominal yang dibayar tidak boleh melebihi sisa pokok
                    if ($value > $sisaPokok) {
                        $fail('Dana pokok tidak boleh lebih dari sisa pokok (Rp ' . number_format($sisaPokok, 0, ',', '.') . ').');
                    }
                },
            ],
            'bagi_hasil_dibayar' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (!$this->id_pengajuan_investasi_finlog) {
                        return;
                    }

                    $pengajuan = PengajuanInvestasiFinlog::find($this->id_pengajuan_investasi_finlog);
                    if (!$pengajuan) {
                        $fail('Data investasi tidak ditemukan.');
                        return;
                    }

                    $totalBagiHasil = $pengajuan->nominal_bagi_hasil_yang_didapat ?? 0;

                    $totalSudahDibayar = PengembalianInvestasiFinlog::getTotalDikembalikan(
                        $pengajuan->id_pengajuan_investasi_finlog
                    )->total_bagi_hasil ?? 0;
                    $sisaBagiHasil = max(0, $totalBagiHasil - $totalSudahDibayar);

                    // Jika masih ada sisa bagi hasil, nilai wajib diisi (tidak boleh kosong / null)
                    if ($sisaBagiHasil > 0 && ($value === null || $value === '')) {
                        $fail('Bagi Hasil harus diisi karena masih ada sisa Bagi Hasil yang belum dibayar.');
                        return;
                    }

                    // Jika diisi, tidak boleh melebihi sisa
                    if ($value !== null && $value > $sisaBagiHasil) {
                        $fail('Bagi Hasil tidak boleh lebih dari sisa Bagi Hasil (Rp ' . number_format($sisaBagiHasil, 0, ',', '.') . ').');
                    }
                },
            ],
            'bukti_transfer' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tanggal_pengembalian' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'id_pengajuan_investasi_finlog.required' => 'No Kontrak harus dipilih.',
            'id_pengajuan_investasi_finlog.exists' => 'No Kontrak tidak valid.',

            'dana_pokok_dibayar.numeric' => 'Dana Pokok harus berupa angka.',
            'dana_pokok_dibayar.min' => 'Dana Pokok minimal 0.',

            'bagi_hasil_dibayar.numeric' => 'Bagi Hasil harus berupa angka.',
            'bagi_hasil_dibayar.min' => 'Bagi Hasil minimal 0.',

            'bukti_transfer.required' => 'Bukti Transfer harus diupload.',
            'bukti_transfer.file' => 'Bukti Transfer harus berupa file.',
            'bukti_transfer.mimes' => 'Bukti Transfer harus berupa file PDF, JPG, JPEG, atau PNG.',
            'bukti_transfer.max' => 'Bukti Transfer tidak boleh lebih besar dari 2MB.',

            'tanggal_pengembalian.required' => 'Tanggal Pengembalian harus diisi.',
            'tanggal_pengembalian.date' => 'Tanggal Pengembalian tidak valid.',
        ];
    }
}


