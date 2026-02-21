<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PengajuanInvestasi;
use App\Models\PengembalianInvestasi as ModelsPengembalianInvestasi;
use Livewire\WithFileUploads;
use App\Attributes\FieldInput;
use App\Attributes\ParameterIDRoute;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\PengembalianInvestasiRequest;

class PengembalianInvestasi extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads;

    private string $validateClass = PengembalianInvestasiRequest::class;

    #[ParameterIDRoute]
    public $id;

    #[FieldInput]
    public $id_pengajuan_investasi, $dana_pokok_dibayar, $bunga_dibayar, $bukti_transfer, $tanggal_pengembalian;

    public $nominal_investasi;
    public $lama_investasi;
    public $bunga_total;
    public $sisa_pokok;
    public $sisa_bunga;
    public $dana_tersedia = 0;
    public $sisa_dana_di_perusahaan = 0;
    public $total_pokok_dikembalikan = 0;
    public $total_bunga_dikembalikan = 0;
    public $jumlah_transaksi = 0;
    public $history = [];

    public function mount()
    {
        $this->setUrlSaveData('store_pengembalian_investasi', 'pengembalian-investasi.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_pengembalian_investasi', 'pengembalian-investasi.update', ["id" => "id_placeholder", "callback" => "afterAction"]);

        $this->tanggal_pengembalian = date('Y-m-d');
    }


    public function getPengajuanInvestasiProperty()
    {
        return PengajuanInvestasi::query()
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->select([
                'id_pengajuan_investasi',
                'nomor_kontrak',
                'nama_investor',
                'jumlah_investasi',
                'lama_investasi',
                'nominal_bunga_yang_didapatkan',
                'status'
            ])
            ->orderBy('tanggal_investasi', 'desc')
            ->get();
    }

    /**
     * Load data kontrak 
     */
    public function loadDataKontrak($idPengajuanInvestasi)
    {
        try {
            $investasi = PengajuanInvestasi::select([
                'jumlah_investasi',
                'lama_investasi',
                'nominal_bunga_yang_didapatkan',
                'sisa_pokok',
                'sisa_bunga',
                'total_disalurkan',
                'total_kembali_dari_penyaluran'
            ])->findOrFail($idPengajuanInvestasi);

            $this->nominal_investasi = $investasi->jumlah_investasi;
            $this->lama_investasi = $investasi->lama_investasi;
            $this->bunga_total = $investasi->nominal_bunga_yang_didapatkan;

            // Calculate sisa dana di perusahaan first (using accessor)
            $this->sisa_dana_di_perusahaan = $investasi->sisa_dana_di_perusahaan;

            // Sisa pokok = yang belum dikembalikan ke investor (jangan dikurangi total disalurkan!)
            $this->sisa_pokok = floatval($investasi->sisa_pokok ?? 0);
            $this->sisa_bunga = $investasi->sisa_bunga;

            // Dana tersedia = what can actually be returned now
            $this->dana_tersedia = $investasi->dana_tersedia;

            $hasHistory = ($investasi->sisa_pokok < $this->nominal_investasi) ||
                ($investasi->sisa_bunga < $this->bunga_total);

            if ($hasHistory) {
                $pengembalian = ModelsPengembalianInvestasi::getTotalDikembalikan($idPengajuanInvestasi);
                $this->total_pokok_dikembalikan = $pengembalian->total_pokok ?? 0;
                $this->total_bunga_dikembalikan = $pengembalian->total_bagi_hasil ?? 0;
                $this->jumlah_transaksi = $pengembalian->jumlah_transaksi ?? 0;

                $this->history = [];
            } else {
                $this->total_pokok_dikembalikan = 0;
                $this->total_bagi_hasil_dikembalikan = 0;
                $this->jumlah_transaksi = 0;
                $this->history = [];
            }

            // Auto-set dana_pokok_dibayar ke 0 jika dana_tersedia sudah 0
            if ($this->dana_tersedia == 0) {
                $this->dana_pokok_dibayar = 0;
            }

            // Auto-set bunga_dibayar ke 0 jika sisa_bunga sudah 0
            if ($this->sisa_bunga == 0) {
                $this->bunga_dibayar = 0;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memuat data kontrak: ' . $e->getMessage());
        }
    }

    /**
     * Reset calculated fields
     */
    public function resetCalculatedFields()
    {
        $this->nominal_investasi = null;
        $this->lama_investasi = null;
        $this->bunga_total = null;
        $this->total_pokok_dikembalikan = 0;
        $this->total_bunga_dikembalikan = 0;
        $this->sisa_pokok = null;
        $this->sisa_bunga = null;
        $this->dana_tersedia = 0;
        $this->sisa_dana_di_perusahaan = 0;
        $this->jumlah_transaksi = 0;
        $this->history = [];
    }

    /**
     * Reset form
     */
    public function resetForm()
    {
        $this->reset([
            'id_pengajuan_investasi',
            'dana_pokok_dibayar',
            'bunga_dibayar',
            'bukti_transfer',
        ]);
        $this->tanggal_pengembalian = date('Y-m-d');
        $this->resetCalculatedFields();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Render view
     */
    public function render()
    {
        return view('livewire.pengembalian-investasi.index', [
            'pengajuanInvestasi' => $this->pengajuanInvestasi,
        ])
            ->layout('layouts.app', [
                'title' => 'Pengembalian Investasi'
            ]);
    }
}
