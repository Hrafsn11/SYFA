<?php

namespace App\Livewire\SFinlog;

use App\Attributes\FieldInput;
use App\Attributes\ParameterIDRoute;
use App\Http\Requests\SFinlog\PengembalianInvestasiFinlogRequest;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Livewire\Traits\HasValidate;
use App\Models\PengajuanInvestasiFinlog;
use App\Models\PengembalianInvestasiFinlog as ModelPengembalianInvestasiFinlog;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class PengembalianInvestasiFinlog extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads;

    private string $validateClass = PengembalianInvestasiFinlogRequest::class;

    #[ParameterIDRoute]
    public $id;

    #[FieldInput]
    public $id_pengajuan_investasi_finlog, $dana_pokok_dibayar, $bagi_hasil_dibayar, $bukti_transfer, $tanggal_pengembalian;

    public $nominal_investasi;
    public $lama_investasi;
    public $bagi_hasil_total;
    public $tanggal_investasi;
    public $total_pokok_dikembalikan = 0;
    public $total_bagi_hasil_dikembalikan = 0;
    public $jumlah_transaksi = 0;
    public $bulan_berjalan = 0;
    public $is_bulan_terakhir = false;
    public $tanggal_pengembalian_terakhir = null;
    public $bisa_bayar_bagi_hasil = false;
    public $bisa_bayar_pokok = false;
    public $info_periode = '';

    public $total_dana_disalurkan = 0;
    public $total_dana_dikembalikan_penyaluran = 0;
    public $sisa_dana_di_perusahaan = 0;
    public $dana_pokok_tersedia = 0;

    public $sisa_pokok_db = 0;
    public $sisa_bagi_hasil_db = 0;

    public function mount()
    {
        $this->setUrlSaveData(
            'store_pengembalian_investasi_finlog',
            'sfinlog.pengembalian-investasi.store',
            ["callback" => "afterAction"]
        );

        $this->tanggal_pengembalian = date('Y-m-d');
    }

    public function getPengajuanInvestasiProperty()
    {
        return PengajuanInvestasiFinlog::query()
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->select([
                'id_pengajuan_investasi_finlog',
                'nomor_kontrak',
                'nama_investor',
                'nominal_investasi',
                'lama_investasi',
                'nominal_bagi_hasil_yang_didapat',
                'tanggal_investasi',
            ])
            ->orderBy('tanggal_investasi', 'desc')
            ->get();
    }

    /**
     * Load data kontrak finlog untuk menampilkan info di modal.
     */
    public function loadDataKontrak($idPengajuanInvestasiFinlog)
    {
        try {
            $investasi = PengajuanInvestasiFinlog::select([
                'nominal_investasi',
                'lama_investasi',
                'nominal_bagi_hasil_yang_didapat',
                'tanggal_investasi',
                'sisa_pokok',
                'sisa_bagi_hasil',
            ])->findOrFail($idPengajuanInvestasiFinlog);

            $this->nominal_investasi = $investasi->nominal_investasi;
            $this->lama_investasi = $investasi->lama_investasi;
            $this->bagi_hasil_total = $investasi->nominal_bagi_hasil_yang_didapat;
            $this->tanggal_investasi = $investasi->tanggal_investasi;
            $this->sisa_pokok_db = $investasi->sisa_pokok ?? $investasi->nominal_investasi;
            $this->sisa_bagi_hasil_db = $investasi->sisa_bagi_hasil ?? $investasi->nominal_bagi_hasil_yang_didapat;

            $pengembalian = ModelPengembalianInvestasiFinlog::getTotalDikembalikan($idPengajuanInvestasiFinlog);
            $this->total_pokok_dikembalikan = $pengembalian->total_pokok ?? 0;
            $this->total_bagi_hasil_dikembalikan = $pengembalian->total_bagi_hasil ?? 0;
            $this->jumlah_transaksi = $pengembalian->jumlah_transaksi ?? 0;

            $this->calculatePenyaluranData($idPengajuanInvestasiFinlog);

            // Hitung bulan berjalan dari tanggal investasi
            $this->calculateBulanBerjalan();

            // Cek tanggal pengembalian terakhir
            $this->getTanggalPengembalianTerakhir($idPengajuanInvestasiFinlog);

            // Tentukan apakah bisa bayar bagi hasil dan pokok
            $this->checkPaymentEligibility();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memuat data kontrak: ' . $e->getMessage());
        }
    }

    /**
     * Hitung data penyaluran deposito
     */
    private function calculatePenyaluranData($idPengajuanInvestasiFinlog)
    {
        $penyaluran = \DB::table('penyaluran_deposito_sfinlog')
            ->where('id_pengajuan_investasi_finlog', $idPengajuanInvestasiFinlog)
            ->selectRaw('
                SUM(nominal_yang_disalurkan) as total_disalurkan,
                SUM(nominal_yang_dikembalikan) as total_dikembalikan
            ')
            ->first();

        $this->total_dana_disalurkan = floatval($penyaluran->total_disalurkan ?? 0);
        $this->total_dana_dikembalikan_penyaluran = floatval($penyaluran->total_dikembalikan ?? 0);

        // Sisa dana yang masih di perusahaan (belum dikembalikan dari penyaluran)
        $this->sisa_dana_di_perusahaan = $this->total_dana_disalurkan - $this->total_dana_dikembalikan_penyaluran;

        // Dana pokok yang tersedia untuk dikembalikan ke investor
        // = Nominal Investasi - Total Pokok Sudah Dikembalikan - Sisa Dana di Perusahaan
        $sisaPokokBelumDikembalikan = ($this->nominal_investasi ?? 0) - ($this->total_pokok_dikembalikan ?? 0);
        $this->dana_pokok_tersedia = max(0, $sisaPokokBelumDikembalikan - $this->sisa_dana_di_perusahaan);
    }

    /**
     * Hitung bulan berjalan dari tanggal investasi
     */
    private function calculateBulanBerjalan()
    {
        if (!$this->tanggal_investasi) {
            $this->bulan_berjalan = 0;
            return;
        }

        $tanggalInvestasi = Carbon::parse($this->tanggal_investasi)->startOfDay();
        $tanggalSekarang = Carbon::now()->startOfDay();

        // Hitung selisih bulan dengan lebih akurat
        // Bulan ke-1 adalah bulan dimana investasi dimulai
        $tahunInvestasi = $tanggalInvestasi->year;
        $bulanInvestasi = $tanggalInvestasi->month;
        $tahunSekarang = $tanggalSekarang->year;
        $bulanSekarang = $tanggalSekarang->month;

        // Hitung selisih bulan
        $selisihTahun = $tahunSekarang - $tahunInvestasi;
        $selisihBulan = $bulanSekarang - $bulanInvestasi;
        $totalBulan = ($selisihTahun * 12) + $selisihBulan;

        // Bulan berjalan = bulan ke-1 adalah bulan investasi
        $this->bulan_berjalan = max(1, $totalBulan + 1);

        // Jika sudah melewati lama investasi, set ke bulan terakhir
        if ($this->bulan_berjalan > $this->lama_investasi) {
            $this->bulan_berjalan = $this->lama_investasi;
        }
    }

    /**
     * Ambil tanggal pengembalian terakhir
     */
    private function getTanggalPengembalianTerakhir($idPengajuanInvestasiFinlog)
    {
        $pengembalianTerakhir = ModelPengembalianInvestasiFinlog::where('id_pengajuan_investasi_finlog', $idPengajuanInvestasiFinlog)
            ->orderBy('tanggal_pengembalian', 'desc')
            ->first();

        $this->tanggal_pengembalian_terakhir = $pengembalianTerakhir ? $pengembalianTerakhir->tanggal_pengembalian : null;
    }

    /**
     * Cek apakah bisa melakukan pembayaran berdasarkan aturan bisnis
     */
    private function checkPaymentEligibility()
    {
        // Reset flags
        $this->bisa_bayar_bagi_hasil = false;
        $this->bisa_bayar_pokok = false;
        $this->is_bulan_terakhir = false;
        $this->info_periode = '';

        if (!$this->tanggal_investasi || !$this->lama_investasi) {
            return;
        }

        // Cek apakah sudah bulan terakhir
        $this->is_bulan_terakhir = ($this->bulan_berjalan >= $this->lama_investasi);

        // Cek apakah sudah 2 bulan sejak pengembalian terakhir atau belum ada pengembalian
        $bisaBayarBerdasarkanPeriode = $this->checkPeriode2Bulan();

        // Aturan: Bagi hasil bisa dibayar di bulan ke-2, 4, 6, dst (bulan genap) atau bulan terakhir
        $bulanGenap = ($this->bulan_berjalan % 2 == 0);
        $bulanTerakhir = $this->is_bulan_terakhir;

        // Bagi hasil bisa dibayar jika:
        // 1. Bulan genap (2, 4, 6, dst) DAN sudah 2 bulan sejak pengembalian terakhir
        // 2. ATAU bulan terakhir (tidak perlu cek periode 2 bulan untuk bulan terakhir)
        // 3. DAN masih ada sisa bagi hasil yang belum dikembalikan
        $this->bisa_bayar_bagi_hasil = (($bulanGenap && $bisaBayarBerdasarkanPeriode) || $bulanTerakhir) && ($this->sisa_bagi_hasil_db > 0);

        // Pokok hanya bisa dibayar di bulan terakhir DAN bagi hasil sudah lunas (from DB)
        // Use sisa_bagi_hasil_db from database instead of calculating
        $this->bisa_bayar_pokok = $bulanTerakhir && ($this->sisa_bagi_hasil_db <= 0) && ($this->sisa_pokok_db > 0);

        // Set info periode
        if (!$bisaBayarBerdasarkanPeriode && !$bulanTerakhir) {
            $tanggalBerikutnya = $this->getTanggalPembayaranBerikutnya();
            $this->info_periode = 'Pembayaran berikutnya dapat dilakukan pada: ' . Carbon::parse($tanggalBerikutnya)->format('d F Y');
        } elseif ($bulanTerakhir) {
            $this->info_periode = 'Bulan terakhir investasi - Pokok dapat dibayarkan jika Bagi Hasil sudah lunas';
        } else {
            $this->info_periode = 'Periode pembayaran: Bulan ke-' . $this->bulan_berjalan;
        }
    }

    /**
     * Cek apakah sudah 2 bulan sejak pengembalian terakhir
     */
    private function checkPeriode2Bulan(): bool
    {
        // Jika belum ada pengembalian, bisa bayar di bulan ke-2
        if (!$this->tanggal_pengembalian_terakhir) {
            return $this->bulan_berjalan >= 2;
        }

        $tanggalTerakhir = Carbon::parse($this->tanggal_pengembalian_terakhir)->startOfDay();
        $tanggalSekarang = Carbon::now()->startOfDay();

        // Hitung selisih bulan dengan lebih akurat
        $tahunTerakhir = $tanggalTerakhir->year;
        $bulanTerakhir = $tanggalTerakhir->month;
        $tahunSekarang = $tanggalSekarang->year;
        $bulanSekarang = $tanggalSekarang->month;

        $selisihTahun = $tahunSekarang - $tahunTerakhir;
        $selisihBulan = $bulanSekarang - $bulanTerakhir;
        $totalBulan = ($selisihTahun * 12) + $selisihBulan;

        // Harus sudah 2 bulan atau lebih
        return $totalBulan >= 2;
    }

    /**
     * Hitung tanggal pembayaran berikutnya (2 bulan dari pengembalian terakhir)
     */
    private function getTanggalPembayaranBerikutnya(): string
    {
        if (!$this->tanggal_pengembalian_terakhir) {
            // Jika belum ada pengembalian, tanggal berikutnya adalah bulan ke-2 dari tanggal investasi
            $tanggalInvestasi = Carbon::parse($this->tanggal_investasi);
            return $tanggalInvestasi->copy()->addMonths(2)->format('Y-m-d');
        }

        $tanggalTerakhir = Carbon::parse($this->tanggal_pengembalian_terakhir);
        return $tanggalTerakhir->copy()->addMonths(2)->format('Y-m-d');
    }

    public function resetCalculatedFields()
    {
        $this->nominal_investasi = null;
        $this->lama_investasi = null;
        $this->bagi_hasil_total = null;
        $this->tanggal_investasi = null;
        $this->total_pokok_dikembalikan = 0;
        $this->total_bagi_hasil_dikembalikan = 0;
        $this->jumlah_transaksi = 0;
        $this->bulan_berjalan = 0;
        $this->is_bulan_terakhir = false;
        $this->tanggal_pengembalian_terakhir = null;
        $this->bisa_bayar_bagi_hasil = false;
        $this->bisa_bayar_pokok = false;
        $this->info_periode = '';

        // Reset penyaluran data
        $this->total_dana_disalurkan = 0;
        $this->total_dana_dikembalikan_penyaluran = 0;
        $this->sisa_dana_di_perusahaan = 0;
        $this->dana_pokok_tersedia = 0;

        // Reset sisa from database
        $this->sisa_pokok_db = 0;
        $this->sisa_bagi_hasil_db = 0;
    }

    public function resetForm()
    {
        $this->reset([
            'id_pengajuan_investasi_finlog',
            'dana_pokok_dibayar',
            'bagi_hasil_dibayar',
            'bukti_transfer',
        ]);

        $this->tanggal_pengembalian = date('Y-m-d');
        $this->resetCalculatedFields();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.sfinlog.pengembalian-investasi-sfinlog.index', [
            'pengajuanInvestasi' => $this->pengajuanInvestasi,
        ])->layout('layouts.app', [
                    'title' => 'Pengembalian Investasi - SFinlog'
                ]);
    }
}


