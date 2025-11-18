<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Enums\BanksEnum;
use App\Models\MasterKol;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\DebiturDanInvestorRequest;
use Livewire\Attributes\Renderless;
use Livewire\WithFileUploads;

class DebiturDanInvestor extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads;
    private string $validateClass = DebiturDanInvestorRequest::class;
    public $kol, $banks;
    
    #[FieldInput]
    public $nama, $email, $nama_bank, $deposito, $nama_ceo, $alamat, $no_telepon, $no_rek, $npwp, $id_kol, $password, $password_confirmation, $flagging;

    #[FieldInput]
    #[Renderless]
    public $tanda_tangan;
    
    public function mount()
    {
        $this->setUrlSaveData('store_master_debitur_dan_investor', 'master-data.debitur-investor.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_master_debitur_dan_investor', 'master-data.debitur-investor.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
        $this->setUrlSaveData('delete_master_debitur_dan_investor', 'master-data.debitur-investor.destroy', ["id" => "id_placeholder", "callback" => "afterAction"]);

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
        if ($this->flagging == 'tidak') {
            $listInput = array_filter($listInput, function ($value) {
                return $value !== 'deposito';
            });
        } else {
            $listInput = array_filter($listInput, function ($value) {
                return !in_array($value, ['nama_ceo', 'id_kol', 'npwp']);
            });
        }

        foreach (array_values($listInput) as $key => $value) {
            $this->form_data[$value] = $this->{$value};
        }
    }
}
