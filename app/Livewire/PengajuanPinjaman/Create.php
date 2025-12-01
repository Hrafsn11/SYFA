<?php

namespace App\Livewire\PengajuanPinjaman;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Attributes\FieldInput;
use App\Enums\JenisPembiayaanEnum;
use App\Models\PengajuanPeminjaman;
use App\Attributes\ParameterIDRoute;
use App\Livewire\Traits\HasValidate;
use App\Models\MasterDebiturDanInvestor;
use App\Models\MasterSumberPendanaanEksternal;
use App\Http\Requests\PengajuanPinjamanRequest;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Livewire\PengajuanPinjaman\Event\HandleCreateEvents;
use App\Livewire\PengajuanPinjaman\FieldsInput\FieldInputCreate;
use App\Livewire\PengajuanPinjaman\Dispatch\HandleCreateDispatch;

class Create extends Component
{
    use HasUniversalFormAction, 
        HasValidate, 
        WithFileUploads, 
        HandleCreateEvents, // event handling
        HandleCreateDispatch, // dispatching
        FieldInputCreate; // list input fields
    private string $validateClass = PengajuanPinjamanRequest::class;

    #[ParameterIDRoute]
    public $id = null;

    public $nama_perusahaan;
    public $nama_bank;
    public $no_rekening;
    public $lampiran_sid_current;
    public $nilai_kol;
    // besides installment
    public $total_pinjaman;
    public $total_bagi_hasil;
    public $pembayaran_total;
    // installment
    public $nominal_pinjaman;
    public $pps_debit;
    public $pps_percentage;
    public $s_finance;
    public $total_pembayaran_installment;
    public $bayar_per_bulan;

    
    public $pengajuan = null;
    public $sumber_eksternal;
    public $title;
    public $invoice_financing_data = [];
    public $po_financing_data = [];
    public $installment_data = [];
    public $factoring_data = [];
    public $list_tenor_pembayaran;

    public function mount($id = null)
    {
        $this->id = $id;

        $this->setUrlSaveData('store', 'peminjaman.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update', 'peminjaman.update', ["id" => "id_placeholder", "callback" => "afterAction"]);

        $this->nama_perusahaan = auth()->user()->name;
        $this->sumber_eksternal = MasterSumberPendanaanEksternal::orderBy('nama_instansi')->get();
        $this->title = 'Menu Pengajuan Peminjaman - Draft';
        $this->list_tenor_pembayaran = [
            ['value' => '3', 'label' => '3 Bulan'],
            ['value' => '6', 'label' => '6 Bulan'],
            ['value' => '9', 'label' => '9 Bulan'],
            ['value' => '12', 'label' => '12 Bulan']
        ];
    }

    public function render()
    {
        $masterDebiturDanInvestor = MasterDebiturDanInvestor::where('email', auth()->user()->email)
            ->where('flagging', 'tidak')
            ->where('status', 'active')
            ->with('kol')
            ->first();
        
        $this->nama_bank = $masterDebiturDanInvestor->nama_bank;
        $this->no_rekening = $masterDebiturDanInvestor->no_rek;
        $this->nilai_kol = $masterDebiturDanInvestor->kol->kol;

        if ($this->id !== null) $this->edit();
        return view('livewire.pengajuan-pinjaman.create');
    }

    public function setterFormData()
    {
        foreach ($this->getUniversalFieldInputs() as $key => $value) {
            $this->form_data[$value] = $this->{$value};
        }

        if ($this->jenis_pembiayaan != JenisPembiayaanEnum::INSTALLMENT) {
            unset($this->form_data['tenor_pembayaran']);
        } else {
            unset($this->form_data['tanggal_pencairan']);
            unset($this->form_data['tanggal_pembayaran']);
        }
    }

    public function editInvoice($idx)
    {
        $data = [];

        $this->index_data_invoice = $idx;
        foreach ($this->form_data_invoice[$idx] as $key => $value) {
            if (in_array($key, [
                'dokumen_invoice_file', 
                'dokumen_kontrak_file', 
                'dokumen_so_file', 
                'dokumen_bast_file', 
                'dokumen_lainnnya_file'
            ])) continue;

            if (in_array($key, ['nilai_invoice', 'nilai_pinjaman', 'nilai_bagi_hasil'])) {
                $this->{$key} = rupiahFormatter($value);
            } else if (in_array($key, ['invoice_date', 'due_date'])) {
                $this->{$key} = parseCarbonDate($value)->format('d/m/Y');
                $data[$key] = $this->{$key};
            } else {
                $this->{$key} = $value;
            }
        }
        $this->dispatch('edit-invoice', $data);
    }

    protected function setAdditionalValidationData(): array
    {
        return [
            'jenis_pembiayaan' => $this->jenis_pembiayaan ?? null,
            'form_data_invoice' => $this->form_data_invoice ?? [],
        ];
    }

    private function edit()
    {
        $this->pengajuan = PengajuanPeminjaman::with(['debitur', 'instansi', 'buktiPeminjaman'])->findOrFail($this->id);
        $this->jenis_pembiayaan = $this->pengajuan->jenis_pembiayaan;

        $jenisPembiayaan = $this->jenis_pembiayaan;
        $this->{str_replace(' ', '_', strtolower($jenisPembiayaan)) . '_data'} = $this->pengajuan->buktiPeminjaman;
    }

    public function afterSave($payload)
    {
        if ($payload->error === false) {
            $this->redirect(route('peminjaman.index'));
        }
    }
}
