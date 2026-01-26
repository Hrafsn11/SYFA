<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Enums\BanksEnum;
use App\Models\MasterKol;
use Livewire\WithFileUploads;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasModal;
use Livewire\Attributes\Renderless;
use App\Attributes\ParameterIDRoute;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\DebiturDanInvestorRequest; 

class DebiturDanInvestor extends Component
{
    use HasUniversalFormAction, HasValidate, HasModal, WithFileUploads;
    private string $validateClass = DebiturDanInvestorRequest::class;
    public $kol, $banks;

    #[ParameterIDRoute]
    public $id; // untuk edit data
    
    #[FieldInput]
    public $nama, $kode_perusahaan, $email, $nama_bank, $deposito, $nama_ceo, $email_ceo, $nama_direktur_holding, $email_direktur_holding, $nama_komisaris, $email_komisaris, $alamat, $no_telepon, $no_rek, $npwp, $id_kol, $password, $password_confirmation, $flagging, $flagging_investor;

    #[FieldInput]
    #[Renderless]
    public $tanda_tangan;
    
    public function mount()
    {
        $this->setUrlSaveData('store_master_debitur_dan_investor', 'master-data.debitur-investor.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_master_debitur_dan_investor', 'master-data.debitur-investor.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
        $this->setUrlSaveData('status_master_debitur_dan_investor', 'master-data.debitur-investor.toggle-status', ["id" => "id_placeholder", "callback" => "afterAction"]);
        $this->setUrlSaveData('unlock_master_debitur_dan_investor', 'master-data.debitur-investor.unlock', ["id" => "id_placeholder", "callback" => "afterAction"]);

        $this->kol = MasterKol::orderBy('id_kol', 'asc')->get();
        $this->banks = BanksEnum::getConstants();
        $this->flagging = 'tidak';
    }

    public function render()
    {
        return view('livewire.master-data.debitur-dan-investor.index')
        ->layout('layouts.app', [
            'title' => 'Master Debitur dan Investor'
        ]);
    }

    public function setterFormData()
    {
        $listInput = $this->getUniversalFieldInputs();
        
        // For debitur: exclude investor-specific fields
        if ($this->flagging == 'tidak') {
            $listInput = array_filter($listInput, function ($value) {
                return !in_array($value, ['deposito', 'flagging_investor']);
            });
        } 
        // For investor: exclude debitur-specific fields
        else {
            $listInput = array_filter($listInput, function ($value) {
                return !in_array($value, ['nama_ceo', 'email_ceo', 'nama_direktur_holding', 'email_direktur_holding', 'nama_komisaris', 'email_komisaris', 'id_kol', 'npwp']);
            });
        }

        foreach (array_values($listInput) as $key => $value) {
            $this->form_data[$value] = $this->{$value};
        }
    }

    /**
     * Handle form submission
     */
    public function submit()
    {
        $routeName = $this->id 
            ? 'master-data.debitur-investor.update' 
            : 'master-data.debitur-investor.store';
        
        $params = $this->id 
            ? ['id' => $this->id, 'callback' => 'afterAction'] 
            : ['callback' => 'afterAction'];
        
        $this->saveData($routeName, $params);
    }

    /**
     * Reset form data when modal is closed
     */
    public function resetFormData()
    {
        $this->nama = null;
        $this->email = null;
        $this->nama_bank = null;
        $this->deposito = null;
        $this->nama_ceo = null;
        $this->email_ceo = null;
        $this->nama_direktur_holding = null;
        $this->email_direktur_holding = null;
        $this->nama_komisaris = null;
        $this->email_komisaris = null;
        $this->alamat = null;
        $this->no_telepon = null;
        $this->no_rek = null;
        $this->npwp = null;
        $this->id_kol = null;
        $this->password = null;
        $this->password_confirmation = null;
        $this->flagging = 'tidak';
        $this->flagging_investor = null;
        $this->tanda_tangan = null;
        $this->id = null;
        $this->form_data = [];
    }
}
