<?php

namespace App\Livewire\PenyaluranDeposito;

use Livewire\Component;
use App\Models\PengajuanInvestasi;
use App\Models\MasterDebiturDanInvestor;
use Livewire\WithFileUploads;
use App\Attributes\FieldInput;
use App\Attributes\ParameterIDRoute;
use Livewire\Attributes\Renderless;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\PenyaluranDepositoRequest;
use Illuminate\Support\Facades\DB;

class PenyaluranDepositoIndex extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads;

    private string $validateClass = PenyaluranDepositoRequest::class;

    #[ParameterIDRoute]
    public $id;

    #[FieldInput]
    public $id_pengajuan_investasi, $id_debitur, $nominal_yang_disalurkan, $tanggal_pengiriman_dana, $tanggal_pengembalian, $bukti_pengembalian;

    public function mount()
    {
        $this->setUrlSaveData('store_penyaluran_deposito', 'penyaluran-deposito.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_penyaluran_deposito', 'penyaluran-deposito.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    /**
     */
    public function getPengajuanInvestasiProperty()
    {
        return PengajuanInvestasi::query()
            ->withSisaDana()
            ->whereNotNull('pengajuan_investasi.nomor_kontrak')
            ->where('pengajuan_investasi.nomor_kontrak', '!=', '')
            ->orderBy('pengajuan_investasi.created_at', 'desc')
            ->get();
    }

    /**
     */
    public function getDebiturProperty()
    {
        return MasterDebiturDanInvestor::query()
            ->where('flagging', 'tidak')
            ->where('status', 'active')
            ->orderBy('nama', 'asc')
            ->get(['id_debitur', 'nama']);
    }

    public function render()
    {
        return view('livewire.penyaluran-deposito.index', [
            'pengajuanInvestasi' => $this->pengajuanInvestasi,
            'debitur' => $this->debitur,
        ])
            ->layout('layouts.app', [
                'title' => 'Aset Investasi'
            ]);
    }

    /**
     * Update nominal yang dikembalikan
     */
    public function updateNominalPengembalian($id, $nominal)
    {
        try {
            DB::beginTransaction();

            $penyaluran = \App\Models\PenyaluranDeposito::findOrFail($id);

            if ($nominal > $penyaluran->nominal_yang_disalurkan) {
                $this->dispatch('showAlert', type: 'error', message: 'Nominal yang dikembalikan tidak boleh lebih besar dari nominal yang disalurkan!');
                return;
            }

            // Validasi: nominal tidak boleh negatif
            if ($nominal < 0) {
                $this->dispatch('showAlert', type: 'error', message: 'Nominal yang dikembalikan tidak boleh negatif!');
                return;
            }

            // Ambil nominal lama untuk hitung selisih
            $nominalLama = $penyaluran->nominal_yang_dikembalikan ?? 0;
            $selisih = $nominal - $nominalLama;

            // Update nominal di penyaluran deposito
            $penyaluran->update([
                'nominal_yang_dikembalikan' => $nominal
            ]);

            // Update total_kembali_dari_penyaluran di pengajuan_investasi
            $pengajuan = \App\Models\PengajuanInvestasi::find($penyaluran->id_pengajuan_investasi);
            if ($pengajuan) {
                $totalKembaliLama = $pengajuan->total_kembali_dari_penyaluran ?? 0;
                $pengajuan->update([
                    'total_kembali_dari_penyaluran' => $totalKembaliLama + $selisih
                ]);
            }

            DB::commit();

            // Refresh table
            $this->dispatch('refreshPenyaluranDepositoTable');
            
            // Dispatch success
            $this->dispatch('pengembalian-success', message: 'Nominal pengembalian berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating nominal pengembalian: ' . $e->getMessage());
            $this->dispatch('showAlert', type: 'error', message: 'Terjadi kesalahan saat menyimpan data!');
        }
    }
}
