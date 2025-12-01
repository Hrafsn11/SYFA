<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PengajuanRestrukturisasi;
use Illuminate\Support\Facades\Storage;

class ValidasiPengajuanRestrukturisasi extends Component
{
    public $pengajuanId;
    public $pengajuan;
    public $debitur;
    
    public function mount($id)
    {
        $this->pengajuanId = $id;
        $this->loadData();
    }
    
    public function loadData()
    {
        $this->pengajuan = PengajuanRestrukturisasi::with(['debitur', 'peminjaman'])
            ->findOrFail($this->pengajuanId);
        
        $this->debitur = $this->pengajuan->debitur;
    }
    
    public function getDocumentUrl($fieldName)
    {
        if (!$this->pengajuan->$fieldName) {
            return null;
        }
        
        return Storage::disk('public')->url($this->pengajuan->$fieldName);
    }
    
    public function render()
    {
        return view('livewire.pengajuan-restrukturisasi.detail');
    }
}
