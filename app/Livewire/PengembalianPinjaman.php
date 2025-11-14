<?php

namespace App\Livewire;

use App\Attributes\FieldInput;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Livewire\Traits\HasValidate;
use App\Http\Requests\PengembalianPinjamanRequest;
use Livewire\Component;

class PengembalianPinjaman extends Component
{
    use HasUniversalFormAction, HasValidate;
    
    private string $validateClass = PengembalianPinjamanRequest::class;

    #[FieldInput]
    public $kode_peminjaman, $nama_perusahaan, $total_pinjaman, $total_bagi_hasil;
    
    #[FieldInput]
    public $tanggal_pencairan, $lama_pemakaian, $nominal_invoice, $invoice_dibayarkan;
    
    #[FieldInput]
    public $sisa_utang, $sisa_bagi_hasil, $catatan;

    public function mount()
    {
        $this->setUrlSaveData('store_pengembalian', 'pengembalian.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_pengembalian', 'pengembalian.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
        $this->setUrlSaveData('delete_pengembalian', 'pengembalian.destroy', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    public function render()
    {
        return view('livewire.pengembalian-pinjaman.index')
            ->layout('layouts.app', [
                'title' => 'Pengembalian Pinjaman'
            ]);
    }
}
