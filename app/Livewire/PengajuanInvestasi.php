<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasterDebiturDanInvestor;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\PengajuanInvestasiRequest;
use Illuminate\Support\Facades\Auth;

class PengajuanInvestasi extends Component
{    
    use HasUniversalFormAction, HasValidate;
    private string $validateClass = PengajuanInvestasiRequest::class;
    
    #[FieldInput]
    public $id_debitur_dan_investor, $nama_investor, $deposito, $tanggal_investasi, $lama_investasi, $jumlah_investasi, $bagi_hasil_pertahun;
    
    public $investors = [];
    public $currentInvestor = null;

    public function mount() 
    {
        // Get current logged in user's investor data if exists
        $this->currentInvestor = MasterDebiturDanInvestor::where('user_id', Auth::id())
                                            ->where('flagging', 'ya')
                                            ->first();
        
        // Get active investors for dropdown
        $this->investors = MasterDebiturDanInvestor::where('flagging', 'ya')
                                             ->where('status', 'Aktif')
                                             ->get();

        $this->setUrlSaveData('store_pengajuan_investasi', 'pengajuan-investasi.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_pengajuan_investasi', 'pengajuan-investasi.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
        $this->setUrlSaveData('delete_pengajuan_investasi', 'pengajuan-investasi.destroy', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    public function render()
    {
        return view('livewire.pengajuan-investasi.index');
    }
}
