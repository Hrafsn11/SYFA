<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use App\Models\MasterDebiturDanInvestor;
use App\Models\CellsProject;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\SFinlog\PengajuanInvestasiFinlogRequest;
use Illuminate\Support\Facades\Auth;

class PengajuanInvestasiSFinlog extends Component
{    
    use HasUniversalFormAction, HasValidate;
    
    private string $validateClass = PengajuanInvestasiFinlogRequest::class;
    
    #[FieldInput]
    public $id_debitur_dan_investor, $id_cells_project, $nama_investor, $tanggal_investasi, $lama_investasi, $nominal_investasi, $persentase_bagi_hasil;
    
    public $projects = [];
    public $currentInvestor = null;

    public function mount() 
    {
        $this->currentInvestor = MasterDebiturDanInvestor::where('user_id', Auth::id())
                                            ->where('flagging', 'ya')
                                            ->first();
        
        $this->projects = CellsProject::orderBy('nama_cells_bisnis', 'asc')->get();

        $this->setUrlSaveData('store_pengajuan_investasi_finlog', 'sfinlog.pengajuan-investasi.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_pengajuan_investasi_finlog', 'sfinlog.pengajuan-investasi.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
        $this->setUrlSaveData('delete_pengajuan_investasi_finlog', 'sfinlog.pengajuan-investasi.destroy', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    public function render()
    {
        return view('livewire.sfinlog.pengajuan-investasi.index');
    }
}
