<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramRestrukturisasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pengajuan_restrukturisasi' => 'required|exists:pengajuan_restrukturisasi,id_pengajuan_restrukturisasi',
            'metode_perhitungan' => 'required|in:Flat,Efektif (Anuitas)',
            'plafon_pembiayaan' => 'required|numeric|min:0',
            'suku_bunga_per_tahun' => 'required|numeric|min:0|max:100',
            'jangka_waktu_total' => 'required|integer|min:1|max:360',
            'masa_tenggang' => 'required|integer|min:0|max:36',
            'tanggal_mulai_cicilan' => 'required|date|after_or_equal:today',

            'jadwal_angsuran' => 'required|array|min:1',
            'jadwal_angsuran.*.no' => 'required|integer|min:1',
            'jadwal_angsuran.*.tanggal_jatuh_tempo' => 'required|string',
            'jadwal_angsuran.*.tanggal_jatuh_tempo_raw' => 'required|date',
            'jadwal_angsuran.*.pokok' => 'required|numeric|min:0',
            'jadwal_angsuran.*.margin' => 'required|numeric|min:0',
            'jadwal_angsuran.*.total_cicilan' => 'required|numeric|min:0',
            'jadwal_angsuran.*.catatan' => 'nullable|string|max:500',
            'jadwal_angsuran.*.is_grace_period' => 'required|boolean',
            'jadwal_angsuran.*.status' => 'nullable|in:Belum Jatuh Tempo,Jatuh Tempo,Lunas',
            'jadwal_angsuran.*.bukti_pembayaran' => 'nullable|string',
            'jadwal_angsuran.*.tanggal_bayar' => 'nullable|date',
            'jadwal_angsuran.*.nominal_bayar' => 'nullable|numeric|min:0',

            'total_pokok' => 'required|numeric|min:0',
            'total_margin' => 'required|numeric|min:0',
            'total_cicilan' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_pengajuan_restrukturisasi.required' => 'Silakan pilih pengajuan restrukturisasi terlebih dahulu.',
            'id_pengajuan_restrukturisasi.exists' => 'Pengajuan restrukturisasi tidak valid.',

            'metode_perhitungan.required' => 'Metode perhitungan harus dipilih.',
            'metode_perhitungan.in' => 'Metode perhitungan harus Flat atau Efektif (Anuitas).',

            'plafon_pembiayaan.required' => 'Plafon pembiayaan harus diisi.',
            'plafon_pembiayaan.numeric' => 'Plafon pembiayaan harus berupa angka.',
            'plafon_pembiayaan.min' => 'Plafon pembiayaan tidak boleh kurang dari 0.',

            'suku_bunga_per_tahun.required' => 'Suku bunga per tahun harus diisi.',
            'suku_bunga_per_tahun.numeric' => 'Suku bunga harus berupa angka.',
            'suku_bunga_per_tahun.min' => 'Suku bunga tidak boleh kurang dari 0%.',
            'suku_bunga_per_tahun.max' => 'Suku bunga tidak boleh lebih dari 100%.',

            'jangka_waktu_total.required' => 'Jangka waktu total harus diisi.',
            'jangka_waktu_total.integer' => 'Jangka waktu harus berupa bilangan bulat.',
            'jangka_waktu_total.min' => 'Jangka waktu minimal 1 bulan.',
            'jangka_waktu_total.max' => 'Jangka waktu maksimal 360 bulan (30 tahun).',

            'masa_tenggang.required' => 'Masa tenggang harus diisi.',
            'masa_tenggang.integer' => 'Masa tenggang harus berupa bilangan bulat.',
            'masa_tenggang.min' => 'Masa tenggang minimal 0 bulan.',
            'masa_tenggang.max' => 'Masa tenggang maksimal 36 bulan (3 tahun).',

            'tanggal_mulai_cicilan.required' => 'Tanggal mulai cicilan harus diisi.',
            'tanggal_mulai_cicilan.date' => 'Format tanggal mulai cicilan tidak valid.',
            'tanggal_mulai_cicilan.after_or_equal' => 'Tanggal mulai cicilan tidak boleh kurang dari hari ini.',

            'jadwal_angsuran.required' => 'Mohon hitung jadwal angsuran sebelum menyimpan.',
            'jadwal_angsuran.array' => 'Format jadwal angsuran tidak valid.',
            'jadwal_angsuran.min' => 'Jadwal angsuran minimal 1 bulan.',

            'total_pokok.required' => 'Total pokok harus dihitung.',
            'total_margin.required' => 'Total margin harus dihitung.',
            'total_cicilan.required' => 'Total cicilan harus dihitung.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_pengajuan_restrukturisasi' => 'pengajuan restrukturisasi',
            'metode_perhitungan' => 'metode perhitungan',
            'plafon_pembiayaan' => 'plafon pembiayaan',
            'suku_bunga_per_tahun' => 'suku bunga per tahun',
            'jangka_waktu_total' => 'jangka waktu total',
            'masa_tenggang' => 'masa tenggang',
            'tanggal_mulai_cicilan' => 'tanggal mulai cicilan',
            'jadwal_angsuran' => 'jadwal angsuran',
            'total_pokok' => 'total pokok',
            'total_margin' => 'total margin',
            'total_cicilan' => 'total cicilan',
        ];
    }

    protected function passedValidation(): void
    {
        $masaTenggang = $this->input('masa_tenggang');
        $jangkaWaktu = $this->input('jangka_waktu_total');

        if ($masaTenggang >= $jangkaWaktu) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'masa_tenggang' => 'Masa tenggang tidak boleh lebih dari atau sama dengan jangka waktu total.',
            ]);
        }

        $jadwalAngsuran = $this->input('jadwal_angsuran', []);
        $totalPokok = $this->input('total_pokok', 0);
        $totalMargin = $this->input('total_margin', 0);

        $sumPokok = array_sum(array_column($jadwalAngsuran, 'pokok'));
        $sumMargin = array_sum(array_column($jadwalAngsuran, 'margin'));

        $tolerance = 1;

        if (abs($sumPokok - $totalPokok) > $tolerance) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'total_pokok' => 'Total pokok tidak sesuai dengan jumlah angsuran. Silakan hitung ulang.',
            ]);
        }

        if (abs($sumMargin - $totalMargin) > $tolerance) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'total_margin' => 'Total margin tidak sesuai dengan jumlah angsuran. Silakan hitung ulang.',
            ]);
        }
    }
}
