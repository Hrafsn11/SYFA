<?php

namespace App\Livewire\PengembalianPinjaman;

use Livewire\Component;
use App\Attributes\FieldInput;
use App\Models\PengajuanPeminjaman;
use App\Livewire\Traits\HasValidate;
use App\Models\MasterDebiturDanInvestor;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\PengembalianPinjamanRequest;

class Create extends Component
{
    use HasUniversalFormAction, HasValidate;
    private string $validateClass = PengembalianPinjamanRequest::class;
    
    #[FieldInput]
    public 
        $kode_peminjaman, 
        $total_pinjaman, 
        $total_bagi_hasil, 
        $tanggal_pencairan, 
        $lama_pemakaian, 
        $nominal_invoice, 
        $invoice_dibayarkan, 
        $bulan_pembayaran, 
        $yang_harus_dibayarkan, 
        $sisa_utang, 
        $sisa_bagi_hasil, 
        $catatan, 
        $pengembalian_invoices = [
            [
                'nominal' => null,
                'file' => null
            ]
        ];


    public $pengajuanPeminjaman, $namaPerusahaan;

    public function mount()
    {
        $this->debitur = MasterDebiturDanInvestor::where('user_id', auth()->user()->id)->first();
        $this->namaPerusahaan = $this->debitur->nama ?? auth()->user()->name;
        $this->pengajuanPeminjaman = isset($this->debitur)
            ? PengajuanPeminjaman::where('id_debitur', $this->debitur->id_debitur)
            ->where('status', 'Dana Sudah Dicairkan')
            ->select('id_pengajuan_peminjaman', 'nomor_peminjaman')
            ->get()
            : collect([]);
    }

    public function render()
    {
        return view('livewire.pengembalian-pinjaman.create')
            ->layout('layouts.app', [
                'title' => 'Tambah Pengembalian Pinjaman'
            ]);
    }

    public function getData()
    {

    }
}
