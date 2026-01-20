<?php

namespace App\Http\Requests\SFinlog;

use App\Models\PengajuanInvestasiFinlog;
use App\Models\PengembalianInvestasiFinlog;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

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
                    $totalPokokSudahDibayar = $totalSudahDibayar->total_pokok ?? 0;

                    $sisaBagiHasil = max(0, $totalBagiHasil - $totalBagiHasilSudahDibayar);
                    $sisaPokok = max(0, ($pengajuan->nominal_investasi ?? 0) - $totalPokokSudahDibayar);

                    // Cek apakah sudah bulan terakhir
                    $tanggalInvestasi = Carbon::parse($pengajuan->tanggal_investasi)->startOfDay();
                    $tanggalSekarang = Carbon::now()->startOfDay();

                    // Hitung bulan berjalan dengan lebih akurat
                    $tahunInvestasi = $tanggalInvestasi->year;
                    $bulanInvestasi = $tanggalInvestasi->month;
                    $tahunSekarang = $tanggalSekarang->year;
                    $bulanSekarang = $tanggalSekarang->month;

                    $selisihTahun = $tahunSekarang - $tahunInvestasi;
                    $selisihBulan = $bulanSekarang - $bulanInvestasi;
                    $totalBulan = ($selisihTahun * 12) + $selisihBulan;
                    $bulanBerjalan = max(1, $totalBulan + 1);

                    if ($bulanBerjalan > $pengajuan->lama_investasi) {
                        $bulanBerjalan = $pengajuan->lama_investasi;
                    }
                    $isBulanTerakhir = ($bulanBerjalan >= $pengajuan->lama_investasi);

                    // 1) Pokok hanya bisa dibayar di bulan terakhir
                    if (!$isBulanTerakhir) {
                        $fail('Dana pokok hanya dapat dibayarkan di bulan terakhir investasi (bulan ke-' . $pengajuan->lama_investasi . '). Saat ini bulan ke-' . $bulanBerjalan . '.');
                        return;
                    }

                    // 2) Pokok tidak boleh dibayar sebelum bagi hasil lunas
                    if ($sisaBagiHasil > 0) {
                        $fail('Dana pokok tidak dapat dibayarkan sebelum Bagi Hasil lunas.');
                        return;
                    }

                    // 3) Jika pokok sudah lunas, tidak boleh ada pembayaran lagi
                    if ($sisaPokok <= 0) {
                        $fail('Dana pokok sudah lunas, tidak dapat dibayarkan lagi.');
                        return;
                    }

                    // 4) Hitung dana yang masih di perusahaan (dari penyaluran deposito)
                    $penyaluran = \DB::table('penyaluran_deposito_sfinlog')
                        ->where('id_pengajuan_investasi_finlog', $pengajuan->id_pengajuan_investasi_finlog)
                        ->selectRaw('
                            SUM(nominal_yang_disalurkan) as total_disalurkan,
                            SUM(nominal_yang_dikembalikan) as total_dikembalikan
                        ')
                        ->first();

                    $totalDanaDisalurkan = floatval($penyaluran->total_disalurkan ?? 0);
                    $totalDanaDikembalikan = floatval($penyaluran->total_dikembalikan ?? 0);
                    $sisaDanaDiPerusahaan = $totalDanaDisalurkan - $totalDanaDikembalikan;

                    // Dana pokok yang tersedia untuk dikembalikan ke investor
                    // = Sisa Pokok - Sisa Dana di Perusahaan
                    $danaPokokTersedia = max(0, $sisaPokok - $sisaDanaDiPerusahaan);

                    // 5) Nominal yang dibayar tidak boleh melebihi dana pokok tersedia
                    if ($value > $danaPokokTersedia) {
                        if ($sisaDanaDiPerusahaan > 0) {
                            $fail('Dana pokok yang dapat dikembalikan maksimal Rp ' . number_format($danaPokokTersedia, 0, ',', '.') . '. Masih ada Rp ' . number_format($sisaDanaDiPerusahaan, 0, ',', '.') . ' yang belum dikembalikan dari perusahaan.');
                        } else {
                            $fail('Dana pokok tidak boleh lebih dari sisa pokok (Rp ' . number_format($sisaPokok, 0, ',', '.') . ').');
                        }
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

                    // Cek periode penagihan (harus 2 bulan sekali atau bulan terakhir)
                    $tanggalInvestasi = Carbon::parse($pengajuan->tanggal_investasi)->startOfDay();
                    $tanggalSekarang = Carbon::now()->startOfDay();

                    // Hitung bulan berjalan dengan lebih akurat
                    $tahunInvestasi = $tanggalInvestasi->year;
                    $bulanInvestasi = $tanggalInvestasi->month;
                    $tahunSekarang = $tanggalSekarang->year;
                    $bulanSekarang = $tanggalSekarang->month;

                    $selisihTahun = $tahunSekarang - $tahunInvestasi;
                    $selisihBulan = $bulanSekarang - $bulanInvestasi;
                    $totalBulan = ($selisihTahun * 12) + $selisihBulan;
                    $bulanBerjalan = max(1, $totalBulan + 1);

                    if ($bulanBerjalan > $pengajuan->lama_investasi) {
                        $bulanBerjalan = $pengajuan->lama_investasi;
                    }

                    $isBulanTerakhir = ($bulanBerjalan >= $pengajuan->lama_investasi);
                    $bulanGenap = ($bulanBerjalan % 2 == 0);

                    // Cek apakah sudah 2 bulan sejak pengembalian terakhir
                    $pengembalianTerakhir = PengembalianInvestasiFinlog::where('id_pengajuan_investasi_finlog', $pengajuan->id_pengajuan_investasi_finlog)
                        ->orderBy('tanggal_pengembalian', 'desc')
                        ->first();

                    $bisaBayarBerdasarkanPeriode = true;
                    if ($pengembalianTerakhir) {
                        $tanggalTerakhir = Carbon::parse($pengembalianTerakhir->tanggal_pengembalian)->startOfDay();

                        // Hitung selisih bulan dengan lebih akurat
                        $tahunTerakhir = $tanggalTerakhir->year;
                        $bulanTerakhir = $tanggalTerakhir->month;

                        $selisihTahun = $tahunSekarang - $tahunTerakhir;
                        $selisihBulan = $bulanSekarang - $bulanTerakhir;
                        $totalBulan = ($selisihTahun * 12) + $selisihBulan;

                        $bisaBayarBerdasarkanPeriode = ($totalBulan >= 2);
                    } else {
                        // Jika belum ada pengembalian, harus minimal bulan ke-2
                        $bisaBayarBerdasarkanPeriode = ($bulanBerjalan >= 2);
                    }

                    // Validasi periode: Bagi hasil hanya bisa dibayar di bulan genap (2, 4, 6, dst) atau bulan terakhir
                    if (!$isBulanTerakhir && (!$bulanGenap || !$bisaBayarBerdasarkanPeriode)) {
                        if (!$bulanGenap) {
                            $fail('Bagi Hasil hanya dapat dibayarkan di bulan genap (bulan ke-2, 4, 6, dst) atau bulan terakhir. Saat ini bulan ke-' . $bulanBerjalan . '.');
                        } else {
                            $tanggalBerikutnya = $pengembalianTerakhir
                                ? Carbon::parse($pengembalianTerakhir->tanggal_pengembalian)->addMonths(2)->format('d F Y')
                                : Carbon::parse($pengajuan->tanggal_investasi)->addMonths(2)->format('d F Y');
                            $fail('Pembayaran berikutnya dapat dilakukan pada: ' . $tanggalBerikutnya);
                        }
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


