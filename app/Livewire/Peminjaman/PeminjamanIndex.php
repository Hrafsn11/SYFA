<?php

namespace App\Livewire\Peminjaman;

use Livewire\Component;

class PeminjamanIndex extends Component
{
    public $peminjaman_data = [];

    public function mount()
    {
        $this->peminjaman_data = [
            [
                'id' => 1,
                'nama_perusahaan' => 'PT Maju Jaya',
                'lampiran_sid' => 'sid_001.pdf',
                'nilai_kol' => 'A',
            ],
            [
                'id' => 2,
                'nama_perusahaan' => 'CV Sukses Makmur',
                'lampiran_sid' => 'sid_002.pdf',
                'nilai_kol' => 'B',
            ],
            [
                'id' => 3,
                'nama_perusahaan' => 'PT Sejahtera Abadi',
                'lampiran_sid' => 'sid_003.pdf',
                'nilai_kol' => 'D',
            ],
            [
                'id' => 4,
                'nama_perusahaan' => 'PT Teknologi Maju',
                'lampiran_sid' => 'sid_004.pdf',
                'nilai_kol' => 'A',
            ],
            [
                'id' => 5,
                'nama_perusahaan' => 'CV Digital Solution',
                'lampiran_sid' => 'sid_005.pdf',
                'nilai_kol' => 'C',
            ],
            [
                'id' => 6,
                'nama_perusahaan' => 'PT Pelabuhan Indonesia',
                'lampiran_sid' => 'sid_006.pdf',
                'nilai_kol' => 'A',
            ],
            [
                'id' => 7,
                'nama_perusahaan' => 'PT Angkasa Pura',
                'lampiran_sid' => 'sid_007.pdf',
                'nilai_kol' => 'B',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.peminjaman.index')
            ->layout('layouts.app');
    }
}