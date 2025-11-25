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

class PenyaluranDepositoIndex extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads;
    
    private string $validateClass = PenyaluranDepositoRequest::class;
    public $pengajuanInvestasi, $debitur;

    #[ParameterIDRoute]
    public $id;
    
    #[FieldInput]
    public $id_pengajuan_investasi, $id_debitur, $nominal_yang_disalurkan, $tanggal_pengiriman_dana, $tanggal_pengembalian, $bukti_pengembalian;

    public function mount()
    {
        $this->setUrlSaveData('store_penyaluran_deposito', 'penyaluran-deposito.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_penyaluran_deposito', 'penyaluran-deposito.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
        
        $this->pengajuanInvestasi = PengajuanInvestasi::with('investor')
            ->whereNotNull('nomor_kontrak')
            ->where('nomor_kontrak', '!=', '')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $this->debitur = MasterDebiturDanInvestor::where('flagging', 'tidak')
            ->where('status', 'active')
            ->get();
    }

    public function render()
    {
        return view('livewire.penyaluran-deposito.index')
        ->layout('layouts.app', [
            'title' => 'Penyaluran Deposito'
        ]);
    }
}
