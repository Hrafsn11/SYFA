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
    public $total_pokok_dikembalikan = 0;
    public $total_bagi_hasil_dikembalikan = 0;
    public $jumlah_transaksi = 0;

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
            ])->findOrFail($idPengajuanInvestasiFinlog);

            $this->nominal_investasi   = $investasi->nominal_investasi;
            $this->lama_investasi      = $investasi->lama_investasi;
            $this->bagi_hasil_total    = $investasi->nominal_bagi_hasil_yang_didapat;

            $pengembalian = ModelPengembalianInvestasiFinlog::getTotalDikembalikan($idPengajuanInvestasiFinlog);
            $this->total_pokok_dikembalikan   = $pengembalian->total_pokok ?? 0;
            $this->total_bagi_hasil_dikembalikan = $pengembalian->total_bagi_hasil ?? 0;
            $this->jumlah_transaksi           = $pengembalian->jumlah_transaksi ?? 0;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memuat data kontrak: ' . $e->getMessage());
        }
    }

    public function resetCalculatedFields()
    {
        $this->nominal_investasi = null;
        $this->lama_investasi = null;
        $this->bagi_hasil_total = null;
        $this->total_pokok_dikembalikan = 0;
        $this->total_bagi_hasil_dikembalikan = 0;
        $this->jumlah_transaksi = 0;
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


